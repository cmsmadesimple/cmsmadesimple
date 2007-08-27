<?php

function smarty_block_translate($params, $content, &$smarty)
{
	if (is_null($content))
	{
		return;
	}
	
	echo $content;
}

?>