<?php
$lang['addacomment'] = 'Ajouter un commentaire';
$lang['cancel'] = 'Annuler';
$lang['comment'] = 'Commentaire';
$lang['error'] = 'Erreur';
$lang['errorenterauthor'] = 'Entrer un auteur';
$lang['errorentercomment'] = 'Entrer un commentaire (n\'est-ce pas le but?)';
$lang['submit'] = 'Envoyer';
$lang['yourname'] = 'Votre nom';
$lang['help'] = <<<EOD
	<h3>Que fait ce module?</h3>
	<p>Le module Commentaires est un tag-module.  Il est utile pour ajouter des commentaires aux pages, commentaires qui sont lus par les visiteurs de la page. Un exemple pratique de ce module module serait pour des pages de documentation, les utilisateurs peuvent donc ajouter des commentaires additionels et de l'information aux pages.</p>
	<h3>Comment l'utiliser?</h3>
	<p>Le module Commentaires est un module utilisé avec les tags.  Il est inséré dans votre page ou template en utilisant le tag cms_module. La synthaxe est: <code>{cms_module module="comments"}</code></p>
	<h3>Quels sont les paramètres possibles?</h3>
	<p>
	<ul>
		<li><em>(en option)</em> number="5" - Le nombre maximal de commentaires à afficher -- laisser ce paramètre vide affichera tous les commentaires</li>
		<li><em>(en option)</em> dateformat - Le format de date et heure utilisé, selon la fonction de php: strftime.  Voir <a href="http://php.net/strftime" target="_blank">ici</a> pour la liste des paramètres et pour information.</li>
	</ul>
EOD;
?>
