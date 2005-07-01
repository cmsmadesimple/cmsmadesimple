<?php
$lang['help'] = <<<EOF
	<h3>Cosa questo fa?</h3>
	<p>Visualizza un MENU in DHTML.</p>
	<h3>Come usarlo?</h3>
	<p>Inserisci semplicemente nel template/pagina: <code>{cms_module module='phplayers'}</code></p>
	<h3>Quali parametri ci sono?</h3>
	<p>
	<ul>
		<li><em>(opzionale)</em> <tt>showadmin</tt> - 1/0, se vuoi o non vuoi visualizzare il LINK al pannello di amministazione.</li>
		<li><em>(opzionale)</em> <tt>start_element</tt> - la gerarchia delle voci del MENU (ie : 1.2 or 3.5.1 for example). Questo parametro imposta la radice del MENU.</li>
		<li><em>(opzionale)</em> <tt>number_of_levels</tt> - un intero, il numero di livelli che vuoi far mostrare nel MENU.</li>
		<li><em>(opzionale)</em> <tt>horizontal</tt> - 1/0, se vuoi o no mostrare il MENU orizzontalmente.</li>
		<li><em>(opzionale)</em> <tt>id</tt> - text without spaces or special chars, default: menu1. You must specify it if you want to use more than one menu per page.</li>
		<li><em>(opzionale)</em> <tt>relative</tt> - 1/0, if set the menu will generate only the childs of the current page. This is very usefulll if you want to add contect sensitive menus.</li>
		<li><em>(opzionale)</em> <tt>tree</tt> - 1/0, this option will generate a tree menu.</li>
	</ul>
	</p>
EOF;
?>
