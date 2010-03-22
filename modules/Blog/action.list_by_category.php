<?php
if (!isset($gCms)) die("Can't call actions directly!");

$posts = null;
$category = cms_orm('BlogCategory')->find_by_name_or_slug($params['category'], $params['category']);

if ($category != null)
{
	$posts = $category->published_posts;
}

$this->add_header_text('<link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="' . $this->generate_rss_link(array(), $params['category']) . '" />');

$smarty->assign('posts', $posts);

if (coalesce_key($params, 'rss', false) == true)
	echo $this->process_template_from_database($id, $return_id, 'rss');
else
	echo $this->process_template_from_database($id, $return_id, 'summary');

# vim:ts=4 sw=4 noet
?>