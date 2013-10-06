<?php
if (!isset($gCms)) exit;
if (!$this->CheckPermission('Modify Templates')) exit;

if (!isset($params["templatetype"])) exit;

$message="";$tab="";$error="";
switch ($params["templatetype"]) {
  case "linktemplate" : {
    $tab="linktemplate";
    if (isset($params["resetlinktemplatebutton"])) {
      $this->ResetLinkTemplate();
      $message=$this->Lang("templatereset");
    } else {
      if ($params["newtemplate"]!="") {
        $this->SetLinkTemplate($params["newtemplate"]);
        $message=$this->Lang("templatesaved"); 
      } else {
        $error=$this->Lang("notemplatecontent");      
      }
    }
    break;
  }
  case "printtemplate" : {
    $tab="printtemplate";
    if (isset($params["resetprinttemplatebutton"])) {
      $this->ResetPrintTemplate();
      $message=$this->Lang("templatereset");
    } else {
      if ($params["newtemplate"]!="") {
        $this->SetPrintTemplate($params["newtemplate"]);
        $message=$this->Lang("templatesaved"); 
      } else {
        $error=$this->Lang("notemplatecontent");      
      }
    }
    break;
  }
  /*case "pdftemplate" : {
    $tab="pdftemplate";
    if (isset($params["resetpdftemplatebutton"])) {
      $this->ResetPDFTemplate();
      $message=$this->Lang("templatereset");
    } else {
      if ($params["newtemplate"]!="") {
        $this->SetPDFTemplate($params["newtemplate"]);
        $message=$this->Lang("templatesaved"); 
      } else {
        $error=$this->Lang("notemplatecontent");      
      }
    }
    break;
  }*/
  case "overridestyle" : {
    $tab="overridestyle";
    if (isset($params["resetoverridestylebutton"])) {
      $this->ResetOverrideStyle();
      $message=$this->Lang("overridestylereset");
    } else {
      if (isset($params["newstyle"])) {
        $this->SetOverrideStyle($params["newstyle"]);
        $message=$this->Lang("overridestylesaved"); 
      }
    }
    break;
  }
}

$this->Redirect($id,"defaultadmin",$returnid,array("module_message"=>$message,"module_error"=>$error,"tab"=>$tab));
?>