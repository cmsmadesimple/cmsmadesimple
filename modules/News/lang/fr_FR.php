<?php
$lang['allentries'] = 'Toutes les entrées';
$lang['addarticle'] = 'Ajouter un article';
$lang['addcategory'] = 'Ajouter une catégorie';
$lang['addnewsitem'] = 'Ajouter une News';
$lang['areyousure'] = 'Etes-vous sûr de vouloir effacer?';
$lang['articles'] = 'Articles';
$lang['cancel'] = 'Annuler';
$lang['category'] = 'Catégorie';
$lang['categories'] = 'Catégories';
$lang['content'] = 'Contenu';
$lang['dateformat'] = '%s pas dans un format valide yyyy-mm-dd hh:mm:ss';
$lang['delete'] = 'Effacer';
$lang['description'] = 'Ajout, édition et effacement des entrées News';
$lang['detailtemplate'] = 'Template de l\'article détaillé';
$lang['displaytemplate'] = 'Afficher le template';
$lang['edit'] = 'Editer';
$lang['enddate'] = 'Date de fin';
$lang['endrequiresstart'] = 'Entrer une date de fin nécessite qu\'une date de début soit également entrée';
$lang['entries'] = '%s entrées';
$lang['expiry'] = 'Expiration';
$lang['filter'] = 'Filtre';
$lang['more'] = 'Plus';
$lang['name'] = 'Nom';
$lang['news'] = 'News';
$lang['newcategory'] = 'Nouvelle catégorie';
$lang['needpermission'] = 'Vous devez avoir la permission \'%s\' pour exécuter cette action.';
$lang['nocategorygiven'] = 'Aucune catégorie entrée';
$lang['nocontentgiven'] = 'Aucun contenu entré';
$lang['noitemsfound'] = '<strong>Aucun</strong> objet trouvé pour cette catégorie: %s';
$lang['nopostdategiven'] = 'Il manque la date à laquelle la News sera postée';
$lang['note'] = '<em>Note:</em> Les dates doivent être entrées dans ce format \'yyyy-mm-dd hh:mm:ss\'.';
$lang['notitlegiven'] = 'Aucun titre entré';
$lang['print'] = 'Imprimer';
$lang['postdate'] = 'Date à laquelle la News sera postée';
$lang['postinstall'] = 'Assurez-vous que vous définissez la permission "Modify News" sur les utilisateurs qui administeront les News.';
$lang['rsstemplate'] = 'RSS Template';
$lang['startdate'] = 'Date de début';
$lang['startrequiresend'] = 'Entrer une date de début nécessite qu\'une date de fin soit également entrée';
$lang['submit'] = 'Envoyer';
$lang['summarytemplate'] = 'Template du sommaire de l\'article';
$lang['title'] = 'Titre';
$lang['help'] = <<<EOF
	<h3>Que fait ce module?</h3>
	<p>News est un module pour afficher des News sur vos pages, de façon similaire à un blog, mais avec plus de fonctions!  Dès que le module est installé, une page d'administration des News est ajoutée à votre menu "contenu" qui vous permettra de sélectionner ou ajouter des catégories de News.  Dès qu'une catégorie de News est sélectionnée ou créée, une liste des News pour cette catégorie est affichée.  Depuis là, vous pouvez ajouter, éditer ou effacer les News dans cette catégorie.</p>
	<h3>Securité</h3>
	<p>L'utilisateur doit faire partie d'un groupe avec la permission 'Modify News' pour pouvoir ajouter, éditer ou effacer des News.</p>
	<h3>Comment l'utiliser?</h3>
	<p>La façon la plus facile de l'utiliser est en conjonction avec le tag cms_module.  Cela insérera votre module dans votre template ou votre page page où vous le désirez, et y affichera les News.  Exemple de synthaxe: <code>{cms_module module="news" number="5" category="beer"}</code></p>
	<h3>Quels sont les paramètres possibles?</h3>
	<p>
	<ul>
	<li><em>(en option)</em> number="5" - Le nombre maximal de News à afficher -- laisser ce paramètre vide affichera toutes les News.</li>
	<li><em>(en option)</em> makerssbutton="true" - Crée un bouton pour un lien à une entrée RSS feed des News.</li>
	<li><em>(en option)</em> category="category" - Affiche les News de cette catégorie uniquement -- laisser ce paramètre vide affichera toutes les News.</li>
	<li><em>(en option)</em> moretext="more..." - Texte à afficher à la fin d'une News qui dépasse la longueur définie du sommaire.  Defaut = "more...".</li>
	<li><em>(en option}</em> summarytemplate="sometemplate.tpl" - Utilise un template séparé pour l'affichage des sommaires des articles.  Il doit se trouver dans modules/News/templates.
	<li><em>(en option}</em> detailtemplate="sometemplate.tpl" - Utilise un template séparé pour l'affichage des articles détaillés.  Il doit se trouver dans modules/News/templates.
	<li><em>(en option)</em> sortasc="true" - Trie les News dans un ordre de date ascendant plutôt que descendant (descendant par défaut).</li>
	</ul>
	</p>
EOF;
?>
