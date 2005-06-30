<?php
$lang['addarticle'] = 'Tilføj artikel';
$lang['addcategory'] = 'Tilføj kategori';
$lang['addnewsitem'] = 'Tilføj nyhed';
$lang['allcategories'] = 'Alle katagorier';
$lang['allentries'] = 'Alle indlæg';
$lang['areyousure'] = 'Er du sikker på dette skal slettes?';
$lang['articles'] = 'Artikler';
$lang['cancel'] = 'Fortryd';
$lang['category'] = 'Kategori';
$lang['categories'] = 'Kategorier';
$lang['content'] = 'Indhold';
$lang['dateformat'] = '%s er ikke gyldigt ifølge åååå-mm-dd tt:mm:ss formatet';
$lang['delete'] = 'Slet';
$lang['description'] = 'Tilføj, redigér og slet nyheder';
$lang['detailtemplate'] = 'Detaljeret skabelon';
$lang['displaytemplate'] = 'Vis skabelon';
$lang['edit'] = 'Redigér';
$lang['enddate'] = 'Slut dato';
$lang['endrequiresstart'] = 'Angivelse af en slutdato kræver en startdato';
$lang['entries'] = '%s Indlæg';
$lang['expiry'] = 'Udløb';
$lang['filter'] = 'Filter';
$lang['more'] = 'Mere';
$lang['moretext'] = 'Mere tekst';
$lang['name'] = 'Navn';
$lang['news'] = 'Nyheder';
$lang['newcategory'] = 'Ny kategori';
$lang['needpermission'] = 'Du skal ha tilladelsen \'%s\' for at kunne udføre den funktion.';
$lang['nocategorygiven'] = 'Ingen kategori angivet';
$lang['nocontentgiven'] = 'Intet indhold angivet';
$lang['noitemsfound'] = '<strong>Ingen</strong> nyheder fundet for kategorien: %s';
$lang['nopostdategiven'] = 'Ingen oprettelsesdato angivet';
$lang['note'] = '<em>Bemærk:</em> Datoer skal angives i formatet: \'åååå-mm-dd tt:mm:ss\' format.';
$lang['notitlegiven'] = 'Ingen title angivet';
$lang['numbertodisplay'] = 'Antal der skal vises (blank viser alle nyheder)';
$lang['print'] = 'Udskriv';
$lang['postdate'] = 'Oprettelsesdato';
$lang['postinstall'] = 'Kontrollér at tilladelsen "Modify News" er slået til for brugere der skal kunne administrere Nyheder.';
$lang['rsstemplate'] = 'RSS Skabelon';
$lang['selectcategory'] = 'Vælg kategori';
$lang['sortascending'] = 'Sortér stigende';
$lang['startdate'] = 'Start dato';
$lang['startrequiresend'] = 'Angivelse af en start dato kræver en slutdato';
$lang['submit'] = 'Send';
$lang['summarytemplate'] = 'Resumé skabelon';
$lang['title'] = 'Titel';
$lang['help'] = <<<EOF
	<h3>Hvad gør dette modul?</h3>
	<p>News er et modul til at vise nyheder på din side, på sammen måde som blogs, men med flere funktioner! Når modulet er installeret er der tilføjet et News admin menupunkt til administrations menuen som giver dig mulighed for at vælge eller tilføje en nyheds kategori. Så snart en kategori er tilføjet eller valgt vises en liste over nyheder for denne katagori. Herfra kan du tilføjet, redigere eller slette nyheder for kategorien.</p>
	<h3>Sikkerhed</h3>
	<p>Brugeren skal tilhøre en gruppe med 'Modify News' tilladelsen for at kunne tilføje, redigere og slette nyheder.</p>
	<h3>Hvordan bruger jeg modulet?</h3>
	<p>Den nemmeste måde er at bruge modulet ved hjælpet af cms_module tag'en. Dette vil indsætte modulet i din skabelon eller siger lige hvor du vil ha det, og vise nyheder her. Koden ser ud som noget i denne stil: <code>{cms_module module="news" number="5" category="øl"}</code></p>
	<h3>Hvilke parametre findes der?</h3>
	<p>
	<ul>
	<li><em>(valgfrit)</em> number="5" - Det maksimale antal nyhder der skal vises - hvis der ikke angives noget vises alle nyheder</li>
	<li><em>(valgfrit)</em> makerssbutton="true" - Vis en knap med et link til RSS feed'et med nyhederne.</li>
	<li><em>(valgfrit)</em> category="kategori" - Vis kun nyheder for denne katagori og dens "børn". Hvis intet angives vises alle kategorier.</li>
	<li><em>(valgfrit)</em> moretext="mere..." - Teksten der skal vises ved slutningen af en nyhed, hvis længden overstiger resumé længden. Standard er "more...".</li>
	<li><em>(valgfrit}</em> summarytemplate="minskabelon.tpl" - Brug en brugerskabelon til at vise nyhedens resumé. Filen skal befinde sig i modules/News/templates.
	<li><em>(valgfrit}</em> detailtemplate="minskabelon.tpl" - Brug en brugerskabelon til at vise hele nyheden. Filen skal befinde sig i modules/News/templates.
	<li><em>(valgfrit)</em> sortby="news_date" - Felt det skal sorteres på.  Mulige værdier er: "news_date", "summary", "news_data", "news_category", "news_title".  Defaults to "news_date".</li>
	<li><em>(valgfrit)</em> sortasc="true" - Sortér nyheder i stigende rækkefølge i stedet for i faldende.</li>
	</ul>
	</p>
EOF;
?>
