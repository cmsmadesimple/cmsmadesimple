<?php
$lang['allentries'] = 'Alle Eintr&auml;ge';
$lang['addarticle'] = 'Eintrag anlegen';
$lang['addcategory'] = 'Kategorie hinzuf&uuml;gen';
$lang['addnewsitem'] = 'Eintrag hinzuf&uuml;gen';
$lang['allcategories'] = 'Alle Kategorien';
$lang['allentries'] = 'Alle Eintr&auml;ge';
$lang['areyousure'] = 'Wirklich l&ouml;schen?';
$lang['articles'] = 'Artikel';
$lang['cancel'] = 'Abbruch';
$lang['category'] = 'Kategorie';
$lang['categories'] = 'Kategorien';
$lang['content'] = 'Inhalt';
$lang['dateformat'] = '%s ist kein g&uuml;ltiges yyyy-mm-dd hh:mm:ss Format';
$lang['delete'] = 'L&ouml;schen';
$lang['description'] = 'News-Eintr&auml;ge hinzuf&uuml;gen, bearbeiten und l&ouml;schen';
$lang['detailtemplate'] = 'Detail-Template';
$lang['displaytemplate'] = 'Zeige Template';
$lang['edit'] = 'Bearbeiten';
$lang['enddate'] = 'Verfallsdatum';
$lang['endrequiresstart'] = 'Die Angabe eines Verfallsdatums erfordert auch die Angabe eines Anfangsdatums.';
$lang['entries'] = '%s Eintr&auml;ge';
$lang['expiry'] = 'Verf&auml;llt';
$lang['filter'] = 'Filter';
$lang['more'] = 'Mehr';
$lang['moretext'] = 'Mehr-Text';
$lang['name'] = 'Name';
$lang['news'] = 'News';
$lang['newcategory'] = 'Neue Kategorie';
$lang['needpermission'] = 'Sie ben&ouml;tigen die Berechtigung \'%s\', um diese Funktion nutzen zu k&ouml;nnen.';
$lang['nocategorygiven'] = 'Keine Kategorie vorhanden';
$lang['nocontentgiven'] = 'Kein Inhalt vorhanden';
$lang['noitemsfound'] = 'Keine Eintr&auml;ge in dieser Kategorie gefunden: %s';
$lang['nopostdategiven'] = 'Kein Erstellungsdatum vorhanden';
$lang['note'] = '<em>Hinweis:</em> Datum/Zeit muss ein g&uuml;ltiges \'yyyy-mm-dd hh:mm:ss\' Format sein.';
$lang['notitlegiven'] = 'Kein Titel eingegeben';
$lang['numbertodisplay'] = 'Anzuzeigende Anzahl (leer lassen, um alle anzuzeigen)';
$lang['print'] = 'Drucken';
$lang['postdate'] = 'Erstellt am:';
$lang['postinstall'] = 'Stellen Sie sicher, dass User, welche News administrieren d&uuml;rfen, die Berechtigung "Modify News" haben !';
$lang['rsstemplate'] = 'RSS-Template';
$lang['startdate'] = 'Anfangsdatum';
$lang['startrequiresend'] = 'Die Eingabe eines Anfangsdatums erfordert auch die Eingabe eines Verfallsdatums.';
$lang['submit'] = 'Speichern';
$lang['title'] = 'Titel';
$lang['selectcategory'] = 'Select Category'; //Needs translation
$lang['submit'] = 'Speichern';
$lang['summary'] = 'Zusammenfassung';
$lang['summarytemplate'] = 'Zusammenfassungs-Template';
$lang['title'] = 'Titel';
$lang['useexpiration'] = 'Verfallsdatum verwenden';
$lang['help'] = <<<EOF
	<h3>Was macht dieses Modul?</h3>
	<p>Dieses Modul zeigt Ihnen Nachrichten in der Seite an, in der das Modul aktiviert ist, &auml;hnlich einem HTML Abschnitt (Blog),<br />aber mit mehr M&ouml;glichkeiten.<br /> Ist das Modul installiert, kann es ?ber die News - Administratorseite eingestellt werden.<br /> Dort k&ouml;nnen Kategorien selektiert oder neue angelegt werden.<br /> Wurde eine Kategorie selektiert oder angelegt wird eine der vorhandenen  Eintr&auml;ge angezeigt.<br /> Sie k&ouml;nnen an dieser Stelle Eintr&auml;ge hinzuf&uuml;gen, bearbeiten oder l&ouml;schen und zwar f&uuml;r diese Kategorie.</p>
	<h3>Sicherheit</h3>
	<p>Ein User kann das Modul nur administrieren, wenn er einer Gruppe angeh&ouml;rt, die das Recht 'Modify News' besitzt !</p>
	<h3>Wie wird es eingesetzt ?</h3>
	<p>Der einfachste Weg ist der Gebrauch in Verbindung mit dem cms_module tag.<br /> Damit wird das Modul automatisch in Ihrem Template oder Seite eingef&uuml;gt, wo auch immer Sie es w&uuml;schen.<br /> Der Code f&uuml;r den Tag  sieht in etwa so aus:<br /><br /> <code>{cms_module module="news" number="5" category="Meine Kategorie"}</code></p>
	<h3>Welche Parameter gibt es ?</h3>
	<p>
	<ul>
	<li><em>(optional)</em> number="5" - Maximal anzuzeigende Eintr&auml;ge =- ohne Parameter werden wird alles angezeigt.</li>
	<li><em>(optional)</em> dateformat - Datum/Zeit Format (Das Format muss dem der <a href="http://php.net/date" target="_blank">PHP - Date - Funktion</a> entsprechen.).</li>
	<li><em>(optional)</em> makerssbutton="true" - Erzeugt ein Button mit einem Link zu einem RSS -Feed zu einem Eintrag.</li>
	<li><em>(optional)</em> swaptitledate="true" - Schaltet die Reihenfolge von Datum und Titel.</li>
	<li><em>(optional)</em> category="Meine Kategorie" - Zeigt nur Eintr&auml;ge dieser Kategorie an. Ohne Parameter werden alle Kategorien angezeigt.</li>
	<li><em>(optional)</em> summary="page" - Aktiviert die zusammenfassende Methode.Links werden mit dem Titel ausgegeben und der Inhalt wird gem&auml;&szlig; dem Parameter length gek&uuml;rzt.</li>
	<li><em>(optional)</em> length="80" - Wird f&uuml;r die zusammenfassende Methode ben&ouml;tigt. Jeder Artikel wird auf Anzahl der Anschl&auml;ge gek&uuml;rzt, die hier vorgegeben sind.</li>
	<li><em>(optional)</em> showcategorywithtitle="true" - Veranla&szlig;t die Anzeige der Kategorie nebst Titel.</li>
	<li><em>(optional)</em> moretext="Mein Text..." - Hier den Text vorgeben, der dann erscheint, wenn ein Artikel l&auml;nger ist, als &uuml;ber den parameter length vorgegeben. Die Voreinstellung ist "more...".</li>
	<li><em>(optional)</em> sortasc="true" - Sortiert Eintr&auml;ge in aufsteigende Folge schneller, als in absteigender (nach Datum).</li>
	</ul>
	</p>
EOF;
?>
