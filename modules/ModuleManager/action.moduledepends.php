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
$depends_str = isset($data['depends']) ? $data['depends'] : '';
$depends = array();
if( $depends_str && $depends_str != 'a:0:{}' ) {
  $depends = json_decode($depends_str, true);
  if( !is_array($depends) ) $depends = array();
}

$tpl = $smarty->CreateTemplate($this->GetTemplateResource('remotecontent.tpl'), null, null, $smarty);
$tpl->assign('title',$this->Lang('dependstxt'));
$tpl->assign('moduletext',$this->Lang('nametext'));
$tpl->assign('vertext',$this->Lang('vertext'));
$tpl->assign('xmltext',$this->Lang('xmltext'));
$tpl->assign('modulename',$name);
$tpl->assign('moduleversion',$version);
$tpl->assign('xmlfile',$xmlfile);
$tpl->assign('back_url',$this->create_url($id,'defaultadmin',$returnid));
$tpl->assign('link_back',$this->CreateLink($id,'defaultadmin',$returnid, $this->Lang('back_to_module_manager')));	

$txt = '';
if( is_array($depends) && count($depends) > 0 ) {
  $txt = '<ul>';
  foreach( $depends as $one ) {
    $txt .= '<li>'.$one['name'].' => '.$one['version'].'</li>';
  }
  $txt .= '</ul>';
}
else {
  $txt = $this->Lang('msg_nodependencies');
}
$tpl->assign('content',$txt);
$tpl->display();

#
# EOF
#
?>