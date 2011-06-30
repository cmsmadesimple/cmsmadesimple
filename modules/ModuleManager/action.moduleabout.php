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

if( !isset( $params['name'] ) )
  {
    $this->_DisplayErrorPage( $id, $params, $returnid,
			      $this->Lang('error_insufficientparams'));
    return;
  }
$name = $params['name'];

if( !isset( $params['version'] ) )
  {
    $this->_DisplayErrorPage( $id, $params, $returnid,
			      $this->Lang('error_insufficientparams'));
    return;
  }
$version = $params['version'];

$url = $this->GetPreference('module_repository');
if( $url == '' )
  {
    $this->_DisplayErrorPage( $id, $params, $returnid,
			      $this->Lang('error_norepositoryurl'));
    return;
  }
$url .= '/moduleabout';

if( !isset($params['filename'] ) )
  {
    $this->_DisplayErrorPage( $id, $params, $returnid,
			      $this->Lang('error_nofilename'));
    return;
  }
$xmlfile = $params['filename'];

$req = new cms_http_request();
$req->execute($url,'','POST',array('name'=>$xmlfile));
$status = $req->getStatus();
$result = $req->getResult();
if( $status != 200 || $result == '' )
  {
    $this->_DisplayErrorPage( $id, $params, $returnid, $this->Lang('error_request_problem'));
    return;
  }
$about = json_decode($result,true);
if( $about[0] == false )
  {
    $this->_DisplayErrorPage( $id, $params, $returnid,
			      $about[1] );
    return;
  }

$this->smarty->assign('title',$this->Lang('abouttxt'));
$this->smarty->assign('moduletext',$this->Lang('nametext'));
$this->smarty->assign('vertext',$this->Lang('vertext'));
$this->smarty->assign('xmltext',$this->Lang('xmltext'));
$this->smarty->assign('modulename',$name);
$this->smarty->assign('moduleversion',$version);
$this->smarty->assign('xmlfile',$xmlfile);
$this->smarty->assign('content',$about[1]);
$this->smarty->assign('link_back',$this->CreateLink($id,'defaultadmin',$returnid, $this->Lang('back_to_module_manager')));
echo $this->ProcessTemplate('remotecontent.tpl');

#
# EOF
#
?>