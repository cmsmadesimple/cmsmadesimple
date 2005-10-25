<?php
$themeObject->DisplayMainDivEnd();
$themeObject->OutputFooterJavascript();
$themeObject->DisplayFooter();

if ($gCms->config["debug"] == true)
{
	echo '<div id="DebugFooter">';
	global $sql_queries;
	echo "<div>".$sql_queries."</div>\n";
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
$htmlresult = @ob_get_contents();
@ob_end_clean();

#Do any header replacements (this is for WYSIWYG stuff)
$footertext = '';
$formtext = '';
$bodytext = '';

$userid = get_userid();
$wysiwyg = get_preference($userid, 'wysiwyg');

foreach($gCms->modules as $key=>$value)
{
	if ($gCms->modules[$key]['installed'] == true &&
		$gCms->modules[$key]['active'] == true &&
		$gCms->modules[$key]['object']->IsWYSIWYG())
	{
		@ob_start();
		$gCms->modules[$key]['object']->WYSIWYGGenerateBody();
		$bodytext .= @ob_get_contents();
		@ob_end_clean();

		@ob_start();
		$gCms->modules[$key]['object']->WYSIWYGGenerateHeader();
		$footertext .= @ob_get_contents();
		@ob_end_clean();

		@ob_start();
		$gCms->modules[$key]['object']->WYSIWYGPageForm();
		$formtext .= @ob_get_contents();
		@ob_end_clean();
	}
}

$htmlresult = str_replace('<!-- THIS IS WHERE HEADER STUFF SHOULD GO -->', $footertext, $htmlresult);
$htmlresult = str_replace('##FORMSUBMITSTUFFGOESHERE##', ' '.$formtext, $htmlresult);
$htmlresult = str_replace('##BODYSUBMITSTUFFGOESHERE##', ' '.$bodytext, $htmlresult);

echo $htmlresult;

