<?php
$lang['help'] = <<<EOF
	<h3>What does this do?</h3>
	<p>Prints a dhtml vertical menu.</p>
	<h3>How do I use it?</h3>
	<p>Just insert the module into your template/page like: <code>{cms_module module='phplayers'}</code></p>
	<h3>What parameters does it take?</h3>
	<p>
	<ul>
		<li><em>(optional)</em> <tt>showadmin</tt> - 1/0, whether you want to show or not the admin link.</li>
		<li><em>(optional)</em> <tt>start_element</tt> - the hierarchy of your element (ie : 1.2 or 3.5.1 for example). This parameter sets the root of the menu.</li>
		<li><em>(optional)</em> <tt>number_of_levels</tt> - an integer, the number of levels you want to show in your menu.</li>
		<li><em>(optional)</em> <tt>horizontal</tt> - 1/0, whether you want to have a horizontal menu instead of vertical.</li>
		<li><em>(optional)</em> <tt>id</tt> - text without spaces or special chars, default: menu1. You must specify it if you want to use more than one menu per page.</li>
		<li><em>(optional)</em> <tt>relative</tt> - 1/0, if set the menu will generate only the childs of the current page. This is very usefull if you want to add contect sensitive menus.</li>
		<li><em>(optional)</em> <tt>tree</tt> - 1/0, this option will generate a tree menu.</li>

	</ul>
	</p>
EOF;
?>
