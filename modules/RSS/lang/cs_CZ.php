<?php
$lang['help'] = <<<EOF
            <h3>Co dělá?</h3>
            <p>RSS je modul pro zobrazení zdrojů novinek z jiných adres na Vašich stránkách. Může to být vloženo do šablony nebo stránky jako tag a to zobrazí titulek a url každé položky ze zadaného zdroje..</p>
            <h3>Co bych měl ještě vědět?</h3>
            <p>RSS modul kešuje zdroje, takže nejsou stahovány a parsovány při každém refreshi. Stažené zdroje jsou ukládány lokálně pro snadný přístup. Jakmile lokální data zastarají, jsou stažena nová data. Pokles výkonu by neměl být znát použitím v šabloně.</p>
            <h3>Jak se používá?</h3>
            <p>Toto je tag modul, je vkládán do šablony nebo stránky pomocí tagu cms_module. Příklad syntaxe: <br /><code>{cms_module module="rss" url="http://download.freshmeat.net/backend/fm-releases.rdf" numbertoshow="5"}</code></p>
            <h3>Jaké jsou parametry?</h3>
            <p>
            <ul>
                <li>url="http://feed_url" - URL RSS zdroje</li>
                <li><em>(volitelné)</em>numbertoshow="5" - Maximální počet položek k zobrazení -- prázdné zobrazí všechny položky</li>
            </ul>
            </p>
EOF;
?>
