<?php
if (!isset($gCms)) exit;

$this->Database->drop_table('blog_posts');
$this->Database->drop_table('blog_categories');
$this->Database->drop_table('blog_post_categories');

?>