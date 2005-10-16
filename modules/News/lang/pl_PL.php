<?php
$lang['addarticle'] = 'Dodaj Artykuł';
$lang['addcategory'] = 'Dodaj kategorię';
$lang['addnewsitem'] = 'Dodaj element Artykułu';
$lang['allcategories'] = 'Wszystkie kategorie';
$lang['allentries'] = 'Wszystkie wpisy';
$lang['areyousure'] = 'Czy na pewno chcesz skasować?';
$lang['articles'] = 'Artykuły';
$lang['cancel'] = 'Anuluj';
$lang['category'] = 'Kategoria';
$lang['categories'] = 'Kategorie';
$lang['content'] = 'Zawartość';
$lang['dateformat'] = '%s nieprawidłowy format yyyy-mm-dd hh:mm:ss';
$lang['delete'] = 'Skasuj';
$lang['description'] = 'Dodaj, edytuj i usuń Artykuł';
$lang['detailtemplate'] = 'Szablon całego Artykułu';
$lang['displaytemplate'] = 'Wyświetl szablon';
$lang['edit'] = 'Edytuj';
$lang['enddate'] = 'Data końcowa';
$lang['endrequiresstart'] = 'Wpisanie daty końcowej wymusza wpisanie również daty początkowej';
$lang['entries'] = '%s wpisów';
$lang['expiry'] = 'Termin ważności';
$lang['filter'] = 'Filtr';
$lang['more'] = 'Więcej';
$lang['moretext'] = 'Więcej tekstu';
$lang['name'] = 'Nazwa';
$lang['news'] = 'Artykuły';
$lang['newcategory'] = 'Nowa kategoria';
$lang['needpermission'] = 'Musisz mieć \'%s\' uprawnienia by wykonać tą operację.';
$lang['nocategorygiven'] = 'Brak kategorii';
$lang['nocontentgiven'] = 'Brak zawartości';
$lang['noitemsfound'] = '<strong>Brak</strong> znalezionych elementów dla kategorii: %s';
$lang['nopostdategiven'] = 'Brak daty zamieszczenia';
$lang['note'] = '<em>Uwaga:</em> Data musi być w formacie: \'yyyy-mm-dd hh:mm:ss\'.';
$lang['notitlegiven'] = 'brak tytułu';
$lang['numbertodisplay'] = 'Liczba do wyświetlenia (pusty pokazuje wszystkie rekordy)';
$lang['print'] = 'Drukuj';
$lang['postdate'] = 'Data zamieszczenia';
$lang['postinstall'] = 'Upewnij się by ustawić uprawnienia "Modyfikacja Aktualności" dla użytkownika, który będzie administrował elementami Aktualności.';
$lang['rsstemplate'] = 'Szablon RSS';
$lang['selectcategory'] = 'Wybierz kategorię';
$lang['sortascending'] = 'Sortuj rosnąco';
$lang['startdate'] = 'Data początkowa';
$lang['startrequiresend'] = 'Wpisanie daty początkowej wymusza wpisanie również daty końcowej';
$lang['submit'] = 'Dodaj';
$lang['summary'] = 'Podsumowanie';
$lang['summarytemplate'] = 'Szablon Podsumowania';
$lang['title'] = 'Tytuł';
$lang['useexpiration'] = 'Użyj daty wygaśnięcia';
$lang['help'] = <<<EOF
	<h3>Jak to działa?</h3>
	<p>Aktualności to moduł do wyświetlania najświeższych informacji na twojej stronie. Kiedy moduł jest zainstalowany, Panel Administracyjny z Aktualnościami zostaje dodany do menu, co pozwala na wybranie lub dodawanie kategorii Aktualności. Kiedy kategoria Aktualności zostanie stworzona lub wybrana, lista elementów dla danej kategorii zostanie wyświetlona.  W tym miejscu możesz dodawać, edytować lub kasować elementy dla danej kategorii.</p>
	<h3>Bezpieczeństwo</h3>
	<p>Użytkownik musi należeć do grupy z uprawnieniami 'Modyfikacja Aktualności' żeby móc dodawać, edytować lub usuwać Aktualności.</p>
	<h3>Jak ma tego używać?</h3>
	<p>Najprostszym sposobem jest połączenie tego z modułem cms_module tag.  Spowoduje to zaimportowanie modułu do twojego szablonu lub strony, gdziekolwiek chesz oraz wyświetlenie elementów aktualności.  Kod powiniem wyglądać mniej więćej tak: <code>{cms_module module="news" number="5" category="beer"}</code></p>
	<h3>Jakie istnieją parametry?</h3>
	<p>
	<ul>
	<li><em>(optional)</em> number="5" - Maksymalna liczba elementów do wyświetlania =- puste pole spowoduje wyświetlenie wszystkich elementów</li>
	<li><em>(optional)</em> makerssbutton="true" - Stwórz przycisk w celu uzyskania połączenia z kanałem RSS.</li>
	<li><em>(optional)</em> category="category" - Wyświetla tylko elementy tylko dla tej kategorii oraz dla jej dzieci. Puste pole spowoduje wyświetlenie wszystkich elementów</li>
	<li><em>(optional)</em> moretext="more..." - Tekst, który będzie wyświetlany na końcu tekstu, jeśli tekst Aktualności będzie zbyt długi. Domyślnie "więcej...".</li>
	<li><em>(optional}</em> summarytemplate="sometemplate.tpl" - Użyj oddzielnego szablonu by wyświetlić podsumowanie Aktualności.  Musi to zostać uwzględnione modules/News/templates.
	<li><em>(optional}</em> detailtemplate="sometemplate.tpl" - Użyj oddzielnego szablonu by wyświetlić całą Aktualność. Musi to zostać uwzględnione modules/News/templates.
	<li><em>(optional)</em> sortby="news_date" - Pole służące do sortowania.  Opcje: "news_date", "summary", "news_data", "news_category", "news_title".  Domyślnie "news_date".</li>
	<li><em>(optional)</em> sortasc="true" - Sortuj elementy Aktualności raczej rosnąco według dat niż malejąco.</li>
	</ul>
	</p>
EOF;
?>
