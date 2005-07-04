<?php
$lang['help'] = <<<EOF
            <h3>Mit‰ t‰m‰ moduuli tekee?</h3>
            <p>RSS moduulin avulla voit n‰ytt‰‰ omalla sivullasi muiden sivujen tarjoamia rss virtoja. Moduulia voidaan k‰ytt‰‰ sivuilla ja/tai mallipohjissa lis‰‰m‰ll‰ moduulin avainsana. </p>

            <h3>Mit‰ muuta minun tulisi tiet‰‰ ?</h3>

	    <p>RSS moduuli noutaa ja tallentaa rss virrat palvelimen levylle nopeuttaakeeen toimintaansa. Tiedot p‰ivitet‰‰n aika ajoin kun X m‰‰r‰ hakuja on kohdistunut jo haettuun informaatioon.</p>

            <h3>Kuinka moduulia k‰ytet‰‰n?</h3>
            <p>Lis‰‰ avainsana joko mallipohjaan tai sivulle. Esimerkki avainsanasta: <br /><code>{cms_module module="rss" url="http://download.freshmeat.net/backend/fm-releases.rdf" numbertoshow="5"}</code></p>
            <h3>Mit‰ parametrej‰ moduuli hyv‰ksyy?</h3>
            <p>
            <ul>
                <li>url="http://feed_url" - RSS virran URL eli osoite</li>
                <li><em>(optional)</em>numbertoshow="5" - Kuinka monta merkint‰‰n haetusta datasta tulostetaan -- jos t‰t‰ ei ole m‰‰rritelty, tulostetaan kaikki. </li>
            </ul>
            </p>
EOF;
?>
