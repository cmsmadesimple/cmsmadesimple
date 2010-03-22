<?php
if (!isset($gCms)) die("Can't call actions directly!");

$conditions = array();
if ($params['day'] > -1)
{
	$conditions = array('status = ? and post_year = ? and post_month = ? and post_day = ?', 'publish', $params['year'], $params['month'], $params['day']);
}
else if ($params['month'] > -1)
{
	$conditions = array('status = ? and post_year = ? and post_month = ?', 'publish', $params['year'], $params['month']);
}
else if ($params['year'] > -1)
{
	$conditions = array('status = ? and post_year = ?', 'publish', $params['year']);
}
else
{
	$conditions = array('status = ?', 'publish');
}
$smarty->assign('posts', cms_orm('BlogPost')->find_all(array('order' => 'id desc', 'conditions' => $conditions)));

echo $this->process_template_from_database($id, $return_id, 'summary');

# vim:ts=4 sw=4 noet
?>