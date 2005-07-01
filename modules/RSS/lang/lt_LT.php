<?php
$lang['help'] = <<<EOF
            <h3>Ką jis daro?</h3>
            <p>RSS yra modulis naudojamas rodyti RSS naujienas jūsų puslapyje, paimtas iš kitų puslapių. Jį galima įterprti į šabloną ar puslapį kaip žymę, ir jis atvaizduos pavadinimą ir nuorodą į kiekvienai RSS naujienai.</p>
            <h3>Ką dar privalau žinoti?</h3>
            <p>RSS modulis kešuoja naujienas, taigi jos nėra siunčiamos ir agreguojamos kiekvieną kartą perkrovus puslapį. Duomenys yra saugomi lokaliai, taigi, lengvai prieinami. Kai lokaliai saugomi duomenys nėra naujausi, bus siunčiami naujausi duomenys. Jūs neturėtumėte pastebėti jokių našumo pakitimų naudodami šį modulį savo šablonuose.</p>
            <h3>Kaip jį naudoti?</h3>
            <p>Šis modulis įterpiamas cms_module žyme į šabloną ar puslapį. Kodas turėtų atrodyti panašiai: <br /><code>{cms_module module="rss" url="http://download.freshmeat.net/backend/fm-releases.rdf" numbertoshow="5"}</code></p>
            <h3>Kokie parametrai egzistuoja?</h3>
            <p>
            <ul>
                <li>url="http://feed_url" - RSS naujienų URL</li>
                <li><em>(optional)</em>numbertoshow="5" - Maksimalus rodomų įrašų skaičius -- palikus tuščią bus rodomi visi įrašai</li>
            </ul>
            </p>
EOF;
?>
