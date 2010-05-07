<?php
if (!isset($gCms)) die("Can't call actions directly!");

$blah = CmsSearch::get_instance();

$hits = $blah->find($params['searchinput']);

$smarty->assign_by_ref('hits', $hits);

echo $this->process_template_from_database($id, $return_id, 'results');

# vim:ts=4 sw=4 noet
?>