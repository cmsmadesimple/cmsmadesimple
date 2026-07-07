<?php
#--------------------------------------------------
# See DOCS/LICENSE for full license information.
#--------------------------------------------------
if (!defined('CMS_VERSION')) exit;

define('MINIMUM_REPOSITORY_VERSION','1.5');

class ModuleManager extends CMSModule
{
  const _dflt_request_url = 'https://api.cmsmadesimple.org/ModuleRepository/request/v2/';
  const MANAGE_PERM = 'Modify Modules';
  const PREFS_PERM = 'Modify Site Preferences';

  function GetName() { return get_class($this); }
  function GetFriendlyName() { return $this->Lang('friendlyname'); }
  function GetVersion() { return '2.2.0'; }
  function GetHelp() { return $this->Lang('help'); }
  function GetAuthor() { return 'cmsmsfoundation'; }
  function GetAuthorEmail() { return 'foundation@cmsmadesimple.org'; }
  function GetChangeLog() { return file_get_contents(dirname(__FILE__).'/docs/changelog.inc'); }
  function IsPluginModule() { return FALSE; }
  function HasAdmin() { return TRUE; }
  function IsAdminOnly() { return TRUE; }
  function GetAdminSection() { return 'siteadmin'; }
  function GetAdminDescription() { return $this->Lang('admindescription'); }
  function LazyLoadAdmin() { return TRUE; }
  function MinimumCMSVersion() { return '2.2.3'; }
  function GetDependencies() { return []; }
  function InstallPostMessage() { return $this->Lang('postinstall'); }
  function UninstallPostMessage() { return $this->Lang('postuninstall'); }
  function UninstallPreMessage() { return $this->Lang('really_uninstall'); }
  function VisibleToAdminUser() { return ($this->CheckPermission(self::PREFS_PERM) || $this->CheckPermission(self::MANAGE_PERM)); }

  protected function _DisplayErrorPage($id, &$params, $returnid, $message='')
  {
    $smarty = cmsms()->GetSmarty();
    $tpl = $smarty->CreateTemplate($this->GetTemplateResource('error.tpl'), null, null, $smarty);
    $tpl->assign('title_error', $this->Lang('error'));
    $tpl->assign('message', $message);
    $tpl->assign('link_back',$this->CreateLink($id,'defaultadmin',$returnid, $this->Lang('back_to_module_manager')));
    $tpl->display();
  }

  function Install()
  {
    $this->SetPreference('module_repository',ModuleManager::_dflt_request_url);
  }

  function Upgrade($oldversion, $newversion)
  {
    $this->SetPreference('module_repository',ModuleManager::_dflt_request_url);
  }

  function DoAction($action, $id, $params, $returnid=-1)
  {
    $smarty = cmsms()->GetSmarty();
    $smarty->assign($this->GetName(), $this);
    $smarty->assign('mod', $this);
    set_time_limit(9999);
    parent::DoAction( $action, $id, $params, $returnid );
  }

} // end of class

#
# EOF
#
?>
