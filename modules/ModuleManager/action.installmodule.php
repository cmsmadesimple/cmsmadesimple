<?php
#BEGIN_LICENSE
#-------------------------------------------------------------------------
# Module: ModuleManager (c) 2008 by Robert Campbell 
#         (calguy1000@cmsmadesimple.org)
#  An addon module for CMS Made Simple to allow browsing remotely stored
#  modules, viewing information about them, and downloading or upgrading
# 
#-------------------------------------------------------------------------
# CMS - CMS Made Simple is (c) 2005 by Ted Kulp (wishy@cmsmadesimple.org)
# Visit our homepage at: http://www.cmsmadesimple.org
#
#-------------------------------------------------------------------------
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# However, as a special exception to the GPL, this software is distributed
# as an addon module to CMS Made Simple.  You may not use this software
# in any Non GPL version of CMS Made simple, or in any version of CMS
# Made simple that does not indicate clearly and obviously in its admin 
# section that the site was built with CMS Made simple.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
# Or read it online: http://www.gnu.org/licenses/licenses.html#GPL
#
#-------------------------------------------------------------------------
#END_LICENSE
if (!isset($gCms)) exit;
if( !$this->CheckPermission('Modify Modules') ) return;
$this->SetCurrentTab('modules');

if( isset($params['cancel']) ) {
  $this->SetMessage($this->Lang('msg_cancelled'));
  $this->RedirectToAdminTab();
}

