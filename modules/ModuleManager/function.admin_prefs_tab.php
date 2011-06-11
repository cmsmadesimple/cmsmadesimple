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
if( !$this->CheckPermission('Modify Site Preferences') ) exit;

$smarty->assign('formstart',$this->CreateFormStart( $id, 'setprefs', $returnid ));
$smarty->assign('formend',$this->CreateFormEnd());
$smarty->assign('prompt_url',$this->Lang('prompt_repository_url'));
$smarty->assign('input_url',$this->CreateInputText($id, 'url', 
							 $this->GetPreference('module_repository'),
							 80, 255 ));
$smarty->assign('extratext_url',$this->Lang('text_repository_url'));
    
$smarty->assign('prompt_latestdepends',$this->Lang('latestdepends'));
$smarty->assign('input_latestdepends',$this->CreateInputCheckbox($id,'latestdepends','1',$this->GetPreference('latestdepends',1)));
$smarty->assign('info_latestdepends',$this->Lang('info_latestdepends'));
    
$smarty->assign('prompt_reseturl',$this->Lang('prompt_reseturl'));
$smarty->assign('input_reseturl',$this->CreateInputSubmit($id,'reseturl',$this->Lang('reset')));
    

$smarty->assign('prompt_chunksize',$this->Lang('prompt_dl_chunksize'));
$smarty->assign('input_chunksize',$this->CreateInputText($id, 'input_dl_chunksize',
							       $this->GetPreference('dl_chunksize',256),3,3));
$smarty->assign('extratext_chunksize',$this->Lang('text_dl_chunksize'));

$smarty->assign('prompt_disable_caching',$this->Lang('prompt_disable_caching'));
$smarty->assign('input_disable_caching',
		$this->CreateInputCheckbox($id,'disable_caching',1,$this->GetPreference('disable_caching',0)));
$smarty->assign('info_disable_caching',$this->Lang('info_disable_caching'));

$smarty->assign('prompt_resetcache',$this->Lang('prompt_resetcache'));
$smarty->assign('input_resetcache',$this->CreateInputSubmit($id,'resetcache',$this->Lang('reset')));
			  
$smarty->assign('submit',$this->CreateInputSubmit( $id, 'submit', $this->Lang('submit')));
$smarty->assign('prompt_otheroptions',$this->Lang('prompt_otheroptions'));
$smarty->assign('prompt_settings',$this->Lang('prompt_settings'));
echo $this->ProcessTemplate('adminprefs.tpl');

#
# EOF
#
?>