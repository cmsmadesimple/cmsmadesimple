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

if( !isset($gCms) ) exit;
//if( !$this->CheckPermission('Modify Modules') ) return;

$this->SetCurrentTab('installed');
if( !isset($params['mod']) ) {
    $this->SetError($this->Lang('error_missingparam'));
    $this->RedirectToAdminTab();
}
$module = get_parameter_value($params,'mod');
$lang = get_parameter_value($params,'lang');

// get the module instance... force it to load if necessary.
$ops = ModuleOperations::get_instance();
$modinstance = $ops->get_module_instance($module,'',TRUE);
if( !is_object($modinstance) ) {
    $this->SetError($this->Lang('error_getmodule',$module));
    $this->RedirectToAdminTab();
}
$theme = cms_utils::get_theme_object();
$theme->SetTitle('module_help');

$our_lang = CmsNlsOperations::get_current_language();
$smarty->assign('our_lang',$our_lang);

if( $our_lang != 'en_US' ) {
    if( $lang != '' ) {
        $smarty->assign('mylang_text',$this->Lang('display_in_mylanguage'));
        $smarty->assign('mylang_url',$this->create_url($id,'local_help',$returnid,array('mod'=>$module)));
        CmsNlsOperations::set_language('en_US');
    }
    else {
        $yourlang_url = $this->create_url($id,'local_help',$returnid,array('mod'=>$module,'lang'=>'en_US'));
        $smarty->assign('our_lang',$our_lang);
        $smarty->assign('englang_url',$yourlang_url);
        $smarty->assign('englang_text',$this->Lang('display_in_english'));
    }
}

$smarty->assign('module_name',$modinstance->GetName());
$smarty->assign('friendly_name',$modinstance->GetFriendlyName());

$smarty->assign('help_page',$modinstance->GetHelpPage());
if( $our_lang != 'en_US' && $lang != '' ) {
    CmsNlsOperations::set_language($our_lang);
}

echo $this->ProcessTemplate('local_help.tpl');

#
# EOF
#