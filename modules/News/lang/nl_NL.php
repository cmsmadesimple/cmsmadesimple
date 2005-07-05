<?php
$lang['addarticle'] = 'Voeg artikel toe';
$lang['addcategory'] = 'Voeg categorie toe';
$lang['addnewsitem'] = 'Voeg nieuwsitem toe';
$lang['allcategories'] = 'Alle categorieën';
$lang['allentries'] = 'Alle toevoegingen';
$lang['areyousure'] = 'Weet u zeker dat u wilt deleten?';
$lang['articles'] = 'Artikelen';
$lang['cancel'] = 'Annuleer';
$lang['category'] = 'Categorie';
$lang['categories'] = 'Categorieën';
$lang['content'] = 'Inhoud';
$lang['dateformat'] = '%s niet in een geldig yyyy-mm-dd hh:mm:ss formaat';
$lang['delete'] = 'Verwijder';
$lang['description'] = 'Voeg toe, verander en verwijder nieuwsitems';
$lang['detailtemplate'] = 'Details Template';
$lang['displaytemplate'] = 'Bekijk Template';
$lang['edit'] = 'Verander';
$lang['enddate'] = 'Eind datum';
$lang['endrequiresstart'] = 'Het toevoegen van een einddatum vereist ook een begindatum';
$lang['entries'] = '%s Items';
$lang['expiry'] = 'Expriry';
$lang['filter'] = 'Filter';
$lang['more'] = 'Meer';
$lang['moretext'] = 'Meer tekst';
$lang['name'] = 'Naam';
$lang['news'] = 'Nieuws';
$lang['newcategory'] = 'Nieuwe categorie';
$lang['needpermission'] = 'U hebt \'%s\' rechten nodig om deze functie te gebruiken.';
$lang['nocategorygiven'] = 'Geen categorie ingevuld';
$lang['nocontentgiven'] = 'Geen inhoud ingevuld';
$lang['noitemsfound'] = '<strong>Geen</strong> items gevonden voor de categorie: %s';
$lang['nopostdategiven'] = 'Geen post datum ingevuld';
$lang['note'] = '<em>Opgelet:</em> Datums in het volgende formaat opgeven \'yyyy-mm-dd hh:mm:ss\' .';
$lang['notitlegiven'] = 'Geen titel ingevuld';
$lang['numbertodisplay'] = 'Aantal weer te geven (leeg is alles weergeven)';
$lang['print'] = 'Afdrukken';
$lang['postdate'] = 'Post Datum';
$lang['postinstall'] = 'Zorg ervoor om de toestemming "wijzigen van het Nieuws" op gebruikers te plaatsen die de punten van het Nieuws zullen beheren.';
$lang['rsstemplate'] = 'RSS Template';
$lang['selectcategory'] = 'Selecteer categorie';
$lang['sortascending'] = 'Sorteer Ascending';
$lang['startdate'] = 'Begin datum';
$lang['startrequiresend'] = 'Het toevoegen van een begindatum vereist ook een einddatum';
$lang['submit'] = 'Submit';
$lang['summary'] = 'Summary';
$lang['summarytemplate'] = 'Summary Template';
$lang['title'] = 'Titel';
$lang['useexpiration'] = 'Use Expiration Date';
$lang['help'] = <<<EOF
	<h3>Wat dit doet?</h3>
	<p>News is a module for displaying news events on your page, similar to a blog style, except with more features!.  When the module is installed, a News admin page is added to the bottom menu that will allow you to select or add a news category.  Once a news category is created or selected, a list of news items for that category will be displayed.  From here, you can add, edit or delete news items for that category.</p>
	<h3>Security</h3>
	<p>The user must belong to a group with the 'Modify News' permission in order to add, edit, or delete News entries.</p>
	<h3>Hoe gebruik ik het?</h3>
	<p>The easiest way to use it is in conjunction with the cms_module tag.  This will insert the module into your template or page anywhere you wish, and display news items.  The code would look something like: <code>{cms_module module="news" number="5" category="beer"}</code></p>
	<h3>What Parameters Exist?</h3>
	<p>
	<ul>
	<li><em>(optional)</em> number="5" - Maximum number of items to display =- leaving empty will show all items</li>
	<li><em>(optional)</em> makerssbutton="true" - Make a button to link to an RSS feed of the News items.</li>
	<li><em>(optional)</em> category="category" - Only display items for that category and it's children.  leaving empty, will show all categories</li>
	<li><em>(optional)</em> moretext="more..." - Text to display at the end of a news item if it goes over the summary length.  Defaults to "more...".</li>
	<li><em>(optional}</em> summarytemplate="sometemplate.tpl" - Use a separate template for displaying the article summary.  It have to live in modules/News/templates.
	<li><em>(optional}</em> detailtemplate="sometemplate.tpl" - Use a separate template for displaying the article detail.  It have to live in modules/News/templates.
	<li><em>(optional)</em> sortasc="true" - Sort news items in ascending date order rather than descending.</li>
	</ul>
	</p>
EOF;
?>
