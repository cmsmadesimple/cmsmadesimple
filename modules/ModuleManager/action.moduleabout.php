<?php
#--------------------------------------------------
# See DOCS/LICENSE for full license information.
#--------------------------------------------------
if (!defined('CMS_VERSION')) exit;

$this->SetCurrentTab('modules');

$name = get_parameter_value($params,'name');
if( !$name ) {
  $this->SetError($this->Lang('error_insufficientparams'));
  $this->RedirectToAdminTab();
  return;
}

$version = get_parameter_value($params,'version');
if( !$version ) {
  $this->SetError($this->Lang('error_insufficientparams'));
  $this->RedirectToAdminTab();
  return;
}

$xmlfile = get_parameter_value($params,'filename');
if( !$xmlfile ) {
  $this->SetError($this->Lang('error_nofilename'));
  $this->RedirectToAdminTab();
  return;
}

$req = new modmgr_cached_request();
$req->execute('https://cdn.cmsmadesimple.org/repository/cache/module_' . urlencode($xmlfile) . '.json');
$status = $req->getStatus();
$result = $req->getResult();
if( $status != 200 || $result == '' ) {
  $this->SetError($this->Lang('error_request_problem'));
  $this->RedirectToAdminTab();
  return;
}
$data = json_decode($result,true);
$about = isset($data['about']) ? base64_decode($data['about']) : '';
if( !$about ) {
  $this->SetError($this->Lang('error_nodata'));
  $this->RedirectToAdminTab();
  return;
}

$tpl = $smarty->CreateTemplate($this->GetTemplateResource('remotecontent.tpl'), null, null, $smarty);
$tpl->assign('title',$this->Lang('abouttxt'));
$tpl->assign('moduletext',$this->Lang('nametext'));
$tpl->assign('vertext',$this->Lang('vertext'));
$tpl->assign('xmltext',$this->Lang('xmltext'));
$tpl->assign('modulename',$name);
$tpl->assign('moduleversion',$version);
$tpl->assign('xmlfile',$xmlfile);
$tpl->assign('content',$about);
$tpl->assign('back_url',$this->create_url($id,'defaultadmin',$returnid));
$tpl->assign('link_back',$this->CreateLink($id,'defaultadmin',$returnid, $this->Lang('back_to_module_manager')));
$tpl->display();

#
# EOF
#
?>