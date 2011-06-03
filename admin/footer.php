<?php
if (isset($USE_THEME) && $USE_THEME == false)
  {
    echo '<!-- admin theme disabled -->';
  }
else
  {
    $themeObject->DisplayMainDivEnd();
    $themeObject->OutputFooterJavascript();
    $themeObject->DisplayFooter();
  }

$gCms = cmsms();
if ($gCms->config["debug"] == true)
{
	echo '<div id="DebugFooter">';
	global $sql_queries;
	if (FALSE == empty($sql_queries))
	  {
	    echo "<div>".$sql_queries."</div>\n";
	  }
	foreach ($gCms->errors as $error)
	{
		echo $error;
	}
	echo '</div> <!-- end DebugFooter -->';
}

?>

</body>
</html>

<?php

#Pull the stuff out of the buffer...
$htmlresult = '';
if (!(isset($USE_OUTPUT_BUFFERING) && $USE_OUTPUT_BUFFERING == false))
{
	$htmlresult = @ob_get_contents();
	@ob_end_clean();
}

#Do any header replacements (this is for WYSIWYG stuff)
$footertext = '';
$formtext = '';
$formsubmittext = '';
$bodytext = '';

$userid = get_userid();
$wysiwyg = get_preference($userid, 'wysiwyg');

$modules = ModuleOperations::get_instance()->GetLoadedModules();
foreach($modules as $key => &$object)
{
  if( is_object($object) && $object->IsWYSIWYG() ) {
    $loadit=false;
    if ($object->WYSIWYGActive()) {
      $loadit=true;
    } 
    if ($loadit) {
      $bodytext.=$object->WYSIWYGGenerateBody();
      $footertext.=$object->WYSIWYGGenerateHeader($htmlresult);
      $formtext.=$object->WYSIWYGPageForm();
      $formsubmittext.=$object->WYSIWYGPageFormSubmit();
    }
  }
}

foreach($modules as $key=>$object)
{
  if ($object->IsSyntaxHighlighter())
    {
      $loadit=false;
      if ($object->SyntaxActive()) {
	$loadit=true;
      }
      if ($loadit) {
	$bodytext.=$object->SyntaxGenerateBody();
	$footertext.=$object->SyntaxGenerateHeader($htmlresult);
	$formtext.=$object->SyntaxPageForm();
	$formsubmittext.=$object->SyntaxPageFormSubmit();
      }
    }
}

$htmlresult = str_replace('<!-- THIS IS WHERE HEADER STUFF SHOULD GO -->', $footertext, $htmlresult);
$htmlresult = str_replace('##FORMSUBMITSTUFFGOESHERE##', ' '.$formtext, $htmlresult);
$htmlresult = str_replace('##INLINESUBMITSTUFFGOESHERE##', ' '.$formsubmittext, $htmlresult);
$htmlresult = str_replace('##BODYSUBMITSTUFFGOESHERE##', ' '.$bodytext, $htmlresult);

echo $htmlresult;
$endtime = microtime();
$memory = (function_exists('memory_get_usage')?memory_get_usage():0);
$memory = $memory - $orig_memory;
$memory_peak = (function_exists('memory_get_peak_usage')?memory_get_peak_usage():0);
debug_to_log(microtime_diff($starttime,$endtime)." / ".(isset($db->query_count)?$db->query_count:'')." / {$memory} / {$memory_peak}");
echo "<!-- ".microtime_diff($starttime,$endtime)." / ".(isset($db->query_count)?$db->query_count:'')." / {$memory} / {$memory_peak} -->\n";
#var_dump(memory_get_usage());

# vim:ts=4 sw=4 noet
?>
