<?php
$lang['addarticle'] = 'Lis‰‰ Artikkeli';
$lang['addcategory'] = 'Lis‰‰ Aihe';
$lang['addnewsitem'] = 'Lis‰‰ uutis otsikko';
$lang['allcategories'] = 'Kaikki Aiheet';
$lang['allentries'] = 'Kaikki Merkinn‰t'; 
$lang['areyousure'] = 'Oletko varma ett‰ haluat poistaa?';
$lang['articles'] = 'Artikkelit';
$lang['cancel'] = 'Peruuta';
$lang['category'] = 'Aihe';
$lang['categories'] = 'Aiheet';
$lang['content'] = 'Sis‰ltˆ';
$lang['dateformat'] = '%s not in a valid yyyy-mm-dd hh:mm:ss format';
$lang['delete'] = 'Poista';
$lang['description'] = 'Lis‰‰, muokkaa ja poista Uutisaiheita';
$lang['detailtemplate'] = 'Yksityiskohtainen mallipohja';
$lang['displaytemplate'] = 'N‰yt‰ mallipohja';
$lang['edit'] = 'Muokkaa';
$lang['enddate'] = 'Muokkaa P‰iv‰m‰‰r‰';
$lang['endrequiresstart'] = 'Jos syˆt‰t loppup‰iv‰m‰‰r‰n, sinun tulee syˆtt‰‰ myˆs aloitus';
$lang['entries'] = '%s Entries';
$lang['expiry'] = 'Er‰‰nty‰'; // EDIT
$lang['filter'] = 'Suodatin';
$lang['more'] = 'Lis‰‰';
$lang['moretext'] = 'Lis‰‰ Teksti‰';
$lang['name'] = 'Nimi';
$lang['news'] = 'Uutiset';
$lang['newcategory'] = 'Uutis Aihe';
$lang['needpermission'] = 'Tarvitset \'%s\' oikeudet jotta voit k‰ytt‰‰ t‰t‰ toimintoa.';
$lang['nocategorygiven'] = 'Et antanut aihetta';
$lang['nocontentgiven'] = 'Et antanut sis‰ltˆ‰';
$lang['noitemsfound'] = 'Aiheessa %s <strong>EI</strong> lˆytynyt merkintˆj‰'; 
$lang['nopostdategiven'] = 'Et antanut p‰iv‰m‰‰r‰‰';
$lang['note'] = '<em>Huom:</em> p‰iv‰m‰‰r‰t pit‰‰ syˆtt‰‰ seuraavassa formaatissa: \'yyyy-mm-dd hh:mm:ss\'.'; 
$lang['notitlegiven'] = 'Et Antanut Otsikkoa'; 
$lang['numbertodisplay'] = 'Kuinka monta n‰ytet‰‰n (tyhj‰ kentt‰ n‰ytt‰‰ kaikki';
$lang['print'] = 'Tulosta'; 
$lang['postdate'] = 'Kirjoitus p‰iv‰m‰‰r‰'; 
$lang['postinstall'] = 'Muista asettaa kaikille k‰ytt‰jille joille haluat oikeuden muokata uutisia oikeus nimelt‰ "Modify News".'; 
$lang['rsstemplate'] = 'RSS Mallipohja'; 
$lang['selectcategory'] = 'Valitse Aihe';
$lang['sortascending'] = 'J‰rjest‰ nousevassa j‰rjestyksess‰';
$lang['startdate'] = 'Aloitus P‰iv‰m‰‰r‰'; 
$lang['startrequiresend'] = 'Jos syˆt‰t aloitus p‰iv‰m‰‰r‰n, syˆt‰ myˆs lopetus';
$lang['submit'] = 'L‰het‰'; 
$lang['summary'] = 'Yhteenveto'; 
$lang['summarytemplate'] = 'Yhteenvedon mallipohja';
$lang['title'] = 'Otsikko';
$lang['useexpiration'] = 'Ota vanhenemisp‰iv‰m‰‰r‰ k‰yttˆˆn';
$lang['help'] = <<<EOF
<h3>Mit‰ t‰m‰ tekee?</h3>
<p>Uutiset moduuli n‰ytt‰‰ p‰iv‰m‰‰r‰‰n sidottuja tapahtumia sivullasi, v‰h‰n niinkuin blog eli verkkop‰iv‰kirja. Kun t‰m‰ moduuli on asennettu, "Uutiset" yll‰pito sivu tulee automaattisesti yll‰pito valikkoon. T‰lt‰ sivulta voit lis‰t‰ tai valita uutis aiheita. Kun olet lis‰nnyt tai valinnut aiheen, lista uutisista valitusta aiheesta n‰ytet‰‰n. T‰st‰ osasta voit joko lis‰t‰, muokata tai poistaa itse uutisia.</p>

<h3>Turvallisuus</h3>
<p>K‰ytt‰j‰ jolla tulee olla oikeis hallita uutisia, tulee kuulua ryhm‰‰n 'Modify News'</p>

<h3>Kuinka t‰t‰ moduulia k‰ytet‰‰n/uutiset tulostetaan?</h3>

<p>Lis‰‰ sivuillesi, esim. mallipohjaa cms_module avainsana. T‰m‰ lis‰‰ moduulin tulosteen suoraan sivullesi. Koodi
jonka lis‰‰t tulee n‰ytt‰‰ esim t‰lt‰: <code>{cms_module module="news" number="5" category="beer"}</code></p>

<h3>Mit‰ parametrej‰ voin antaa moduulille?</h3>
<p>
<ul>
<li><em>(optional)</em> number="5" - N‰yt‰ X m‰‰r‰ uutisia, jos t‰t‰ parametri‰ ei ole annettu, moduuli n‰ytt‰‰ kaikki uutiset</li>
<li><em>(optional)</em> makerssbutton="true" - Lis‰‰ RSS linkki nappi</li>
<li><em>(optional)</em> category="kategoriaXYZ" - Moduuli tulostaa vain valittuun aiheeseen kuuluvat uutiset. Jos kentt‰ on tyhj‰ tai sit‰ ei ole m‰‰rritelty, uutiset n‰ytet‰‰n kaikista aihepiireist‰</li>
<li><em>(optional)</em> moretext="more..." - Teksti joka tulostetaan jos itse uutinen ei mahdu kokonaisuudessaan yhteenveto kappaleeseen.</li>
<li><em>(optional}</em> summarytemplate="sometemplate.tpl" - K‰yt‰ t‰t‰ yhteenveto mallipohjaa yhteenvedon tulostukseen.  Mallipohja pit‰‰ olla erillisess‰ tiedostossa modules/News/templates hakemistossa.</li>
<li><em>(optional}</em> detailtemplate="sometemplate.tpl" - K‰yt‰ t‰t‰ mallipohjaa koko uutisen tulostukseen. Mallipohja pit‰‰ olla erillisess‰ tiedostossa modules/News/templates hakemistossa</li>
<li><em>(optional)</em> sortby="news_date" - Kentt‰ jonka mukaan uutiset j‰rjestet‰‰n. Vaihtoehdot ovat seuraavat: "news_date", "summary", "news_data", "news_category", "news_title".  Oletus on "news_date".</li>
<li><em>(optional)</em> sortasc="true" - J‰rjest‰ uutiset nousevaan j‰rjestykseen. </li>
</ul>
</p>
EOF;
?>
