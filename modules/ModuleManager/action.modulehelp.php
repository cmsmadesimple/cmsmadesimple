<?php
#---------------------------------------------------------------------------
# CMS Made Simple - Power for the professional, Simplicity for the end user.
# (c) 2004 - 2011 by Ted Kulp
# (c) 2011 - 2018 by the CMS Made Simple Development Team
# (c) 2018 and beyond by the CMS Made Simple Foundation
# This project's homepage is: https://www.cmsmadesimple.org
#---------------------------------------------------------------------------
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
# Or read it online: http://www.gnu.org/licenses/licenses.html#GPL
#---------------------------------------------------------------------------

if (!isset($gCms)) exit;

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

$url = $this->GetPreference('module_repository');
if( !$url ) {
  $this->SetError($this->Lang('error_norepositoryurl'));
  $this->RedirectToAdminTab();
  return;
}
$url .= '/modulehelp';

$xmlfile = get_parameter_value($params,'filename');
if( !$xmlfile ) {
  $this->SetError($this->Lang('error_nofilename'));
  $this->RedirectToAdminTab();
  return;
}


$req = new modmgr_cached_request();
$req->execute($url,array('name'=>$xmlfile));
$status = $req->getStatus();
$result = $req->getResult();
if( $status != 200 || $result == '' ) {
  $this->SetError($this->Lang('error_request_problem'));
  $this->RedirectToAdminTab();
  return;
}
$help = json_decode($result,true);
if( !$help ) {
  $this->SetError($this->Lang('error_nodata'));
  $this->RedirectToAdminTab();
  return;
}

$smarty->assign('title',$this->Lang('helptxt'));
$smarty->assign('moduletext',$this->Lang('nametext'));
$smarty->assign('vertext',$this->Lang('vertext'));
$smarty->assign('xmltext',$this->Lang('xmltext'));
$smarty->assign('modulename',$name);
$smarty->assign('moduleversion',$version);
$smarty->assign('xmlfile',$xmlfile);
$smarty->assign('content',$help);
$smarty->assign('back_url',$this->create_url($id,'defaultadmin',$returnid));
$smarty->assign('link_back',$this->CreateLink($id,'defaultadmin',$returnid, $this->Lang('back_to_module_manager')));	

echo $this->ProcessTemplate('remotecontent.tpl');

#
# EOF
#