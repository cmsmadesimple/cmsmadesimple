<?php
if( !cmsms() ) exit();
$config = cmsms()->GetConfig();

$target="";
if (isset($params["popup"])) {
  if ($params["popup"]=="true" || $params["popup"]=="1") {
    $target="target='_blank'";
    $params["goback"]="0";
  }
}
$this->smarty->assign("target", $target);
$onlyurl=false;
if (isset($params["onlyurl"])) {
  if ($params["onlyurl"]=="true" || $params["onlyurl"]=="1") $onlyurl=true;
}


$includetemplate=false;
  if (isset($params['includetemplate']) && (($params["includetemplate"]=="true") || $params["includetemplate"]=="1")) {
    $includetemplate=true;
  }
$url=$this->GetCurrentURL($includetemplate);
$url=base64_encode($url);

$parms = array();
$parms["url"]=$url;
$parms['pageid']=$returnid;

/*if (isset($params['pdf']) && (($params["pdf"]=="true") || $params["pdf"]=="1")) {
  if ($this->GetPreference("pdfenable")=="1") {
    $parms['pdf']=1;
  }
}
else*/

  if( isset($params['script']) && $params['script'] )
    {
      $parms['script']=1;
    }
  if( isset($params['includetemplate']) && $params['includetemplate'] )
    {
      $parms['includetemplate']=1;
    }


// this generates the action url.
$href = $this->CreateLink($id,'output',$returnid,'',$parms,'',true);

// the rest is for display purposes.
$this->smarty->assign("href", $href);
//$this->smarty->assign("url", $url);
$linktext="";
if (isset($params["text"])) {
  $linktext=$params["text"];
} else {
  $linktext=$this->Lang("defaultlinktext");
  /*if( isset($parms['pdf']) && $parms['pdf'] == 1 ) {
    $linktext=$this->Lang("defaultpdflinktext");
  }*/
}
$this->smarty->assign("linktext", $linktext);

if (isset($params["showbutton"])) {
  if ($params["showbutton"]=="true" || $params["showbutton"]=="1") {
    $linkimage="";
    $imgclass="";
    $imgsrc="";

    $imgsrc="modules/CMSPrinting/printbutton.gif";
    /*if (isset($parms['pdf']) && $parms['pdf'] == 1 ) {
      $imgsrc="modules/Printing/pdfbutton.gif";
    }*/
    if (isset($params["src_img"])) $imgsrc=$params["src_img"];
    if (isset($params["class_img"])) $imgclass=$params["class_img"];
    $linkimage='<img src="'.$imgsrc.'" alt="'.$linktext.'" ';
    $linkimage.=' title="'.$linktext.'" ';
    if ($imgclass!="") $linkimage.='class="'.$imgclass.'" ';
    $linkimage.="/>";
    $this->smarty->assign('imgsrc',$imgsrc);
    $this->smarty->assign('imgclass',$imgclass);
    $this->smarty->assign("linkimage", $linkimage);
  }
}
if (isset($params["more"])) $this->smarty->assign("more", $params["more"]);


$class="noprint";
if (isset($params["class"])) $class=$params["class"];
$this->smarty->assign("class", $class);

if ($onlyurl) {
  echo $href; 
} else {
  echo $this->ProcessTemplateFromDatabase("linktemplate");
}

?>
