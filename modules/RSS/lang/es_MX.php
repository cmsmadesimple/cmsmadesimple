<?php
$lang['help'] = <<<EOF
            <h3>Que hace esto?</h3>
            <p>RSS es un modulo para mostrar noticias de otros sitios en tu sitio. Puede ser agregado a una plantilla o pagina como una etiquetita y mostrara el titulo y direccion de cada noticia del FEED RSS.</p>
            <h3>Necesito saber algo mas?</h3>
            <p>El modulo RSS hara un cache de los feeds para que no sean bajados y procesados con cada vista de pagina. En vez de eso, bajara al feed cada tantas vistas de pagina y guardara los datos localmente para que sea facilmente accesible. cuando expira la informacion local, ira por informacion nueva. No veras cargas en tu pagina con este modulo en una plantilla.</p>
            <h3>¿omo se usa?</h3>
            <p>Debido a que solo es un modulo de etiqueta, se inserta en tu pagina o plantilla al usar la etiqueta cms_module.  Un ejemplo es: <br /><code>{cms_module module="rss" url="http://download.freshmeat.net/backend/fm-releases.rdf" numbertoshow="5"}</code></p>
            <h3>¿Que opciones acepta?</h3>
           <p>
            <ul>
                <li>url="http://feed_url" - Direccion del RSS feed</li>
                <li><em>(optional)</em>numbertoshow="5" - Numero maximo de objetos a mostrar -- si lo dejas vacio mostrara todos</li>
            </ul>
            </p>
EOF;
?>
