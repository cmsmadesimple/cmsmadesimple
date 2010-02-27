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

foreach($gCms->modules as $key=>$value)
{
	if ($gCms->modules[$key]['installed'] == true &&
		$gCms->modules[$key]['active'] == true &&
		$gCms->modules[$key]['object']->IsWYSIWYG()
		)
	{
		$loadit=false;
		if ($gCms->modules[$key]['object']->WYSIWYGActive()) {
			$loadit=true;
		} else {
		 //Silmarillion: It shouldn't be loaded unless it's in use 
		  /*if (get_preference(get_userid(), 'wysiwyg')==$gCms->modules[$key]['object']->GetName()) {
		  	$loadit=true;
		  }*/
		}
		if ($loadit) {
		  $bodytext.=$gCms->modules[$key]['object']->WYSIWYGGenerateBody();
		  $footertext.=$gCms->modules[$key]['object']->WYSIWYGGenerateHeader($htmlresult);
		  $formtext.=$gCms->modules[$key]['object']->WYSIWYGPageForm();
		  $formsubmittext.=$gCms->modules[$key]['object']->WYSIWYGPageFormSubmit();
		}
	}
}

foreach($gCms->modules as $key=>$value)
{
	if ($gCms->modules[$key]['installed'] == true &&
		$gCms->modules[$key]['active'] == true &&
		$gCms->modules[$key]['object']->IsSyntaxHighlighter()
		)
	{
		$loadit=false;
		if ($gCms->modules[$key]['object']->SyntaxActive()) {
			$loadit=true;
		} else {
		 //Silmarillion: It shouldn't be loaded unless it's in use
		  /*if (get_preference(get_userid(), 'syntaxhightlighter')==$gCms->modules[$key]['object']->GetName()) {
		  	$loadit=true;
		  }*/
		}
		if ($loadit) {
		  $bodytext.=$gCms->modules[$key]['object']->SyntaxGenerateBody();
		  $footertext.=$gCms->modules[$key]['object']->SyntaxGenerateHeader($htmlresult);
		  $formtext.=$gCms->modules[$key]['object']->SyntaxPageForm();
		  $formsubmittext.=$gCms->modules[$key]['object']->SyntaxPageFormSubmit();
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
echo "<!-- ".microtime_diff($starttime,$endtime)." / ".(isset($db->query_count)?$db->query_count:'')." / {$memory} / {$memory_peak} -->\n";
#var_dump(memory_get_usage());

# vim:ts=4 sw=4 noet
?>
