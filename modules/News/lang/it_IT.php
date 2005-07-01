<?php
$lang['addarticle'] = 'Aggiungi Articolo';
$lang['addcategory'] = 'Aggiungi Categoria';
$lang['addnewsitem'] = 'Aggiungi News';
$lang['allcategories'] = 'Tutte le categorie';
$lang['allentries'] = 'Tutte le news';
$lang['areyousure'] = 'Sei sicuro di volerlo cancellare?';
$lang['articles'] = 'Articoli';
$lang['cancel'] = 'Annulla';
$lang['category'] = 'Categoria';
$lang['categories'] = 'Categorie';
$lang['content'] = 'Contenuto';
$lang['dateformat'] = '%s non &egrave; nel formato valido yyyy-mm-dd hh:mm:ss ';
$lang['delete'] = 'Cancella';
$lang['description'] = 'Aggiungi, edita e rimuovi News';
$lang['detailtemplate'] = 'Dettaglio Template';
$lang['displaytemplate'] = 'Visualizza Template';
$lang['edit'] = 'Edita';
$lang['enddate'] = 'Fine Data';
$lang['endrequiresstart'] = 'Inserire una data di fine richiede anche una data di inizio';
$lang['entries'] = '%s News';
$lang['expiry'] = 'Scade';
$lang['filter'] = 'Filtra';
$lang['more'] = 'Altre';
$lang['moretext'] = 'Maggiore Testo';
$lang['name'] = 'Nome';
$lang['news'] = 'News';
$lang['newcategory'] = 'Nuova Categoria';
$lang['needpermission'] = 'Necessiti del permesso \'%s\' per poter usufruire questa funzione.';
$lang['nocategorygiven'] = 'Categoria non specificata';
$lang['nocontentgiven'] = 'Contenuto non dato';
$lang['noitemsfound'] = '<strong>Nessuna</strong> news trovata per la categoria: %s';
$lang['nopostdategiven'] = 'No Post Date Given';
$lang['note'] = '<em>Nota:</em> La data deve essere nel formato: \'yyyy-mm-dd hh:mm:ss\'.';
$lang['notitlegiven'] = 'Titolo non dato';
$lang['numbertodisplay'] = 'Quantit&agrave; da visualizzare (vuoto mostra tutte le news)';
$lang['print'] = 'Stampa';
$lang['postdate'] = 'Data di Pubblicazione';
$lang['postinstall'] = 'Assicurati di impostare il permesso "Modify News"  sugli utenti che dovranno amministrare le News.';
$lang['rsstemplate'] = 'RSS Template';
$lang['selectcategory'] = 'Seleziona la Categoria';
$lang['sortascending'] = 'Ordine crescente';
$lang['startdate'] = 'Data inizio';
$lang['startrequiresend'] = 'Inserire una data di inizio richiede anche una data di fine';
$lang['submit'] = 'Inserisci';
$lang['summarytemplate'] = 'Sommario Template';
$lang['title'] = 'Titolo';
$lang['help'] = <<<EOF
	<h3>Cosa questo fa?</h3>
	<p>News is a module for displaying news events on your page, similar to a blog style, except with more features!.  When the module is installed, a News admin page is added to the bottom menu that will allow you to select or add a news category.  Once a news category is created or selected, a list of news items for that category will be displayed.  From here, you can add, edit or delete news items for that category.</p>
	<h3>Sicurezza</h3>
	<p>The user must belong to a group with the 'Modify News' permission in order to add, edit, or delete News entries.</p>
	<h3>Come usarlo?</h3>
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
	<li><em>(optional)</em> sortby="news_date" - Field to sort by.  Options are: "news_date", "summary", "news_data", "news_category", "news_title".  Defaults to "news_date".</li>
	<li><em>(optional)</em> sortasc="true" - Sort news items in ascending date order rather than descending.</li>
	</ul>
	</p>
EOF;
?>
