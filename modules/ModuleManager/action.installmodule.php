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
# This project's homepage is: http://www.cmsmadesimple.org
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
$module_name = get_parameter_value($params,'name');
$module_version  = get_parameter_value($params,'version');
$module_filename  = get_parameter_value($params,'filename');
if( $module_name == '' || $module_version == '' || $module_filename == '' ) {
  $this->SetError($this->Lang('error_missingparams'));
  $this->RedirectToAdminTab();
}

if( isset($params['cancel']) ) {
  $this->SetMessage($this->Lang('msg_cancelled'));
  $this->RedirectToAdminTab();
}

try {
  $ops = ModuleOperations::get_instance();
  $alldeps = array();
  $resolve_deps = function($module_name,$module_version,&$alldeps,$depth = 0) use (&$resolve_deps) {
    list($res,$deps) = modulerep_client::get_module_dependencies($module_name,$module_version);

    if( is_array($deps) && count($deps) ) {
      foreach( $deps as $row ) {
	$resolve_deps($row['name'],$row['version'],$alldeps,$depth + 1);

	// filter out the duplicates (by greater version number)
	if( !isset($alldeps[$row['name']]) ) {
	  $alldeps[$row['name']] = $row['version'];
	}
	else {
	  if( version_compare($alldeps[$row['name']],$row['version']) < 0 ) {
	    $alldeps[$row['name']] = $row['version'];
	  }
	}
      }
    }
  };

  // recursively (depth first) get the dependencies for the module+version we specified.
  $resolve_deps($module_name,$module_version,$alldeps);

  $needmodules = array();
  if( is_array($alldeps) && count($alldeps) ) {
    if( $this->GetPreference('latestdepends') ) {
      // get the latest version of dependency (but not necessarily of the module we're installing)
      $newest = modulerep_client::get_modulelatest(array_keys($alldeps));
      if( is_array($newest) && count($newest) ) {
	$newinfo = array();
	foreach( $alldeps as $name => &$ver ) {
	  $fnd = FALSE;
	  foreach( $newest as $rec ) {
	    if( $rec['name'] == $name && version_compare($ver,$rec['version']) < 0 ) {
	      $fnd = TRUE;
	      $needmodules[$name] = $rec;
	      break;
	    }
	  }
	  if( !$fnd && $ops->IsSystemModule($name) ) throw new CmsLogicException($this->Lang('error_dependencynotfound').': '.$name);
	}
      }
    }
  }

  // remove items that are already installed (where installed version is greater or equal)
  $allmoduleinfo = ModuleManagerModuleInfo::get_all_module_info(FALSE);
  $installmods = array();
  foreach( $alldeps as $name => $ver ) {
    if( !isset($allmoduleinfo[$name]) ) {
      // install
      $installmods[$name] = array('name'=>$name,'version'=>$ver,'action'=>'i');
    }
    else if( version_compare($allmoduleinfo[$name]['version'],$ver) < 0 ) {
      // upgrade
      $installmods[$name] = array('name'=>$name,'version'=>$ver,'action'=>'u');
    }
    else if( !$allmoduleinfo[$name]['active'] ) {
      $installmods[$name] = array('name'=>$name,'version'=>$ver,'action'=>'a');
    }
  }

  // get info (filename, version, size, description, and md5sum for all modules we need to install)
  //  $res = modulerep_client::get_multiple_moduleinfo($installmods);
  //  debug_display($res);
  //  die('test1');

  $smarty->assign('return_url',$this->create_url($id,'defaultadmin',$returnid, array('__activetab'=>'modules')));
  $smarty->assign('form_start',$this->CreateFormStart($id, 'installmodule', $returnid).
		  $this->CreateInputHidden($id,'modlist',base64_encode(serialize($installmods))));
  $smarty->assign('formend',$this->CreateFormEnd());
  $smarty->assign('module_name',$module_name);
  $smarty->assign('module_version',$module_version);

  if (count($installmods) > 0) {
    $smarty->assign('dependencies',$installmods);

    echo $this->ProcessTemplate('installinfo.tpl');
    return;
  }

  // only one module.
  $keys = array_keys($deps);
  $key = $keys[0];
  modmgr_utils::install_module($deps[$key],($deps[$key]['status'] == 'u')?1:0);

  // todo, display install results.
}
catch( Exception $e ) {
  $this->SetError($e->GetMessage());
  die($e->GetMessage());
  $this->RedirectToAdminTab();
}
#
# EOF
#
?>