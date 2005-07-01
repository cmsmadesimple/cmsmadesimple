<?php
$lang['addarticle'] = 'Įdėti Straipsnį';
$lang['addcategory'] = 'Įdėti Kategoriją';
$lang['addnewsitem'] = 'Įdėti Naujienų Elementą';
$lang['allcategories'] = 'Visos Kategorijos';
$lang['allentries'] = 'Visi Įrašai';
$lang['areyousure'] = 'Ar tikrai norite ištrinti?';
$lang['articles'] = 'Straipsniai';
$lang['cancel'] = 'Atšaukti';
$lang['category'] = 'Kategorija';
$lang['categories'] = 'Kategorijos';
$lang['content'] = 'Turinys';
$lang['dateformat'] = '%s negalimas yyyy-mm-dd hh:mm:ss formatas';
$lang['delete'] = 'Trinti';
$lang['description'] = 'Įdėti, redaguoti ir trinti Naujienų įrašus';
$lang['detailtemplate'] = 'Detalus Šablonas';
$lang['displaytemplate'] = 'Rodyti Šabloną';
$lang['edit'] = 'Redaguoti';
$lang['enddate'] = 'Baigimosi Data';
$lang['endrequiresstart'] = 'Įvedant baigimosi datą, reikia taip pat įvesti ir pradžios datą';
$lang['entries'] = '%s Įrašai';
$lang['expiry'] = 'Galiojimas';
$lang['filter'] = 'Filtruoti';
$lang['more'] = 'Daugiau';
$lang['moretext'] = 'Daugiau teksto';
$lang['name'] = 'Vardas';
$lang['news'] = 'Naujienos';
$lang['newcategory'] = 'Nauja Kategorija';
$lang['needpermission'] = 'Jums reikia prieigos \'%s\' tam, kad atlikti šią funkciją.';
$lang['nocategorygiven'] = 'Neužpildyta Kategorija';
$lang['nocontentgiven'] = 'Neužpildytas Turinys';
$lang['noitemsfound'] = '<strong>Nerasta</strong> įrašų šioje kategorijoje: %s';
$lang['nopostdategiven'] = 'Neužpildyta įrašo pateikimo data';
$lang['note'] = '<em>Žyma:</em> Datos turi būti \'yyyy-mm-dd hh:mm:ss\' formato.';
$lang['notitlegiven'] = 'Neužpildytas Pavadinimas';
$lang['numbertodisplay'] = 'Rodomas Skaičius (palikus tuščią, rodomi visi įrašai)';
$lang['print'] = 'Spausdinti';
$lang['postdate'] = 'Įrašo data';
$lang['postinstall'] = 'Nustatykite "Redaguoti Naujienas (Modify News)" prieigą tiems vartotojams, kurie administruos naujienas.';
$lang['rsstemplate'] = 'RSS Šablonas';
$lang['selectcategory'] = 'Pasirinkite Kategoriją';
$lang['sortascending'] = 'Rūšiuoti Didėjančiai';
$lang['startdate'] = 'Pradžios Data';
$lang['startrequiresend'] = 'Įvedant pradžios datą, reikia įvesti ir pabaigos datą';
$lang['submit'] = 'Pateikti';
$lang['summary'] = 'Santrauka';
$lang['summarytemplate'] = 'Santraukos Šablonas';
$lang['title'] = 'Pavadinimas';
$lang['useexpiration'] = 'Naudoti galiojimo datą';
$lang['help'] = <<<EOF
	<h3>Ką jis daro?</h3>
	<p>Naujienų modulis rodo naujienas jūsų puslapyje, panašiu kaip 'blog' stiliumi, tik su daugiau galimybių. Kai meniu modulis įdiegtas, Naujienų administravimo puslapis yra įdedamas į meniu apačią, kuris jums leis parinkti ar pridėti naujienų kategoriją.</p>
	<h3>Saugumas</h3>
	<p>Vartotojas turi priklausyti grupei su prieiga 'Redaguoti Naujienas' ('Modify News'), tam kad galėtu pridėti, redaguoti ar trinti naujienų įrašus.</p>
	<h3>Kaip jį naudoti?</h3>
	<p>Lengviausias būdas naudojant cms_module žymę. Bus įterptas modulis į jūsų šabloną ar puslapį ir rodys naujienų įrašus. Kodas turėtų būti panašus kaip: <code>{cms_module module="news" number="5" category="beer"}</code></p>
	<h3>Kokie parametrai egzistuoja?</h3>
	<p>
	<ul>
	<li><em>(optional)</em> number="5" - Maksimalus rodomų įrašų skaičius =- palikus tuščią, bus rodomi visi įrašai</li>
	<li><em>(optional)</em> makerssbutton="true" - Sukuriamas naujienų įrašų RSSui mygtukas.</li>
	<li><em>(optional)</em> category="category" - Rodyti tik tos kategorijos įrašus ir jos vaikus. Palikus tuščią, rodomos visos kategorijos.</li>
	<li><em>(optional)</em> moretext="more..." - Tekstas, kuris rodomas kai naujiena viršiją santrauką. Nustatytas "more...".</li>
	<li><em>(optional}</em> summarytemplate="sometemplate.tpl" - Naudokite atskirą šabloną rodyti naujienų santrauką. Jį reikia sukurti modules/News/templates.
	<li><em>(optional}</em> detailtemplate="sometemplate.tpl" - Naudokite atskirą šabloną rodyti visas naujienas. Jį reikia sukurti modules/News/templates.
	<li><em>(optional)</em> sortby="news_date" - Rūšiavimo laukas. Pasirinkimai: "news_date", "summary", "news_data", "news_category", "news_title". Nustatyta: "news_date".</li>
	<li><em>(optional)</em> sortasc="true" - Rūšiuoti naujienas didėjančia tvarka.</li>
	</ul>
	</p>
EOF;
?>
