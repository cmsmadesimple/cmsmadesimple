<?php
$lang['horizontal'] = 'Horisontalt Stylesheet';
$lang['vertical'] = 'Vertikalt Stylesheet';
$lang['helpshowadmin'] = '1/0, angiver hvorvidt admin-linket ønskes vist i menuen.';
$lang['helpstart_element'] = 'placeringen i hierakiet (1.2 eller 3.5.1 for eksempel). Dette parameter angiver roden for menuen.';
$lang['helpnumber_of_levels'] = 'et tal der angiver hvor mange niveau\'er det ønskes vist i menuen.';
$lang['helphorizontal'] = '1/0, angiver om en horisontal menu ønskes i stedet for en vertikal.';
$lang['help'] = <<<EOF
  <h3>Hvad gør dette?</h3>
  <p>Viser en vertikal menu, der overholder web-standarderne og kun benytter sig af CSS. En mindre stump JavaScript er nødvendig i Internet Explorer.</p>
  <p>Menuen er baseret på disse artikler, <a href="http://www.alistapart.com/articles/horizdropdowns/">Drop-Down Menus, Horizontal Style</a>, <a href="http://www.nickrigby.com/article/11/drop-down-menus-horizontal-style-pt-2">Drop-Down Menus, Horizontal Style: Pt 2</a>, <a href="http://www.nickrigby.com/article/25/drop-down-menus-horizontal-style-pt-3">Drop-Down Menus, Horizontal Style: Pt 3</a> by Nick Rigby.</p>
  <h3>Hvordan benytter jeg den?</h3>
  <p>Indsæt blot modulet i en side eller skabelon således: <code>{cms_module module='cssmenu'}</code></p>
  <p>Redigér /modules/CSSMenu/CSSMenu.css for at ændre på udseendet. Se ovenstående artikler for yderligere information.</p>
  <p>Redigér /modules/CSSMenu/CSSMenuHorizontal.css for at ændre på udseendet. På linie 14 kan \"width: 600px;\" justeres til en passende værdi for bredden. (prøv '100%%')
EOF;
?>
