<?php
$lang['allentries'] = 'Alle oppføringer';
$lang['addnewsitem'] = 'Legg til artikkel';
$lang['cancel'] = 'Avbryt';
$lang['category'] = 'Kategori';
$lang['content'] = 'Innhold';
$lang['dateformat'] = '%s er ikke et gyldig yyyy-mm-dd hh:mm:ss format';
$lang['description'] = 'Legg til, rediger og slett artikler.';
$lang['displaytemplate'] = 'Vis nyhetsmal';
$lang['enddate'] = 'Slutt dato';
$lang['endrequiresstart'] = 'Dersom du har en slutt dato for artikkelen så må du også ha en start dato.';
$lang['entries'] = '%s oppføringer';
$lang['expiry'] = 'Vis i';
$lang['filter'] = 'Filter';
$lang['newcategory'] = 'Ny kategori';
$lang['needpermission'] = 'Du trenger \'%s\' rettigheter for å utføre denne handlingen.';
$lang['nocategorygiven'] = 'Ingen kategori satt';
$lang['nocontentgiven'] = 'Innhold mangler';
$lang['noitemsfound'] = '<strong>Ingen</strong> artikler funnet i denne kategorien: %s';
$lang['nopostdategiven'] = 'Dato mangler';
$lang['note'] = '<em>Merk:</em> Dato må være i et \'yyyy-mm-dd hh:mm:ss\' format.';
$lang['notitlegiven'] = 'Tittel mangler';
$lang['postdate'] = 'Publiserings dato';
$lang['postinstall'] = 'Pass på å sette rettigheter for "Modify News" for brukere som skal administrere nyhets artikler.';
$lang['rsstemplate'] = 'RSS Mal';
$lang['startdate'] = 'Start Dato';
$lang['startrequiresend'] = 'Dersom du har en start dato for artikkelen så må du også ha en slutt dato.';
$lang['submit'] = 'Submit';
$lang['title'] = 'Title';
$lang['help'] = <<<EOF
	<h3>Hva gjør denne modulen?</h3>
	<p>Nyhetsmodulen viser nyheter på din side på en måte som ligner på en blog, men med mer funksjonalitet. Når modulen er installert, så vil en side for å administrere nyhtene vises i administrasjonsgrensesnittet. Her vil du kunne legge til og velge nyhetskategorier. Når en nyhetskategori er laget eller valgt, så vil en liste over artikler i denne kategorien vises. Du kan nå legge til, redigere eller slette artikler fra denne kategorien.</p>
	<h3>Sikkerhet</h3>
	<p>Brukere som skal administrere nyhetsartiklene må tilhøre en gruppe som har 'Modify News' rettigheten.</p>
	<h3>Hvordan bruker jeg modulen?</h3>
	<p>Den letteste måten å bruke modulen er med cms_module taggen. Taggen brukes enten i et innholdselement eller i en mal. Et eksempel på en bruk av modulen er <code>{cms_module module="news" number="5" category="beer"}</code></p>
	<h3>Hvilke parametere finnes?</h3>
	<p>
	<ul>
	<li><em>(optional)</em> number="5" - Maksimalt antall viste artikler =- Dersom parameteren er tom så vises alle artikler</li>
	<li><em>(optional)</em> dateformat - Dato/tids format som bruker parameterene fra php sin date funksjon. Se <a href="http://php.net/date" target="_blank">her</a> for en parameterliste og mer informasjon.</li>
	<li><em>(optional)</em> makerssbutton="true" - Lager en knapp for å linke en RSS tilførsel av nyhetsartikler.</li>
	<li><em>(optional)</em> swaptitledate="true" - Bytter om orderen på dato og tittel</li>
	<li><em>(optional)</em> category="category" - Viser bare elementer i den spesifiserte kategorien. Dersom denne parameteren er tom så vises artikler fra alle kategorier</li>
	<li><em>(optional)</em> summary="page" - Aktiverer sammendragsmodus. Dette innebærer at artikkelen blir kortet ned til antall bokstaver spesifiert med "length" parameteren, og at tittelen for hver nyhet blir laget som en link til den komplette artikkelen.</li>
	<li><em>(optional)</em> length="80" - Brukes sammen med sammendragsmodus (se over), slik at lengden på hver artikkel blir satt til det spesifiserte antall bokstaver etter at alle html tagger er fjernet.</li>
	<li><em>(optional)</em> showcategorywithtitle="true" - Viser den tilhørende kategorien før artikkel tittelen. Dersom parameteren settes til false så benyttes gammel stil.</li>
	<li><em>(optional)</em> moretext="more..." - Med denne parameteren kan du spesifisere teksten som vises på slutten av en artikkel som har blitt kortet som følge av sammendragsmodus. Standard tekst er "more...".</li>
	<li><em>(optional)</em> sortasc="true" - Sort news items in ascending date order rather than descending.</li>
	</ul>
	</p>
EOF;
?>
