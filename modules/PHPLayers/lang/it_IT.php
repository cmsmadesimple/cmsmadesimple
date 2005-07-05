<?php
$lang['help'] = <<<EOF
	<h3>Che cosa fa?</h3>
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
		<li><em>(opzionale)</em> <tt>id</tt> - testo senza spazi o caratteri speciali, default: menu1. Dovete specificarlo se volete usare pi&ugrave; di un menu per pagina.</li>
		<li><em>(opzionale)</em> <tt>relative</tt> - 1/0, se configurato il menu generar&agrave; solo i discendenti della pagina corrente. Questo &egrave; utile se volete aggiungere menu sensibili del contesto.</li>
		<li><em>(opzionale)</em> <tt>tree</tt> - 1/0, questa opzione generer&agrave; un menu ad albero.</li>
	</ul>
	</p>
EOF;
?>
