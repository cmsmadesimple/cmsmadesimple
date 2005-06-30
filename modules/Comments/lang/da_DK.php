<?php
$lang['addacomment'] = 'Tilføj en kommentar';
$lang['cancel'] = 'Afbryd';
$lang['comment'] = 'Kommentar';
$lang['error'] = 'Fejl';
$lang['errorenterauthor'] = 'Angiv en forfatter';
$lang['errorentercomment'] = 'Angiv en kommentar (er det ikke ligesom meningen?)';
$lang['submit'] = 'Send';
$lang['yourname'] = 'Dit navn';
$lang['help'] = <<<EOF
	<h3>Hvad gør dette module?</h3>
	<p>Dette module bruges til at tilføje kommentarer til de enkelte sider som så kan læses af brugere der besøger siden senere. Et praktisk formål med modulet er til dokumentations sider, hvor brugere så kan tilføje yderligere kommentarer og informationer til siden.</p>
	<h3>Hvordan bruges modulet?</h3>
	<p>Comments er bare et tag modul. Det indsættes i din side eller skabelon ved hjælp af en cms_module tag. Et eksempel på syntaks kunne være: <code>{cms_module module="comments"}</code></p>
	<h3>Hvilke parametre er der?</h3>
	<p>
	<ul>
		<li><em>(valgfrit)</em> number="5" - Det maksimale antal kommentarer der skal vises - hvis intet angives vises alle kommentarer.</li>
		<li><em>(valgfrit)</em> dateformat - Formatet for dato/tidspunkt kan angives som i php's strftime function.  Se <a href="http://php.net/strftime" target="_blank">her</a> for en liste over parametre og information.</li>
	</ul>
EOF;
?>
