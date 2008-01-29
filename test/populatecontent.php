<?php

include_once ('../lib/cmsms.api.php');

$db = cms_db();

define('NUM_PER', 32);
define('MAX_DEPTH', 3);

//var_dump('Creating ' . (pow(NUM_PER, MAX_DEPTH) + NUM_PER + 1) . ' nodes...');

$db->Execute('DELETE FROM ' . cms_db_prefix() . 'content_props');
//$db->Execute('ALTER TABLE ' . cms_db_prefix() . 'content_props AUTO_INCREMENT = 1');
$db->Execute('DELETE FROM ' . cms_db_prefix() . 'content');
//$db->Execute('ALTER TABLE ' . cms_db_prefix() . 'content AUTO_INCREMENT = 1');

$id = 1;
$lft = 1;
$rgt = 2;
$hierarchy = '';

function create_content(&$id, &$lft, &$rgt, $hierarchy, $depth, $parent_id)
{
	$db = cms_db();

	/*
	  `id` int(11) NOT NULL auto_increment,
	  `content_name` varchar(255) default NULL,
	  `type` varchar(25) default NULL,
	  `owner_id` int(11) default NULL,
	  `parent_id` int(11) default NULL,
	  `template_id` int(11) default NULL,
	  `item_order` int(11) default NULL,
	  `lft` int(11) default NULL,
	  `rgt` int(11) default NULL,
	  `hierarchy` varchar(255) default NULL,
	  `default_content` tinyint(4) default NULL,
	  `menu_text` varchar(255) default NULL,
	  `content_alias` varchar(255) default NULL,
	  `show_in_menu` tinyint(4) default NULL,
	  `collapsed` tinyint(4) default NULL,
	  `markup` varchar(25) default NULL,
	  `active` tinyint(4) default NULL,
	  `cachable` tinyint(4) default NULL,
	  `id_hierarchy` varchar(255) default NULL,
	  `hierarchy_path` text,
	  `prop_names` text,
	  `metadata` text,
	  `titleattribute` varchar(255) default NULL,
	  `tabindex` varchar(10) default NULL,
	  `accesskey` varchar(5) default NULL,
	  `last_modified_by` int(11) default NULL,
	  `create_date` datetime default NULL,
	  `modified_date` datetime default NULL,
	*/
	for ($i = 1; $i <= NUM_PER; $i++)
	{
		//$new_id = $id + $i;
		$id++;
		$orig_id = $id;
		//$lft = $lft + (MAX_DEPTH - $depth + 1);
		$lft++;

		//$rgt = $lft + ((MAX_DEPTH - $depth + 1) * NUM_PER + 1);

		$new_hierarchy = $hierarchy . $i;
		$query = 'INSERT INTO ' . cms_db_prefix() . 'content (id, lft, rgt, hierarchy, content_name, menu_text, content_alias, item_order, template_id, parent_id, default_content, active, show_in_menu) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1, ?, 0, 1, 1)';
		$db->Execute($query, array($id, $lft, $lft + 1, $new_hierarchy, $new_hierarchy, $new_hierarchy, $new_hierarchy, $i, $parent_id));
		
		if ($depth < MAX_DEPTH)
		{
			create_content($id, $lft, $rgt, $new_hierarchy . '.', $depth + 1, $orig_id);
		}
		
		$lft++;
		
		$query = 'UPDATE ' . cms_db_prefix() . 'content SET rgt = ? WHERE id = ?';
		$db->Execute($query, array($lft, $orig_id));
	}
}

$db->Execute('SET insert_id = 1');

$query = 'INSERT INTO ' . cms_db_prefix() . 'content (id, lft, rgt, hierarchy, content_name, menu_text, item_order) VALUES (?, ?, ?, ?, ?, ?, ?)';
$db->Execute($query, array($id, $lft, $lft+1, '', '__root__', '__root__', 1));

create_content($id, $lft, $rgt, '', 1, 1);

$query = 'UPDATE ' . cms_db_prefix() . 'content SET rgt = ? WHERE id = ?';
$db->Execute($query, array($lft+1, 1));

$query = 'UPDATE ' . cms_db_prefix() . 'content SET default_content = 1 WHERE id = 2';
$db->Execute($query);

include ('../lib/page.functions.php'); //bug -- needs fixing in CmsSmarty
CmsCache::clear();

?>
