<?php

class Object
{
	/**
	 * A hack to support __construct() on PHP 4
	 * Hint: descendant classes have no PHP4 class_name() constructors,
	 * so this constructor gets called first and calls the top-layer __construct()
	 * which (if present) should call parent::__construct()
	 *
	 * @return Object
	 */
	function Object()
	{
		$args = func_get_args();
		if (method_exists($this, '__destruct'))
		{
			register_shutdown_function(array(&$this, '__destruct'));
		}
		call_user_func_array(array(&$this, '__construct'), $args);
	}
	/**
	 * Class constructor, overridden in descendant classes.
	 */
	function __construct()
	{
	}
}

?>