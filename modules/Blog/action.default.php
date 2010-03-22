<?php
if (!isset($gCms)) die("Can't call actions directly!");

$smarty->assign('posts', cms_orm('BlogPost')->find_all(array('order' => 'id desc', 'conditions' => array('status = ?', 'publish'))));

if (coalesce_key($params, 'rss', false) == true)
	echo $this->process_template_from_database($id, $return_id, 'rss');
else
	echo $this->process_template_from_database($id, $return_id, 'summary');

# vim:ts=4 sw=4 noet
?>
