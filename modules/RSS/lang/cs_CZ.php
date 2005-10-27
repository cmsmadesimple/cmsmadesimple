<?php
$lang['helpurl'] = 'URL RSS zdroje';
$lang['helpnumbertoshow'] = 'Maximální počet položek k zobrazení -- prázdné zobrazí všechny položky.';
$lang['helptarget'] = 'Vloží parametr target do odkazu.  Nastavte na "_blank" pro otevření v novém okně.';
$lang['helpdescriptions'] = 'Zobrazit popis RSS položek.';
$lang['help'] = <<<EOF
            <h3>Co dělá tento modul?</h3>
            <p>RSS je modul pro zobrazení novinek z jiných adres na Vašich stránkách. Vkládá se do šablony nebo stránky jako tag a to zobrazí titulek a url každé položky ze zadaného zdroje.</p>
            <h3>Co bych měl ještě vědět?</h3>
            <p>RSS modul zdroje kešuje, tudíž nejsou stahovány a parsovány při každém znovunačtení stránky. Stažené zdroje jsou ukládány lokálně pro snadný přístup. Jakmile lokální data zastarají, jsou stažena nová data. Tento modul by neměl mít vliv na dobu zpracování Vašich stránek.</p>
            <h3>Jak se používá?</h3>
            <p>Toto je tag modul, je vkládán do šablony nebo stránky pomocí tagu cms_module. Příklad syntaxe: <br /><code>{cms_module module="rss" url="http://download.freshmeat.net/backend/fm-releases.rdf" numbertoshow="5"}</code></p>
EOF;
?>
