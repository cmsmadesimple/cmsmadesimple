<?php
if( !isset($gCms) ) exit;
if( !isset($params['url']) ) exit;
if( !isset($params['pageid']) ) exit;

$url = '';
$pdf = false;
$script = false;
$includetemplate=false;
$title = '';
$templateid='';
$menutext='';
$pageid;
$config =& $gCms->GetConfig();

$url             = base64_decode($params['url']);
$pageid          = (int)$params['pageid'];
$pdf             = (int)$params['pdf'];
$pdf             = $pdf && $this->GetPreference('pdfenable');

$script          = (int)$params['script'];
$includetemplate = (int)$params['includetemplate'];
// get the output content.
$showcontent = '';
if( startswith($url,$config['root_url']) ) {
  $showcontent = $this->GetURLContent($url);
  if ((isset($_REQUEST["includetemplate"]) && $_REQUEST["includetemplate"]=="true") ||
      (isset($params['includetemplate']) && $params['includetemplate']=='1') )
    {
      $showcontent=$this->GetBody($showcontent);
    }
}

$contentops =& $gCms->GetContentOperations();
$content =& $contentops->LoadContentFromId($pageid);
$title = $content->Name();
$templateid = $content->TemplateId();
$menutext = $content->MenuText();

$this->smarty->assign("title",$title);
$this->smarty->assign("content", $showcontent);
$this->smarty->assign("url", $url);
$encoding=$config['default_encoding'];

// kill any output that may have happened already.
$handlers = ob_list_handlers(); 
for ($cnt = 0; $cnt < sizeof($handlers); $cnt++) { ob_end_clean(); }

//Printing-specific assignments
$smarty->assign("templateid", $templateid);
$smarty->assign("overridestylesheet", $this->GetPreference("overridestyle"));
$smarty->assign("encoding", $encoding);
$smarty->assign("rooturl", $config["root_url"]."/");
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
header("Content-Type: " . cmsms()->get_variable('content-type') . "; charset=" .get_encoding());
if ($script) {
  $smarty->assign("printscript", '<script type="text/javascript">window.print();</script>');
 } else {
  $smarty->assign("printscript", '');
 }
$str = $this->ProcessTemplateFromDatabase("printtemplate");
echo $str;
exit;