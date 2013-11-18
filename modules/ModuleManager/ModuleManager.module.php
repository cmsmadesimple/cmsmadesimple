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

define('MINIMUM_REPOSITORY_VERSION','1.5');

class ModuleManager extends CMSModule
{
  const _dflt_request_url = 'http://www.cmsmadesimple.org/ModuleRepository/request/v2/';

  function GetName() { return 'ModuleManager'; }
  function GetFriendlyName() { return $this->Lang('friendlyname'); }
  function GetVersion() { return '1.10'; }
  function GetHelp() { return $this->Lang('help'); }
  function GetAuthor() { return 'calguy1000'; }
  function GetAuthorEmail() { return 'calguy1000@hotmail.com'; }
  function GetChangeLog() { return file_get_contents(dirname(__FILE__).'/changelog.inc'); }
  function IsPluginModule() { return false; }
  function HasAdmin() { return true; }
  function IsAdminOnly() { return true; }
  function GetAdminSection() { return 'siteadmin'; }
  function GetAdminDescription() { return $this->Lang('admindescription'); }
  function LazyLoadAdmin() { return TRUE; }
  function MinimumCMSVersion() { return "1.99-alpha0"; }
  function InstallPostMessage() { return $this->Lang('postinstall'); }
  function UninstallPostMessage() { return $this->Lang('postuninstall'); }
  function UninstallPreMessage() { return $this->Lang('really_uninstall'); }
  function VisibleToAdminUser() { return ($this->CheckPermission('Modify Site Preferences') || $this->CheckPermission('Modify Modules')); }

  protected function _DisplayErrorPage($id, &$params, $returnid, $message='')
  {
    $this->smarty->assign('title_error', $this->Lang('error'));
    $this->smarty->assign_by_ref('message', $message);
    $this->smarty->assign('link_back',$this->CreateLink($id,'defaultadmin',$returnid, $this->Lang('back_to_module_manager')));	

    // Display the populated template
    echo $this->ProcessTemplate('error.tpl');
  }
	

  function Install()
  {
    $this->SetPreference('module_repository',ModuleManager::_dflt_request_url);
  }


  /*---------------------------------------------------------
   Upgrade()
   ---------------------------------------------------------*/
  function Upgrade($oldversion, $newversion)
  {
    $this->SetPreference('module_repository',ModuleManager::_dflt_request_url);
  }

	
  /*---------------------------------------------------------
   DoAction($action, $id, $params, $returnid)
   ---------------------------------------------------------*/
  function DoAction($action, $id, $params, $returnid=-1)
  {
    @set_time_limit(9999);
    switch ($action) {
    case 'recurseinstall':
      die('call installmodule action');
      break;

    // fallback through to call the action.xxxx.php file
    default:
      parent::DoAction( $action, $id, $params, $returnid );
      break;
    }
  }

} // end of class

#
# EOF
#
?>