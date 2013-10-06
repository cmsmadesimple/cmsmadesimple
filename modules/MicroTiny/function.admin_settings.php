<?php
if( !cmsms() ) exit();

if (!$this->VisibleToAdminUser()) {
  $this->ShowErrors($this->Lang("accessdenied"));
  return;
}
$allowimages = $this->GetPreference('allowimages',"0");
$css_styles = $this->GetPreference('css_styles',"");

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
$smarty->assign('input_show_statusbar',$this->CreateInputCheckbox($id,'show_statusbar', 1, $this->GetPreference('show_statusbar')));

$smarty->assign('allow_resize',$this->Lang('allow_resize'));
$smarty->assign('help_allow_resize',$this->Lang('help_allow_resize'));
$smarty->assign('input_allow_resize',$this->CreateInputCheckbox($id,'allow_resize',1,$this->GetPreference('allow_resize')));

$smarty->assign('strip_background',$this->Lang('strip_background'));
$smarty->assign('help_strip_background',$this->Lang('help_strip_background'));
$smarty->assign('input_strip_background',$this->CreateInputCheckbox($id,'strip_background',1,$this->GetPreference('strip_background',1)));

$smarty->assign('force_blackonwhite',$this->Lang('force_blackonwhite'));
$smarty->assign('help_force_blackonwhite',$this->Lang('help_force_blackonwhite'));
$smarty->assign('input_force_blackonwhite',$this->CreateInputCheckbox($id,'force_blackonwhite',1,$this->GetPreference('force_blackonwhite')));

$smarty->assign('submit', $this->CreateInputSubmit($id, "savesettings", $this->Lang("savesettings")));

echo $this->ProcessTemplate('settings.tpl');

?>

