
</div>

<div id="footer" class="footer">

</div>

<div id="footer1"></div>
<div id="footer2"><a href="http://www.cmsmadesimple.org">CMS made simple</a> is Free Software released under the GNU/GPL License</div>

<?php
if ($config["debug"] == true)
{
	echo '<div id="debugfooter">';
	global $sql_queries;
	echo "<div>".$sql_queries."</div>\n";
	echo '</div>';
}
?>
</body>
</html>

<?php

#Pull the stuff out of the buffer...
$htmlresult = '<!-- Start of buffered output -->' . @ob_get_contents() . '<!-- End of buffered output -->';
@ob_end_clean();

#Do any header replacements (this is for WYSIWYG stuff)
$footertext = '';
$formtext = '';

$userid = get_userid();
$wysiwyg = get_preference($userid, 'wysiwyg');

if (isset($wysiwyg) && $wysiwyg != '')
{
	if (isset($gCms->modules[$wysiwyg]) && $gCms->modules[$wysiwyg]['Installed'] == true &&
		$gCms->modules[$wysiwyg]['Active'] == true && isset($gCms->modules[$wysiwyg]['wysiwyg_module']))
	{
		if (isset($gCms->modules[$wysiwyg]['wysiwyg_header_function']))
		{
			@ob_start();
			call_user_func_array($gCms->modules[$wysiwyg]['wysiwyg_header_function'], array($gCms));
			$footertext .= @ob_get_contents();
			@ob_end_clean();
		}
		if (isset($gCms->modules[$wysiwyg]['wysiwyg_form_function']))
		{
			@ob_start();
			call_user_func_array($gCms->modules[$wysiwyg]['wysiwyg_form_function'], array($gCms));
			$formtext .= @ob_get_contents();
			@ob_end_clean();
		}
	}
}

$htmlresult = str_replace('<!-- THIS IS WHERE HEADER STUFF SHOULD GO -->', $footertext, $htmlresult);
$htmlresult = str_replace('##FORMSUBMITSTUFFGOESHERE##', $formtext, $htmlresult);

echo $htmlresult;

?>