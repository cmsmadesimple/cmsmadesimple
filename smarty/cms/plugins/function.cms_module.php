<?php

function smarty_function_cms_module($params, &$smarty) {

	global $cmsmodules;
	global $modulecmsobj;

	if (isset($params['module'])) {

		if (isset($cmsmodules[$params['module']])) {
			@ob_start();
			$obj = $cmsmodules[$params['module']]['Instance'];
			$obj->execute($modulecmsobj,"randstringgoeshere_",$params);
			$modoutput = @ob_get_contents();
			@ob_end_clean();
			echo $modoutput;
		}

	}

}

?>
