<?php

echo '<p>Converting templates using headtags... ';

$query = "SELECT c.template_id, t.template_content FROM ".cms_db_prefix()."content c INNER JOIN ".cms_db_prefix()."content_props p ON p.content_id = c.content_id INNER JOIN ".cms_db_prefix()."templates t ON t.template_id = c.template_id WHERE p.prop_name = 'headtags' AND p.content IS NOT NULL AND p.content NOT LIKE ''";
$result = $db->Execute($query);

if ($result && $result->RecordCount() > 0)
{
	while ($row = $result->FetchRow())
	{
		$templatetxt = $row['template_content'];
		if (function_exists('str_ireplace'))
		{
			$templatetxt = str_ireplace('</head>', "{content block='headtags' wysiwyg='false'}\n</head>", $templatetxt);
		}
		else
		{
			$templatetxt = eregi_replace('<\/head>', "{content block='headtags' wysiwyg='false'}\n</head>", $templatetxt);
		}
		$time = $db->DBTimeStamp(time());
		$query = 'UPDATE ' . cms_db_prefix() . 'templates SET template_content = ?, modified_date = '.$time.' WHERE template_id = ?';
		$db->Execute($query, array($templatetxt, $row['template_id']));
	}
}

echo '[done]</p>';

echo '<p>Updating schema version... ';

$query = 'UPDATE ' . cms_db_prefix() . 'version SET version = 13';
$db->Execute($query);

echo '[done]</p>';

# vim:ts=4 sw=4 noet
?>
