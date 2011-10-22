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

$active_tab = 'modules';
if( isset($params['active_tab']) )
  {
    $active_tab = $params['active_tab'];
  }
$_SESSION[$this->GetName()]['active_tab'] = $active_tab;

$allmods = modulerep_client::get_repository_modules('',0);
$deps = array(array('name'=>$params['name'],'version'=>$params['version'],'filename'=>$params['filename'],
		    'by'=>'','size'=>$params['size']));
if (! $allmods[0])
  {
    $this->_DisplayErrorPage( $id, $params, $returnid, $allmods[1] );
    return;
  }
$ret = modmgr_utils::add_dependencies_to_list($params['filename'],$allmods,$deps);
if (!$ret[0])
  {
    $this->_DisplayErrorPage( $id, $params, $returnid, $ret[1]);
    return;
  }
// de-dupe list
$deps = modmgr_utils::remove_duplicate_dependencies($deps);
modmgr_utils::find_unfulfilled_dependencies($deps);
$tmp = array();
foreach( $deps as $onedep )
{
  if ($onedep['status'] != 's') 
    {
      $tmp[] = $onedep;
    }
}
$deps = $tmp;

$smarty->assign('link_back',$this->CreateLink($id,'defaultadmin',$returnid, $this->Lang('back_to_module_manager')));	
		
if( count($deps) == 0 )
  {
    // nothing to upgrade?
    $_SESSION[$this->GetName()]['tab_message'] = $this->Lang('error_noupgrade');
    $this->Redirect($id,'defaultadmin');
  }

$smarty->assign('time_warning',$this->Lang('time_warning'));
if (count($deps) > 1)
  {
    $smarty->assign('installmodule',modmgr_utils::file_to_module_name($allmods[1],$params['filename']));
    $smarty->assign('dependencies',$deps);

    $smarty->assign('mod',$this);
    $smarty->assign('form_start',$this->CreateFormStart($id, 'doinstall', $returnid).
			  $this->CreateInputHidden($id,'modlist',base64_encode(serialize($deps))));
    $smarty->assign('submit', $this->CreateInputSubmit($id, 'submit', $this->Lang('install_submit')));
    $smarty->assign('cancel', $this->CreateInputSubmit($id, 'cancel', lang('cancel')));
    $smarty->assign('formend',$this->CreateFormEnd());
    echo $this->ProcessTemplate('installinfo.tpl');
    return;
  }

// only one module.
$keys = array_keys($deps);
$key = $keys[0];
$smarty->assign('mod',$this);
$res = modmgr_utils::install_module($deps[$key],($deps[$key]['status'] == 'u')?1:0);
if( is_array($res) && $res[0] == TRUE )
  {
    $this->Redirect($id,'display_install_results');
  }

// some kind of error occurred.
if( !is_array($res) )
  {
    $smarty->assign('message',$this->ShowErrors($this->Lang('error_internal')));
  }
else if( $res[0] == FALSE )
  {
    $smarty->assign('message',$this->ShowErrors($res[1]));
  }
echo $this->ProcessTemplate('installinfo.tpl');

#
# EOF
#
?>
