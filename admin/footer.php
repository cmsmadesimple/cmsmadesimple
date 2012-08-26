<?php
if (isset($USE_THEME) && $USE_THEME == false)
  {
    echo '<!-- admin theme disabled -->';
  }
else
  {
    $themeObject->do_footer();
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

if (!isset($USE_THEME) || $USE_THEME != false)
  {
    echo '</body>';
    echo '</html>';
  }

#Pull the stuff out of the buffer...
$htmlresult = '';
if (!(isset($USE_OUTPUT_BUFFERING) && $USE_OUTPUT_BUFFERING == false))
{
	$htmlresult = @ob_get_contents();
	@ob_end_clean();
}

#Do any header replacements (this is for WYSIWYG stuff)
$headertext = '';
$formtext = '';
$formsubmittext = '';
$bodytext = '';

$userid = get_userid();
//$wysiwyg = get_preference($userid, 'wysiwyg');

// get the active wysiwyg
$loaded = ModuleOperations::get_instance()->GetLoadedModules();
if( is_array($loaded) && count($loaded) )
  {
    foreach( $loaded as $name => &$object )
      {
	if( !is_object($object) ) continue;
	if( $object->IsWYSIWYG() && $object->WYSIWYGActive() )
	  {
	    $bodytext       .= $object->WYSIWYGGenerateBody();
	    $headertext     .= $object->WYSIWYGGenerateHeader($htmlresult);
	    $formtext       .= $object->WYSIWYGPageForm();
	    $formsubmittext .= $object->WYSIWYGPageFormSubmit();
	    //break;
	  }
	else if( $object->IsSyntaxHighlighter() && $object->SyntaxActive() )
	  {
	    $bodytext.=$object->SyntaxGenerateBody();
	    $headertext.=$object->SyntaxGenerateHeader($htmlresult);
	    $formtext.=$object->SyntaxPageForm();
	    $formsubmittext.=$object->SyntaxPageFormSubmit();
	   // break;
	  }
      }
  }

$htmlresult = $themeObject->postprocess($htmlresult);
$htmlresult = str_replace('<!-- THIS IS WHERE HEADER STUFF SHOULD GO -->', $headertext, $htmlresult);
$htmlresult = str_replace('##FORMSUBMITSTUFFGOESHERE##', ' '.$formtext, $htmlresult);
$htmlresult = str_replace('##INLINESUBMITSTUFFGOESHERE##', ' '.$formsubmittext, $htmlresult);
$htmlresult = str_replace('##BODYSUBMITSTUFFGOESHERE##', ' '.$bodytext, $htmlresult);

echo $htmlresult;

$db = cmsms()->GetDb();
$endtime = microtime();
$memory = (function_exists('memory_get_usage')?memory_get_usage():0);
$memory_net = $memory - $orig_memory;
$memory_peak = (function_exists('memory_get_peak_usage')?memory_get_peak_usage():0);
echo "<!-- ".microtime_diff($starttime,$endtime)." / ".(isset($db->query_count)?$db->query_count:'')." / {$memory_net} / {$memory} / {$memory_peak} -->\n";
# vim:ts=4 sw=4 noet
?>
