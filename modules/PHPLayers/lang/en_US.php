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
	</ul>
	</p>
EOF;
?>
