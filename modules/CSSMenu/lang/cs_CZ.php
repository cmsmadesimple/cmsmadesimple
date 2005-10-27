<?php
$lang['helpshowadmin'] = '1/0, zda chcete nebo nechcete zobrazit link na administraci.';
$lang['helpstart_element'] = 'hierarchie položek (např.: 1.2 nebo 3.5.1). Tento parametr nastaví kořen menu.';
$lang['helpnumber_of_levels'] = 'celé číslo, počet úrovní, které chcete zobrazit v menu.';
$lang['helphorizontal'] = '1/0, pokud chcete radši horizontální menu místo vertikálního.';
$lang['helprelative'] = '1/0, zda chcete v menu generovat pouze podpoložky aktuální stránky. Je to velmi užitečné pokud chcete menu citlivé na kontext.';
$lang['help'] = <<<EOF
  <h3>Co dělá tento modul?</h3>
  <p>Vytváří vertikální standardům odpovídající CSS menu. Trochu JavaScriptu je potřebné pro IE.</p>
  <p>Je založeno na těchto článcích, <a href="http://www.alistapart.com/articles/horizdropdowns/">Drop-Down Menus, Horizontal Style</a>, <a href="http://www.nickrigby.com/article/11/drop-down-menus-horizontal-style-pt-2">Drop-Down Menus, Horizontal Style: Pt 2</a>, <a href="http://www.nickrigby.com/article/25/drop-down-menus-horizontal-style-pt-3">Drop-Down Menus, Horizontal Style: Pt 3</a> by Nick Rigby.</p>
  <h3>Jak se používá?</h3>
  <p>Stačí pouze vložit tento modul do Vaší šablony/stránky, např.: <code>{cms_module module='cssmenu'}</code></p>
  <p>Instalace obsahuje 2 styly. Připojte horizontální a/nebo vertikální do Vaší šablony. Pro vytvéření nového stylu zkopírujte originál bez úprav.</p>
  <p>Poznámka pro horizontální styl, na řádce 14 nastavte  width: 600px; na vhodnou hodnotu pro šířku. (zkuste '100%%;')
EOF;
?>
