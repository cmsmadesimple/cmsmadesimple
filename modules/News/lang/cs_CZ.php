<?php
$lang['addarticle'] = 'Vložit novinku';
$lang['addcategory'] = 'Vložit kategorii';
$lang['addnewsitem'] = 'Vložit novinku';
$lang['allcategories'] = 'Všechny kategorie';
$lang['allentries'] = 'Všechny položky';
$lang['areyousure'] = 'Opravdu chcete smazat?';
$lang['articles'] = 'Novinky';
$lang['cancel'] = 'Storno';
$lang['category'] = 'Kategorie';
$lang['categories'] = 'Kategorie';
$lang['content'] = 'Obsah';
$lang['dateformat'] = '%s není ve správném yyyy-mm-dd hh:mm:ss formátu';
$lang['delete'] = 'Smazat';
$lang['description'] = 'Vložit, upravit nebo smazat novinky';
$lang['detailtemplate'] = 'Šablona detailu';
$lang['displaytemplate'] = 'Display Template';
$lang['edit'] = 'Upravit';
$lang['enddate'] = 'Končí';
$lang['endrequiresstart'] = 'Vložení data konce potřebuje také datum počátku';
$lang['entries'] = '%s položek';
$lang['expiry'] = 'Vyprší';
$lang['filter'] = 'Filtr';
$lang['more'] = 'Více';
$lang['moretext'] = 'Více textu';
$lang['name'] = 'Jméno';
$lang['news'] = 'Novinky';
$lang['newcategory'] = 'Nová kategorie';
$lang['needpermission'] = 'Potřebujete \'%s\' oprávnění pro provedení této funkce.';
$lang['nocategorygiven'] = 'Kategorie nezadána';
$lang['nocontentgiven'] = 'Obsah nezadán';
$lang['noitemsfound'] = '<strong>Žádné</strong> položky nenalezeny v kategorii: %s';
$lang['nopostdategiven'] = 'Datum vložení nezadáno';
$lang['note'] = '<em>Poznámka:</em> Data musí být v \'yyyy-mm-dd hh:mm:ss\' formátu.';
$lang['notitlegiven'] = 'Nadpis nezadán';
$lang['numbertodisplay'] = 'Počet k zobrazení (prázdné zobrazí všechny záznamy)';
$lang['print'] = 'Tisknout';
$lang['postdate'] = 'Datum vložení';
$lang['postinstall'] = 'Nastavte "Modify News" oprávnění uživatelům, kteří budou spravovat novinky.';
$lang['rsstemplate'] = 'RSS šablona';
$lang['selectcategory'] = 'Vybrat kategorii';
$lang['sortascending'] = 'Třídit vzestupně';
$lang['startdate'] = 'Začíná';
$lang['startrequiresend'] = 'Vložení data začátku potřebuje také datum ukončení';
$lang['submit'] = 'Odeslat';
$lang['summary'] = 'Souhrn';
$lang['summarytemplate'] = 'Šablona souhrnu';
$lang['title'] = 'Nadpis';
$lang['useexpiration'] = 'Použít datum konce';
$lang['help'] = <<<EOF
	<h3>Co dělá tento modul?</h3>
	<p>Novinky jsou modul pro zobrazování událostí na Vašich stránkách, podobně jako blogové zápisy, pouze s více možnostmi! Po instalaci modulu je vložena další položka do administračního menu a to Vám umožní vybrat nebo vložit kategorii novinek. Po vytvoření nebo zvolení kategorie je zobrazen seznam novinek pro tuto kategorii. Odsud můžete přidávat, upravovat nebo mazat novinky dané kategorie.</p>
	<h3>Bezpečnost</h3>
	<p>Aby uživatel mohl přidávat, upravovat nebo mazat novinky, musí mít oprávnění skupiny 'Modify News'.</p>
	<h3>Jak se používá?</h3>
	<p>Nejjednodušší použití je ve využití tagu cms_module. Toto vloží modul do šablony nebo stránky a zobrazí novinky jak si přejete. Samotný kó bude vypadat nějak následovně: <code>{cms_module module="news" number="5" category="beer"}</code></p>
	<h3>Jaké jsou parametry?</h3>
	<p>
	<ul>
	<li><em>(volitelné)</em> number="5" - Maximání počet položek k zobrazení =- ponecháno prázdné zobrazí všechny položky</li>
	<li><em>(volitelné)</em> makerssbutton="true" - Vytvořit tlačítko odkazující na  RSS zdroj novinek.</li>
	<li><em>(volitelné)</em> category="category" - Zobrazit položky pouze dané kategorie. Ponecháno prázdné zobrazí všechny kategorie</li>
	<li><em>(volitelné)</em> moretext="more..." - Text zobrazený na konci novinky, pokud překračuje délku souhrnu. Standardně "more...".</li>
	<li><em>(volitelné)</em> summarytemplate="sometemplate.tpl" - Použít zvláštní šablonu pro zobrazení souhrnu. Má být v modules/News/templates.
	<li><em>(volitelné)</em> detailtemplate="sometemplate.tpl" - Použít zvláštní šablonu pro zobrazení detailu.  Má být v modules/News/templates.
	<li><em>(volitelné)</em> sortby="news_date" - Pole pro třídění. Volby jsou: "news_date", "summary", "news_data", "news_category", "news_title".  Základní je "news_date".</li>
	<li><em>(volitelné)</em> sortasc="true" - Seřadit novinky vzestupně místo sestupně.</li>
	</ul>
	</p>
EOF;
?>
