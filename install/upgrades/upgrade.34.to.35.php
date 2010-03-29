<?php
global $gCms;

echo '<p>Changing content type names...';

$themap = array();
$themap['content'] = 'Content';
$themap['link'] = 'Link';
$themap['pagelink'] = 'PageLink';
$themap['separator'] = 'Separator';
$themap['sectionheader'] = 'SectionHeader';
$themap['errorpage'] = 'ErrorPage';
$query = 'UPDATE '.cms_db_prefix().'content SET type = ? WHERE type = ?';
foreach( $themap as $fromtype => $totype )
{
	$db->Execute($query,array($totype,$fromtype));
}

echo '<p>Updating schema version... ';

$query = "UPDATE ".cms_db_prefix()."version SET version = 35";
$db->Execute($query);

echo '[done]</p>';

?>
