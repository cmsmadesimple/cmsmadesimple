<?php
$lang['addarticle'] = 'Ajouter un article';
$lang['addcategory'] = 'Ajouter une catégorie';
$lang['addnewsitem'] = 'Ajouter un article';
$lang['allcategories'] = 'Toutes les catégories';
$lang['allentries'] = 'Toutes les entrées';
$lang['areyousure'] = 'Etes-vous sûr de vouloir effacer?';
$lang['articles'] = 'Articles';
$lang['cancel'] = 'Annuler';
$lang['category'] = 'Catégorie';
$lang['categories'] = 'Catégories';
$lang['content'] = 'Contenu';
$lang['dateformat'] = '%s pas dans un format valide yyyy-mm-dd hh:mm:ss';
$lang['delete'] = 'Effacer';
$lang['description'] = 'Ajout, édition et effacement des articles de News';
$lang['detailtemplate'] = 'Template de l\'article détaillé';
$lang['displaytemplate'] = 'Afficher le template';
$lang['edit'] = 'Editer';
$lang['enddate'] = 'Date de fin';
$lang['endrequiresstart'] = 'Entrer une date de fin nécessite qu\'une date de début soit également entrée';
$lang['entries'] = '%s entrées';
$lang['status'] = 'Status';
$lang['expiry'] = 'Expiration';
$lang['filter'] = 'Filtre';
$lang['more'] = 'Plus';
$lang['moretext'] = 'Texte pour "plus"';
$lang['name'] = 'Nom';
$lang['news'] = 'News';
$lang['newcategory'] = 'Nouvelle catégorie';
$lang['needpermission'] = 'Vous devez avoir la permission \'%s\' pour exécuter cette action.';
$lang['nocategorygiven'] = 'Aucune catégorie entrée';
$lang['nocontentgiven'] = 'Aucun contenu entré';
$lang['noitemsfound'] = '<strong>Aucun</strong> objet trouvé pour cette catégorie: %s';
$lang['nopostdategiven'] = 'Il manque la date à laquelle l\'article sera posté';
$lang['note'] = '<em>Note:</em> Les dates doivent être entrées dans ce format \'yyyy-mm-dd hh:mm:ss\'.';
$lang['notitlegiven'] = 'Aucun titre entré';
$lang['numbertodisplay'] = 'Nombre à afficher (vide = toutes les entrées)';
$lang['print'] = 'Imprimer';
$lang['postdate'] = 'Date à laquelle l\'article sera posté';
$lang['postinstall'] = 'Assurez-vous que vous définissez la permission "Modify News" sur les utilisateurs qui administeront les articles.';
$lang['rsstemplate'] = 'RSS Template';
$lang['selectcategory'] = 'Sélection de catégorie';
$lang['showchildcategories'] = 'Afficher les sous-catégories';
$lang['sortascending'] = 'Tri ascendant';
$lang['startdate'] = 'Date de début';
$lang['startrequiresend'] = 'Entrer une date de début nécessite qu\'une date de fin soit également entrée';
$lang['submit'] = 'Envoyer';
$lang['summary'] = 'Sommaire';
$lang['summarytemplate'] = 'Template du sommaire de l\'article';
$lang['title'] = 'Titre';
$lang['options'] = 'Options';
$lang['useexpiration'] = 'Utiliser la date d\'expiration';
$lang['showautodiscovery'] = 'Show Feed Auto-Discovery Link';
$lang['autodiscoverylink'] = 'Feed Auto-Discovery URL (blank for default)';
$lang['helpnumber'] = 'Le nombre maximal d\'articles à afficher -- laisser ce paramètre vide affichera tous les articles.';
$lang['helpmakerssbutton'] = 'Affiche un bouton pour créer un flux RSS des articles.';
$lang['helpcategory'] = 'Affiche les articles de cette catégorie seulement. Utiliser * pour afficher les sous-catégories. Des catégories multiples peuvent être affichées en les séparant par une virgule. Laisser ce paramètre vide affichera tous les articles.';
$lang['helpmoretext'] = 'Texte à afficher à la fin d\'un article qui dépasse la longueur définie du sommaire.  Défaut = "more..."';
$lang['helpsummarytemplate'] = 'Utilise un template séparé pour l\'affichage du sommaire des articles.  Il doit se trouver dans modules/News/templates.';
$lang['helpdetailtemplate'] = 'Utilise un template séparé pour l\'affichage du détail des articles.  Il doit se trouver dans modules/News/templates.';
$lang['helpsortby'] = 'Champ sur lequel trier les articles.  Les options sont: "news_date", "summary", "news_data", "news_category", "news_title".  Par défaut: "news_date".';
$lang['helpsortasc'] = 'Trie les articles dans un ordre de date ascendant plutôt que descendant. Par défaut: descendant.';
$lang['helpdateformat'] = 'Format d\'affichage de la date de l\'article.  Ceci est basé sur la fonction <a href="http://php.net/strftime" target="_blank">strftime</a> et peut être utilisé dans votre template avec $entry->formatpostdate.';
$lang['help'] = <<<EOF
	<h3>Que fait ce module?</h3>
	<p>News est un module pour afficher des articles de News sur vos pages, de façon similaire à un blog, mais avec plus de fonctions!  Dès que le module est installé, une page de gestion des News est ajoutée au menu d'administration qui vous permettra de sélectionner ou ajouter des catégories d'articles.  Dès qu'une catégorie d'article est sélectionnée ou créée, une liste des articles pour cette catégorie est affichée.  Depuis là, vous pouvez ajouter, éditer ou effacer les articles dans cette catégorie.</p>
	<h3>Securité</h3>
	<p>L'utilisateur doit faire partie d'un groupe avec la permission 'Modify News' pour pouvoir ajouter, éditer ou effacer des articles.</p>
	<h3>Comment l'utiliser?</h3>
	<p>La façon la plus facile de l'utiliser est en conjonction avec le tag cms_module.  Cela insérera votre module dans votre template ou votre page page où vous le désirez, et y affichera les articles.  Exemple de synthaxe: <code>{cms_module module="news" number="5" category="beer"}</code></p>
EOF;
?>

