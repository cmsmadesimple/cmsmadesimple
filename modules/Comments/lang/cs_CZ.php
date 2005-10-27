<?php
$lang['addacomment'] = 'Vložit komentář';
$lang['cancel'] = 'Storno';
$lang['comment'] = 'Komentovat';
$lang['error'] = 'Chyba';
$lang['errorenterauthor'] = 'Vložit autora';
$lang['errorentercomment'] = 'Vložit komentář (není to důvod existence tohoto modulu?)';
$lang['submit'] = 'Odeslat';
$lang['yourname'] = 'Vaše jméno';
$lang['helpnumber'] = 'Maximální počet položek k zobrazení -- ponecháno prázdné zobrazí všechny položky';
$lang['helpdateformat'] = 'Formát data/času - využívá parametrů z php funkce strftime.  Viz. <a href="http://php.net/strftime" target="_blank">zde</a> pro seznam parametrů.';
$lang['help'] = <<<EOD
	<h3>Co dělá tento modul?</h3>
	<p>Tento modul slouží ke vkládání komentářů k jednotlivým stránkám. Praktický význam má tento modul pro dokumentační stránky, kde mohou uživatelé přispívat poznámkami a dodatečnými informacemi.</p>
	<h3>Jak se používá?</h3>
	<p>Komentář je tag modul. Vkládá se do stránky nebo šablony použitím tagu cms_module.  Příklad syntaxe: <code>{cms_module module="comments"}</code></p>
EOD;
?>
