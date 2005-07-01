<?php
$lang['addacomment'] = 'Pridėti Komentarą';
$lang['cancel'] = 'Atšaukti';
$lang['comment'] = 'Komentaras';
$lang['error'] = 'Klaida';
$lang['errorenterauthor'] = 'Įvesti Autorių';
$lang['errorentercomment'] = 'Įveskite Komentarą';
$lang['submit'] = 'Pateikti';
$lang['yourname'] = 'Jūsų Vardas';
$lang['help'] = <<<EOD
	<h3>Ką jis daro?</h3>
	<p>Šis modulis naudojamas pridėti komentarus puslapiams, kur vėliau tuos komentarus puslapio lankytojai gali skaityti. Praktinė šio modulio nauda yra dokumentaciniai puslapiai.</p>
	<h3>Kaip jį naudoti?</h3>
	<p>Įterpiamas į puslapį arba šabloną naudojant cms_module žymę. Kodas turėtų atrodyti panašiai kaip: <code>{cms_module module="comments"}</code></p>
	<h3>Kokie parametrai egzistuoja?</h3>
	<p>
	<ul>
		<li><em>(optional)</em> number="5" - Maksimalus rodomų įrašų skaičius -- palikus tuščią bus rodomi visi įrašai</li>
		<li><em>(optional)</em> dateformat - Datos/laiko formatas naudojant php funkcijos strftime parametrus.  Žiūrėkite <a href="http://php.net/strftime" target="_blank">here</a> apie parametrus ir informaciją.</li>
	</ul>
EOD;
?>
