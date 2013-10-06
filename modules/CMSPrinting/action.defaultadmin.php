<?php

if (!isset($gCms)) exit;
if (!$this->VisibleToAdminUser()) exit;

echo $this->StartTabHeaders();
$tab="";
if (isset($params["tab"])) $tab=$params["tab"];

if ($this->CheckPermission('Modify Stylesheets')) {
  echo $this->SetTabHeader('overridestyle',$this->Lang('overridestyle'), ($tab=="overridestyle")?true:false);
}

echo $this->EndTabHeaders();

echo $this->StartTabContent();

if ($this->CheckPermission('Modify Stylesheets')) {
  echo $this->StartTab('overridestyle');
  $this->smarty->assign('startform', $this->CreateFormStart ($id, 'savetemplate', $returnid));
  $this->smarty->assign('endform', $this->CreateFormEnd());
  $this->smarty->assign('templatetype', $this->CreateInputHidden($id, 'templatetype','overridestyle'));

  $this->smarty->assign('templatetext', $this->Lang('stylesheet'));
  $this->smarty->assign('templateinput', $this->CreateTextArea(false,$id,$this->GetPreference("overridestyle"),"newstyle"));

  $this->smarty->assign('submit', $this->CreateInputSubmit ($id, 'saveoverridestylebutton', $this->Lang('save')));
  $this->smarty->assign('reset', $this->CreateInputSubmit ($id, 'resetoverridestylebutton', $this->Lang('reset'),"","",$this->Lang("confirmresetstyle")));

  // Display the populated template
  echo $this->ProcessTemplate ('template.tpl');
  echo $this->EndTab();
}

echo $this->EndTabContent();


?>