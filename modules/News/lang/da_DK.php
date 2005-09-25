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
$lang['status'] = 'Status';
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
$lang['summary'] = 'Resumé';
$lang['summarytemplate'] = 'Resumé skabelon';
$lang['title'] = 'Titel';
$lang['useexpiration'] = 'Benyt udløbsdato';
$lang['helpnumber'] = 'Det maksimale antal nyheder der skal vises -- alle nyheder vises hvis feltet er blankt.';
$lang['helpmakerssbutton'] = 'Vis en knap der er et link til en RSS-kilde for nyhederne.';
$lang['helpcategory'] = 'Vis kun nyheder for denne kategori og den underkategorier. Hvis feltet er blankt vises alle kategodier.';
$lang['helpmoretext'] = 'Tekst der skal vises efter en nyhed hvis længden af denne overskrider resumé længden. Standard er "more..."';
$lang['helpsummarytemplate'] = 'Benyt en seperat skabelon til at vise en nyhedsartikel som resumé. Den skal befinde i modules/News/templates.';
$lang['helpdetailtemplate'] = 'Benyt en seperat skabelon til at vi en nyhedsartikel i detaljer. Den skal befinde sig i modules/News/templates.';
$lang['helpsortby'] = 'Hvilket felt der skal sorteres efter. Muligheder er: "news_date", "summary", "news_data", "news_category", "news_title". Standard er "news_date".';
$lang['helpsortasc'] = 'Sortér nyheder i stigende rækkefølge i stedet for faldende.';
$lang['helpdateformat'] = 'Formatet som nyhedens post-dato skal vises med. Denne er baseret på <a href="http://php.net/strftime" target="_blank">strftime</a> funktionene og kan bruges i din skabelon med $entry->formatpostdate.';
$lang['help'] = <<<EOF
	<h3>Hvad gør dette modul?</h3>
	<p>News er et modul til at vise nyheder på din side, på sammen måde som blogs, men med flere funktioner! Når modulet er installeret er der tilføjet et News admin menupunkt til administrations menuen som giver dig mulighed for at vælge eller tilføje en nyheds kategori. Så snart en kategori er tilføjet eller valgt vises en liste over nyheder for denne katagori. Herfra kan du tilføjet, redigere eller slette nyheder for kategorien.</p>
	<h3>Sikkerhed</h3>
	<p>Brugeren skal tilhøre en gruppe med 'Modify News' tilladelsen for at kunne tilføje, redigere og slette nyheder.</p>
	<h3>Hvordan bruger jeg modulet?</h3>
	<p>Den nemmeste måde er at bruge modulet ved hjælpet af cms_module tag'en. Dette vil indsætte modulet i din skabelon eller siger lige hvor du vil ha det, og vise nyheder her. Koden ser ud som noget i denne stil: <code>{cms_module module="news" number="5" category="øl"}</code></p>	
	</p>
EOF;
?>
