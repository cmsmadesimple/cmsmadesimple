<?php
$lang['addacomment'] = 'Voeg een reactie toe';
$lang['cancel'] = 'Annuleer';
$lang['comment'] = 'Reactie';
$lang['error'] = 'Error';
$lang['errorenterauthor'] = 'Vul een naam in.';
$lang['errorentercomment'] = 'Vul een reactie in.';
$lang['submit'] = 'Toevoegen';
$lang['yourname'] = 'Uw naam';
$lang['help'] = <<<EOD
	<h3>Wat dit doet?</h3>
	<p>
	De commentarenmodule is een tagmodule. Het wordt gebruikt om commentaren aan individuele pagina's toe te voegen die door gebruikers kunnen worden gelezen die de pagina later bezoeken. De praktische reden voor deze module is voor documentatiepagina's, zodat de gebruikers extra commentaren en informatie aan de pagina kunnen toevoegen.
	</p>
	<h3>Hoe gebruik ik het?</h3>
	<p>Comments is just a tag module.  It's inserted into your page or template by using the cms_module tag.  Example syntax would be: <code>{cms_module module="comments"}</code></p>
	<h3>What parameters are there?</h3>
	<p>
	<ul>
		<li><em>(optional)</em> number="5" - Maximum number of items to display -- leaving empty will show all items</li>
		<li><em>(optional)</em> dateformat - Date/Time format using parameters from php's strftime function.  See <a href="http://php.net/strftime" target="_blank">here</a> for a parameter list and information.</li>
	</ul>
EOD;
?>
