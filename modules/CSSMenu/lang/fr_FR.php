<?php
$lang['horizontal'] = 'Feuille de style pour affichage horizontal';
$lang['vertical'] = 'Feuille de style pour affichage vertical';
$lang['helpshowadmin'] = '1/0, pour afficher ou non le lien à l\'admin.';
$lang['helpstart_element'] = 'La hiérarchie de votre élément (par exemple : 1.2 or 3.5.1). Ce paramètre défini la racine de votre menu';
$lang['helpnumber_of_levels'] = 'Un nombre entier, le nombre de niveaux que vous voulez afficher dans votre menu';
$lang['helphorizontal'] = '1/0, affiche un menu horizontal plutôt que vertical.';
$lang['helprelative'] = '1/0, si est défini en 1, le menu va générer seulement les niveaux situés en-dessous du niveau de la page actuelle. Ceci est très utile quand vous voulez ajouter des menus dans un contexte sensible.';
$lang['help'] = <<<EOF
  <h3>Que fait ce module?</h3>
  <p>Il affiche un menu vertical ou horizontal, purement compatible CSS. Un petit code JavaScript est requis pour l'affichage dans Internet Explorer.</p>
  <p>Ce module est basé sur ces articles, <a href="http://www.alistapart.com/articles/horizdropdowns/">Drop-Down Menus, Horizontal Style</a>, <a href="http://www.nickrigby.com/article/11/drop-down-menus-horizontal-style-pt-2">Drop-Down Menus, Horizontal Style: Pt 2</a>, <a href="http://www.nickrigby.com/article/25/drop-down-menus-horizontal-style-pt-3">Drop-Down Menus, Horizontal Style: Pt 3</a> by Nick Rigby.</p>
  <h3>Comment l'utiliser?</h3>
  <p>Insérez simplement votre menu dans votre template/page ainsi: <code>{cms_module module='cssmenu'}</code></p>
  <p>Modifiez /modules/CSSMenu/CSSMenu.css pour changer le style. Se référer aux articles ci-dessus (en anglais) pour plus d\'information.</p>
  <p>Modifiez /modules/CSSMenu/CSSMenuHorizontal.css pour changer le style. A la ligne 14, ajustez la largeur: 600px; à celle qui vous convient. (essayez '100%%')
EOF;
?>
