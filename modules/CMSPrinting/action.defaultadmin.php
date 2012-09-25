<?php

if (!isset($gCms)) exit;
if (!$this->VisibleToAdminUser()) exit;

echo $this->StartTabHeaders();
$tab="";
if (isset($params["tab"])) $tab=$params["tab"];

if ($this->CheckPermission('Modify Templates')) {
  echo $this->SetTabHeader('linktemplate',$this->Lang('linktemplate'), ($tab=="linktemplate")?true:false);
  echo $this->SetTabHeader('printtemplate',$this->Lang('printtemplate'), ($tab=="printtemplate")?true:false);
}

if ($this->CheckPermission('Modify Stylesheets')) {
  echo $this->SetTabHeader('overridestyle',$this->Lang('overridestyle'), ($tab=="overridestyle")?true:false);
}
/*if ($this->GetPreference("pdfenable")=="1") {
  if ($this->CheckPermission('Modify Templates')) {
    echo $this->SetTabHeader('pdftemplate',$this->Lang('pdftemplate'), ($tab=="pdftemplate")?true:false);
  }
}*/
/*if ($this->CheckPermission('Modify Site Preferences')) {
  echo $this->SetTabHeader('pdfsettings',$this->Lang('pdfsettings'), ($tab=="pdfsettings")?true:false);
}*/

echo $this->EndTabHeaders();

echo $this->StartTabContent();

if ($this->CheckPermission('Modify Templates')) {
  echo $this->StartTab('linktemplate');
  $this->smarty->assign('startform', $this->CreateFormStart ($id, 'savetemplate', $returnid));
  $this->smarty->assign('endform', $this->CreateFormEnd());
  $this->smarty->assign('templatetype', $this->CreateInputHidden($id, 'templatetype','linktemplate'));

  $this->smarty->assign('templatetext', $this->Lang('template'));
  $this->smarty->assign('templateinput', $this->CreateTextArea(false,$id,$this->GetTemplate("linktemplate"),"newtemplate"));

  $this->smarty->assign('submit', $this->CreateInputSubmit ($id, 'savelinktemplatebutton', $this->Lang('savetemplate')));
  $this->smarty->assign('reset', $this->CreateInputSubmit ($id, 'resetlinktemplatebutton', $this->Lang('reset'),"","",$this->Lang("confirmresettemplate")));

  // Display the populated template
  echo $this->ProcessTemplate ('template.tpl');

  echo $this->EndTab();

  echo $this->StartTab('printtemplate');
  $this->smarty->assign('startform', $this->CreateFormStart ($id, 'savetemplate', $returnid));
  $this->smarty->assign('endform', $this->CreateFormEnd());
  $this->smarty->assign('templatetype', $this->CreateInputHidden($id, 'templatetype','printtemplate'));

  $this->smarty->assign('templatetext', $this->Lang('template'));
  $this->smarty->assign('templateinput', $this->CreateTextArea(false,$id,$this->GetTemplate("printtemplate"),"newtemplate"));

  $this->smarty->assign('submit', $this->CreateInputSubmit ($id, 'saveprinttemplatebutton', $this->Lang('savetemplate')));
  $this->smarty->assign('reset', $this->CreateInputSubmit ($id, 'resetprinttemplatebutton', $this->Lang('reset'),"","",$this->Lang("confirmresettemplate")));

  // Display the populated template
  echo $this->ProcessTemplate ('template.tpl');

  echo $this->EndTab();
}

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
/*if ($this->GetPreference("pdfenable")=="1") {
  if ($this->CheckPermission('Modify Templates')) {
    echo $this->StartTab('pdftemplate');
    $this->smarty->assign('startform', $this->CreateFormStart ($id, 'savetemplate', $returnid));
    $this->smarty->assign('endform', $this->CreateFormEnd());
    $this->smarty->assign('templatetype', $this->CreateInputHidden($id, 'templatetype','pdftemplate'));

    $this->smarty->assign('templatetext', $this->Lang('template'));
    $this->smarty->assign('templateinput', $this->CreateTextArea(false,$id,$this->GetTemplate("pdftemplate"),"newtemplate"));

    $this->smarty->assign('submit', $this->CreateInputSubmit ($id, 'savepdftemplatebutton', $this->Lang('savetemplate')));
    $this->smarty->assign('reset', $this->CreateInputSubmit ($id, 'resetpdftemplatebutton', $this->Lang('reset'),"","",$this->Lang("confirmresettemplate")));

    // Display the populated template
    echo $this->ProcessTemplate ('template.tpl');
    echo $this->EndTab();

  }
}*/
/*if ($this->CheckPermission('Modify Site Preferences')) {
  echo $this->StartTab('pdfsettings');
  $this->smarty->assign('startform', $this->CreateFormStart ($id, 'savesettings', $returnid));
  $this->smarty->assign('endform', $this->CreateFormEnd ());

  $this->smarty->assign('pdfenabletext',$this->Lang("pdfenable"));
  $this->smarty->assign('pdfenablehelp',$this->Lang("pdfenablehelp"));
  $this->smarty->assign('pdfenableinput', $this->CreateInputCheckbox($id,"pdfenable","1",$this->GetPreference("pdfenable")));
*/
  /*if ($this->GetPreference("pdfenable")=="1")   {
    /*$this->smarty->assign('pdfenginetext',$this->Lang("pdfengine"));
    $this->smarty->assign('pdfengineinput', $this->CreateInputDropDown($id,"pdfengine",array($this->Lang("internal")=>"internal","ABC"=>"abc","EASYW"=>"easyw"),"",$this->GetPreference("pdfengine","internal")));
  */
  /*  $this->smarty->assign('pdfheaderinput', $this->CreateInputText($id,"pdfheader",$this->GetPreference("pdfheader","Generated by CMS Made Simple"),80,255));
    $this->smarty->assign('pdfheadertext',$this->Lang("pdfheader"));

    $fontsizes=array("8pt"=>"8","10pt"=>"10","12pt"=>"12","14pt"=>"14");
    $this->smarty->assign('headerfontsizetext',$this->Lang("headerfontsize"));
    $this->smarty->assign('headerfontsizeinput', $this->CreateInputDropDown($id,"headerfontsize",$fontsizes,"",$this->GetPreference("headerfontsize","10")));

    $this->smarty->assign('contentfontsizetext',$this->Lang("contentfontsize"));
    $this->smarty->assign('contentfontsizeinput', $this->CreateInputDropDown($id,"contentfontsize",$fontsizes,"",$this->GetPreference("contentfontsize","10")));

    $fonttypes=Array($this->Lang("fontserif")=>"freeserif",$this->Lang("fontsansserif")=>"freesans");
    $this->smarty->assign('fonttypetext',$this->Lang("fonttypetext"));
    $this->smarty->assign('fonttypeinput', $this->CreateInputDropDown($id,"fonttype",$fonttypes,"",$this->GetPreference("fonttype","freesans")));

    $this->smarty->assign('orientationtext',$this->Lang("orientation"));
    $this->smarty->assign('orientationinput', $this->CreateInputDropDown($id,"orientation",array($this->Lang("portrait")=>"P",$this->Lang("landscape")=>"L"),"",$this->GetPreference("orientation","P")));
  }

  $this->smarty->assign('submit', $this->CreateInputSubmit ($id, 'savesettingsbutton', $this->Lang('savesettings')));

  // Display the populated template
  echo $this->ProcessTemplate ('settings.tpl');
  echo $this->EndTab();
}
*/

echo $this->EndTabContent();


?>