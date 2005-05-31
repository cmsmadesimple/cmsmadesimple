<?php
$lang['help'] = <<<EOF
            <h3>Que fait ce module?</h3>
            <p>RSS est un module pour afficher des Flux RSS d'autres sites dans votre site.  Il peut être inséré dans un template ou une page de contenu, en tant que tag,  et affichera le titre et l'url de chaque élément donné.</p>
            <h3>Que dois-je savoir?</h3>
            <p>The RSS module mettra les Flux en cache, afin qu'ils ne soient pas téléchargés et parsés à chaque rafraîchissement.  A la place, il télécharge les Flux tous les tant de rafraîchissements de la page, et stocke les données locallement afin qu'elles soient facilement accessibles.  Quand la copie locale devient obsolète, il télécharge les données fraîches.  Vous ne devriez noter aucune différence de performance lorsque le module est utilisé dans un template.</p>
            <h3>Comment l'utiliser?</h3>
            <p>Puisque RSS Feed est un module utilisant les tags, il est inséré dans votre page ou template avec le tag cms_module.  Un exemple de synthaxe serait: <br /><code>{cms_module module="rss" url="http://download.freshmeat.net/backend/fm-releases.rdf" numbertoshow="5"}</code></p>
            <h3>Quels sont les paramètres possibles?</h3>
            <p>
            <ul>
                <li>url="http://feed_url" - RSS feed URL</li>
                <li><em>(en option)</em>numbertoshow="5" - Le nombre maximal d'objets de Flux RSS à afficher -- laisser ce paramètre vide affichera tous les objets.</li>
            </ul>
            </p>
EOF;
?>
