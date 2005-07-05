<?php
$lang['help'] = <<<EOF
            <h3>Che cosa fa?</h3>
            <p>RSS &egrave; un modulo per visualizzare news feeds da altri siti nel vostro sito.  Pu&ograve; essere inserito in un template o come tag nel contenuto della pagina e visualizzer&agrave; il titolo e url di ciascun termine del dato feed.</p>
            <h3>Qualcos'altro che devo sapere?</h3>
            <p>Il modulo RSS cache (memorizza localmente) i feeds cos&igrave; che non devono essere prelevati e analizzati ogni aggiornamento.  Quando i dati locali diventano obsoleti, prelever&agrave; di nuovo i dati.  Non si dovrebbe notare un degrado delle performance usandolo in un template.</p>
            <h3>Come usarlo?</h3>
            <p>Essendo un modulo, viene inserito nelle tue pagine o template usando il tag cms_module.  Esempio di sintassi: <br /><code>{cms_module module="rss" url="http://download.freshmeat.net/backend/fm-releases.rdf" numbertoshow="5"}</code></p>
            <h3>Quali parametri ci sono?</h3>
            <p>
            <ul>
                <li>url="http://feed_url" - URL dei RSS feed</li>
                <li><em>(optional)</em>numbertoshow="5" - Numero massimo di termini da visualizzare -- lasciandolo VUOTO visualizza tutt i termini</li>
            </ul>
            </p>
EOF;
?>
