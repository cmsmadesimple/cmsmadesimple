<?php
$lang['addacomment'] = 'Aggiungi un Commento';
$lang['cancel'] = 'Cancella';
$lang['comment'] = 'Commento';
$lang['error'] = 'Errore';
$lang['errorenterauthor'] = 'Inserisci l\'Autore';
$lang['errorentercomment'] = 'Inserisci un commento (non &egrave; questo il punto?)';
$lang['submit'] = 'Invia';
$lang['yourname'] = 'Il tuo Nome';
$lang['help'] = <<<EOD
	<h3>Che cosa fa?</h3>
	<p>Il modulo comments &egrave; un modulo tag.  Esso &egrave; usato per aggiungere commenti a pagine individuali, le quali possono essere lette dagli utenti. La ragione pratica di questo modulo &egrave; per la documentazione delle pagine. Cos&igrave; gli utenti possono aggiungere commenti ed informazioni alla pagina.</p>
	<h3>Come usarlo?</h3>
	<p>&Egrave; inserito nelle pagine o template usando il tag cms_module. Esempio di sintassi: <code>{cms_module module="comments"}</code></p>
	<h3>Quali parametri ci sono?</h3>
	<p>
	<ul>
		<li><em>(opzionale)</em> number="5" - Numero massimo di oggetti da visualizzare -- lasciandolo VUOTO li visualizza tutti.</li>
		<li><em>(opzionale)</em> dateformat - Formato Data/Ora usando i parametri della funzione strftime del PHP. Vedi  <a href="http://php.net/strftime" target="_blank">QUI</a> per una lista di parametri ed informazioni.</li>
	</ul>
EOD;
?>
