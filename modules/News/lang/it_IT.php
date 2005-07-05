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
$lang['dateformat'] = '%s non &egrave; nel formato valido yyyy-mm-dd hh:mm:ss';
$lang['delete'] = 'Cancella';
$lang['description'] = 'Aggiungi, modifica e rimuovi News';
$lang['detailtemplate'] = 'Dettaglio Template';
$lang['displaytemplate'] = 'Visualizza Template';
$lang['edit'] = 'Modifica';
$lang['enddate'] = 'Fine Data';
$lang['endrequiresstart'] = 'Inserire una data di fine richiede anche una data di inizio';
$lang['entries'] = '%s News';
$lang['expiry'] = 'Scade';
$lang['filter'] = 'Filtra';
$lang['more'] = 'Altre';
$lang['moretext'] = 'Altro Testo';
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
$lang['summary'] = 'Sommario';
$lang['summarytemplate'] = 'Sommario Template';
$lang['title'] = 'Titolo';
$lang['useexpiration'] = 'Usa la data di Scadenza';
$lang['help'] = <<<EOF
	<h3>Che cosa fa?</h3>
	<p>News &egrave; un modulo per visualizzare eventi sulla tua pagina, simile allo stile blog, solo con pi&ugrave; funzionalit&agrave;!.  Quando il module &egrave; installato, in amministrazione una pagina News &egrave; aggiunta in fondo al menu che vi permette di selezionare o aggiungere una categoria.  Quando una categoria &egrave; creata o selezionata, sar&agrave; visualizzata una lista di news per quella categoria.  Da qui, potete aggiungere, modificare o cancellare news per quella categoria.</p>
	<h3>Sicurezza</h3>
	<p>L'utente deve appartenere a un gruppo con il permesso 'Modifica News' per aggiungere, modificare, o cancellare News.</p>
	<h3>Come usarlo?</h3>
	<p>Il modo pi&ugrave; semplice di usarlo &egrave; congiuntamente con il tag cms_module.  Inseririsci il modulo dove vuoi nel vostro template o pagina, questo visualizzer&agrave; le news.  Esempio di sintassi: <code>{cms_module module="news" number="5" category="beer"}</code></p>
	<h3>Quali parametri ci sono?</h3>
	<p>
	<ul>
	<li><em>(optional)</em> number="5" - Numero massimo di news da visualizzare =- lasciandolo VUOTO visualizza tutte le news</li>
	<li><em>(optional)</em> makerssbutton="true" - Fai un bottone per linkare ad un RSS feed delle News.</li>
	<li><em>(optional)</em> category="category" - Visualizza solo le news per quella categoria e i suoi discendenti.  Lasciandolo VUOTO visualizza tutte le categorie</li>
	<li><em>(optional)</em> moretext="more..." - Testo da visualizzare alla fine delle news se va oltre la lunghezza del Sommario.  Default come "more...".</li>
	<li><em>(optional}</em> summarytemplate="sometemplate.tpl" - Usa un template separato per visualizzare il Sommario dell'articolo.  Deve essere in modules/News/templates.
	<li><em>(optional}</em> detailtemplate="sometemplate.tpl" - Usa un template separato per visualizzare il dettaglio dell'article.  Deve essere in modules/News/templates.
	<li><em>(optional)</em> sortby="news_date" - Ordinati per Campo.  Opzioni sono: "news_date", "summary", "news_data", "news_category", "news_title".  Defaults to "news_date".</li>
	<li><em>(optional)</em> sortasc="true" - Ordina le news in data crescente piuttosto che discendente.</li>
	</ul>
	</p>
EOF;
?>
