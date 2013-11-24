<?php
#CMS - CMS Made Simple
#(c)2004 by Ted Kulp (ted@cmsmadesimple.org)
#This project's homepage is: http://www.cmsmadesimple.org
#
#This program is free software; you can redistribute it and/or modify
#it under the terms of the GNU General Public License as published by
#the Free Software Foundation; either version 2 of the License, or
#(at your option) any later version.
#
#This program is distributed in the hope that it will be useful,
#but WITHOUT ANY WARRANTY; without even the implied warranty of
#MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#GNU General Public License for more details.
#You should have received a copy of the GNU General Public License
#along with this program; if not, write to the Free Software
#Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#

if( !cmsms() ) exit();

if (!$this->VisibleToAdminUser()) {
  $this->ShowErrors($this->Lang("accessdenied"));
  return;
}
$allowimages = $this->GetPreference('allowimages',"1");
$css_styles = $this->GetPreference('css_styles',"");
$show_statusbar = $this->GetPreference('show_statusbar',"1");
$allow_resize = $this->GetPreference('allow_resize',"1");

$smarty->assign('css_styles_text', $this->Lang('css_styles_text'));
$smarty->assign('css_styles_help', $this->Lang('css_styles_help'));
$smarty->assign('css_styles_help2', $this->Lang('css_styles_help2'));
$smarty->assign('css_styles_input', $this->CreateTextArea(false,$id, $css_styles,'css_styles',"pagesmalltextarea","","","",'40','5'));

$smarty->assign('allowimages_text', $this->Lang('allowimages_text'));
$smarty->assign('allowimages_help', $this->Lang('allowimages_help'));
$smarty->assign('allowimages_input', $this->CreateInputCheckbox($id, 'allowimages', "1", $allowimages ));

$smarty->assign('startform', $this->CreateFormStart($id, 'savesettings', $returnid));
$smarty->assign('endform', $this->CreateFormEnd());

$smarty->assign('show_statusbar',$this->Lang('show_statusbar'));
$smarty->assign('help_show_statusbar',$this->Lang('help_show_statusbar'));
$smarty->assign('input_show_statusbar',$this->CreateInputCheckbox($id,'show_statusbar', 1, $show_statusbar));

$smarty->assign('allow_resize',$this->Lang('allow_resize'));
$smarty->assign('help_allow_resize',$this->Lang('help_allow_resize'));
$smarty->assign('input_allow_resize',$this->CreateInputCheckbox($id,'allow_resize',1,$allow_resize));

$smarty->assign('strip_background',$this->Lang('strip_background'));
$smarty->assign('help_strip_background',$this->Lang('help_strip_background'));
$smarty->assign('input_strip_background',$this->CreateInputCheckbox($id,'strip_background',1,$this->GetPreference('strip_background',1)));

$smarty->assign('force_blackonwhite',$this->Lang('force_blackonwhite'));
$smarty->assign('help_force_blackonwhite',$this->Lang('help_force_blackonwhite'));
$smarty->assign('input_force_blackonwhite',$this->CreateInputCheckbox($id,'force_blackonwhite',1,$this->GetPreference('force_blackonwhite')));

$smarty->assign('submit', $this->CreateInputSubmit($id, "savesettings", $this->Lang("savesettings")));

echo $this->ProcessTemplate('settings.tpl');

#
# EOF
#
?>