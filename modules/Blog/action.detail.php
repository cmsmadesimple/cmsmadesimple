<?php
if (!isset($gCms)) die("Can't call actions directly!");

$post = null;
if (isset($params['url']))
{
	$post = cms_orm('BlogPost')->find_by_url($params['url']);
}
else if (isset($params['post_id']))
{
	$post = cms_orm('BlogPost')->find_by_id($params['post_id']);
}

$smarty->assign('post', $post);

echo $this->process_template_from_database($id, $return_id, 'detail');

# vim:ts=4 sw=4 noet
?>