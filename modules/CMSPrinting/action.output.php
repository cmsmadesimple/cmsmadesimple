<?php
if( !isset($gCms) ) exit;
if( !isset($params['url']) ) exit;
if( !isset($params['pageid']) ) exit;

$template = null;
if (isset($params['printtemplate'])) {
  $template = trim($params['printtemplate']);
}
else {
  $tpl = CmsLayoutTemplate::load_dflt_by_type('CMSPrinting::print');
  if( !is_object($tpl) ) {
    audit('',$this->GetName(),'No default link template found');
    return;
  }
  $template = $tpl->get_name();
}

$orig_caching = $smarty->caching;
$smarty->caching = FALSE;

$url = '';
$pdf = false;
$script = false;
$includetemplate=false;
$title = '';
$templateid='';
$menutext='';
$pageid;
$config = $gCms->GetConfig();

$url             = base64_decode($params['url']);
$pageid          = (int)$params['pageid'];

$script          = (isset($params['script']))?(int)$params['script']:0;
$includetemplate = (isset($params['includetemplate']))?(int)$params['includetemplate']:0;
// get the output content.
$showcontent = '';
if( startswith($url,$config['root_url']) ) {
  $req = new cms_http_request();
  $showcontent = $req->execute($url);
  if ((isset($_REQUEST["includetemplate"]) && $_REQUEST["includetemplate"]=="true") ||
      (isset($params['includetemplate']) && $params['includetemplate']=='1') ) {
    $showcontent=$this->GetBody($showcontent);
  }
}

$contentops = $gCms->GetContentOperations();
$content = $contentops->LoadContentFromId($pageid);
$title = $content->Name();
$menutext = $content->MenuText();

$smarty->assign("title",$title);
$smarty->assign("content", $showcontent);
$smarty->assign("url", $url);
$encoding=$config['default_encoding'];

// kill any output that may have happened already.
$handlers = ob_list_handlers(); 
for ($cnt = 0; $cnt < sizeof($handlers); $cnt++) { ob_end_clean(); }

//Printing-specific assignments
$smarty->assign("designid", $content->GetPropertyValue('design_id'));
$smarty->assign("overridestylesheet", $this->GetPreference("overridestyle"));
$smarty->assign("encoding", $encoding);
$smarty->assign("rooturl", $config["root_url"]."/");
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
header("Content-Type: " . cmsms()->get_content_type() . "; charset=" .CmsNlsOperations::get_encoding());
if ($script) {
  $smarty->assign("printscript", '<script type="text/javascript">window.print();</script>');
} else {
  $smarty->assign("printscript", '');
}

echo $smarty->fetch($this->GetTemplateResource($template));
$smarty->caching = $orig_caching;
exit;
?>