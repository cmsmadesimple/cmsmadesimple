<?php

function smarty_function_modified_date($params, &$smarty) {
	if(empty($params['format']))
		$format = "%b %e, %Y";
	else
		$format = $params['format'];

	if ($smarty->get_template_vars('modified_date') != '')
		return strftime($format,$smarty->get_template_vars('modified_date'));
	else
		return "";
}

?> 
