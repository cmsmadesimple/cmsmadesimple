<?php
$lang['addacomment'] = 'Neuer Kommentar';
$lang['cancel'] = 'Abbruch';
$lang['comment'] = 'Kommentar';
$lang['error'] = 'Fehler';
$lang['errorenterauthor'] = 'Name des Autors eingeben !';
$lang['errorentercomment'] = 'Den Kommentar eingeben !';
$lang['submit'] = 'Speichern';
$lang['yourname'] = 'Ihr Name';
$lang['help'] = <<<EOD
	<h3>Was macht dieses Modul?</h3>
	<p> Es wird als Tag individuell eingesetzt und erm&ouml;glicht Besuchern einer Seite einen Kommentar oder eine zus&auml;tzliche Information<br />dazu abzugeben, der seinerseits wieder von anderen Besuchern gelesen werden kann.</p>
	<h3>Wie wird es eingesetzt?</h3>
	<p>Es wird als Tag entweder individuell in eine oder mehreren ausgew?hlten Seiten eingesetzt oder global ?ber das Template.<br /><br />
          Beispiel: <code>{cms_module module="comments"}</code></p>
	<h3>Welche Parameter gibt es?</h3>
	<p>
	<ul>
		<li><em>(optional)</em> number="5" - Maximal anzuzeigende Kommentare -- ohne Parameter werden alle angezeigt.</li>
		<li><em>(optional)</em> dateformat - Datum/Zeit , das Format muss dem der <a href="http://php.net/strftime" target="_blank">PHP strftime - Funktion.</a>entsprechen.</li>
	</ul>
EOD;
?>
