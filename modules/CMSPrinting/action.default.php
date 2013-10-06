<?php
if( !cmsms() ) exit();

$template = null;
if (isset($params['linktemplate'])) {
  $template = trim($params['linktemplate']);
}
else {
  $tpl = CmsLayoutTemplate::load_dflt_by_type('CMSPrinting::link');
  if( !is_object($tpl) ) {
    audit('',$this->GetName(),'No default link template found');
    return;
  }
  $template = $tpl->get_name();
}

$cache_id = '|ns'.md5(serialize($params));
if( !$smarty->isCached($this->GetDatabaseResource($template),$cache_id) ) {
  $config = cmsms()->GetConfig();

  $target="";
  if (isset($params["popup"])) {
    if ($params["popup"]=="true" || $params["popup"]=="1") {
      $target="target='_blank'";
      $params["goback"]="0";
    }
  }
  $smarty->assign("target", $target);
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

  if( isset($params['script']) && $params['script'] ) {
    $parms['script']=1;
  }
  if( isset($params['includetemplate']) && $params['includetemplate'] ) {
    $parms['includetemplate']=1;
  }
  if( isset($params['printtemplate']) && $params['printtemplate'] ) {
    $parms['printtemplate']=$params['printtemplate'];
  }

  // this generates the action url.
  $href = $this->CreateLink($id,'output',$returnid,'',$parms,'',true);

  // the rest is for display purposes.
  $smarty->assign("href", $href);
  $linktext="";
  if (isset($params["text"])) {
    $linktext=$params["text"];
  } else {
    $linktext=$this->Lang("defaultlinktext");
  }
  $smarty->assign("linktext", $linktext);

  if (isset($params["showbutton"])) {
    if ($params["showbutton"]=="true" || $params["showbutton"]=="1") {
      $linkimage="";
      $imgclass="";
      $imgsrc="";

      $imgsrc="modules/CMSPrinting/printbutton.gif";
      if (isset($params["src_img"])) $imgsrc=$params["src_img"];
      if (isset($params["class_img"])) $imgclass=$params["class_img"];
      $linkimage='<img src="'.$imgsrc.'" alt="'.$linktext.'" ';
      $linkimage.=' title="'.$linktext.'" ';
      if ($imgclass!="") $linkimage.='class="'.$imgclass.'" ';
      $linkimage.="/>";
      $smarty->assign('imgsrc',$imgsrc);
      $smarty->assign('imgclass',$imgclass);
      $smarty->assign("linkimage", $linkimage);
    }
  }
  if (isset($params["more"])) $smarty->assign("more", $params["more"]);


  $class="noprint";
  if (isset($params["class"])) $class=$params["class"];
  $smarty->assign("class", $class);
}

// Display template
echo $smarty->fetch($this->GetDatabaseResource($template),$cache_id);
# vim:ts=4 sw=4 noet
?>