try {
  $module_name = get_parameter_value($params,'name');
  $module_version  = get_parameter_value($params,'version');
  $module_filename  = get_parameter_value($params,'filename');
  $module_size = get_parameter_value($params,'size');
  if( $module_name == '' || $module_version == '' || $module_filename == '' || $module_size < 100 ) {
    throw new CmsInvalidDataException( $this->Lang('error_missingparams') );
  }

  if( isset($params['submit']) ) {
    set_time_limit(9999);
    if( isset($params['modlist']) && $params['modlist'] != '' ) {
      $modlist = unserialize(base64_decode($params['modlist']));
      if( !is_array($modlist) || count($modlist) == 0 ) throw new CmsInvalidDataException( $this->Lang('error_missingparams') );

      // cache all of the xml files first... make sure we can download everything, and that it gets cached.
      foreach( $modlist as $key => $rec ) {
	if( $rec['action'] != 'i' && $rec['action'] != 'u' ) continue;
	if( !isset($rec['filename']) ) throw new CmsInvalidDataException( $this->Lang('error_missingparams') );
	if( !isset($rec['size']) ) throw new CmsInvalidDataException( $this->Lang('error_missingparams') );
	$filename = modmgr_utils::get_module_xml($rec['filename'],$rec['size']);
      }

      // expand all of the xml files.
      $ops = cmsms()->GetModuleOperations();
      foreach( $modlist as $key => $rec ) {
	if( $rec['action'] != 'i' && $rec['action'] != 'u' ) continue;
	$xml_filename = modmgr_utils::get_module_xml($rec['filename'],$rec['size'],(isset($rec['md5sum']))?$rec['md5sum']:'');
	$res = $ops->ExpandXMLPackage( $xml_filename, 1 );
	$ops->QueueForInstall($key);
      }

      foreach( $modlist as $name => $rec ) {

	switch( $rec['action'] ) {
	case 'a': // activate
	  $ops->ActivateModule($name);
	  break;
	}
      }

      // done, rest will be done when the module is loaded.
      $this->RedirectToAdminTab();
    }
  }

  $resolve_deps = function($module_name,$module_version,&$alldeps,$depth = 0) use (&$resolve_deps) {
    list($res,$deps) = modulerep_client::get_module_dependencies($module_name,$module_version);

    if( is_array($deps) && count($deps) ) {
      foreach( $deps as $row ) {
	$resolve_deps($row['name'],$row['version'],$alldeps,$depth + 1);

	// filter out the duplicates (by greater version number)
	if( !isset($alldeps[$row['name']]) ) {
	  $alldeps[$row['name']] = $row;
	}
	else {
	  if( version_compare($alldeps[$row['name']]['version'],$row['version']) < 0 ) $alldeps[$row['name']] = $row;
	}
      }
    }
  };

  // recursively (depth first) get the dependencies for the module+version we specified.
  $alldeps = array();
  $resolve_deps($module_name,$module_version,$alldeps);

  // get information for all dependencies, and make sure that they are all there.
  if( is_array($alldeps) && count($alldeps) ) {
    $res = null;
    if( $this->GetPreference('latestdepends') ) {
      // get the latest version of dependency (but not necessarily of the module we're installing)
      $res = modulerep_client::get_modulelatest(array_keys($alldeps));
    }
    else {
      // get the info for all dependencies
      $res = modulerep_client::get_multiple_moduleinfo($alldeps);
    }

    foreach( $alldeps as $name => $row ) {
      $fnd = FALSE;
      $tmp = null;
      foreach( $res as $rec ) {
	if( $rec['name'] != $name ) continue;
	$tmp = version_compare($row['version'],$rec['version']);
	if( $tmp <= 0 ) {
	  $fnd = TRUE;
	  $alldeps[$name] = $rec;
	  break;
	}
      }
    }
  }

  // add our current item into alldeps.
  $alldeps[$module_name] = array('name'=>$module_name,'version'=>$module_version,'filename'=>$module_filename,'size'=>$module_size);

  // remove items that are already installed (where installed version is greater or equal)
  // and create actions as to what we're going to do.
  if( count($alldeps) ) {
    $allmoduleinfo = ModuleManagerModuleInfo::get_all_module_info(FALSE);
    foreach( $alldeps as $name => &$rec ) {
      if( !isset($allmoduleinfo[$name]) ) {
	// install
	$rec['action']='i';
      }
      else if( version_compare($allmoduleinfo[$name]['version'],$rec['version']) < 0 ) {
	// upgrade
	$rec['action']='u';
      }
      else if( !$allmoduleinfo[$name]['active'] ) {
	// activate
	$rec['action']='a';
      }
      else {
	// already installed, do nothing.
	unset($alldeps[$name]);
      }
    }
  }

  // test to make sure we have the required info for each record.
  foreach( $alldeps as $mname => &$rec ) {
    if( $rec['action'] == 'a' ) continue; // if just activating we don't have to worry.
    if( !isset($rec['filename']) ) throw new CmsInvalidDataException( $this->Lang('error_missingmoduleinfo',$mname) );
    if( !isset($rec['version']) ) throw new CmsInvalidDataException( $this->Lang('error_missingmoduleinfo',$mname) );
    if( !isset($rec['size']) ) throw new CmsInvalidDataException( $this->Lang('error_missingmoduleinfo',$mname.' '.$rec['version']) );
  }
  
  // here, if alldeps is empty... we have nothing to do.
  $smarty->assign('return_url',$this->create_url($id,'defaultadmin',$returnid, array('__activetab'=>'modules')));
  $parms = array('name'=>$module_name,'version'=>$module_version,'filename'=>$module_filename,'size'=>$module_size);
  $smarty->assign('form_start',$this->CreateFormStart($id, 'installmodule', $returnid, 'post', '', FALSE, '', $parms).
		  $this->CreateInputHidden($id,'modlist',base64_encode(serialize($alldeps))));
  $smarty->assign('formend',$this->CreateFormEnd());
  $smarty->assign('module_name',$module_name);
  $smarty->assign('module_version',$module_version);
  $tmp = array_keys($alldeps);
  $n = count($tmp) - 1;
  $key = $tmp[$n];
  $action = $alldeps[$key]['action'];
  $smarty->assign('is_upgrade',($action == 'u')?1:0);

  $smarty->assign('dependencies',$alldeps);
  echo $this->ProcessTemplate('installinfo.tpl');
  return;
}
catch( Exception $e ) {
  $this->SetError($e->GetMessage());
  $this->RedirectToAdminTab();
}
#
# EOF
#
?>