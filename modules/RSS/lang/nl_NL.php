<?php
$lang['help'] = <<<EOF
            <h3>Wat dit doet?</h3>
            <p>RSS is a module for displaying news feeds from other sites in your site.  It can be inserted into a template or content page as a tag and will display the title and url of each item from the feed given.</p>
            <h3>Anything else I should know?</h3>
            <p>The RSS module will cache feeds so that they are not downloaded and parsed on every refresh.  Instead, it pulls the feed down every so many page refreshes and stores the data locally so it's easily accessible.  When the local data becomes stale, it will pull fresh data.  You should notice no performance hit by using it in a template.</p>
            <h3>Hoe gebruik ik het?</h3>
            <p>As this is just a tag module, it's inserted into your page or template by using the cms_module tag.  Example syntax would be: <br /><code>{cms_module module="rss" url="http://download.freshmeat.net/backend/fm-releases.rdf" numbertoshow="5"}</code></p>
            <h3>What parameters are there?</h3>
            <p>
            <ul>
                <li>url="http://feed_url" - RSS feed URL</li>
                <li><em>(optional)</em>numbertoshow="5" - Maximum number of items to display -- leaving empty will show all items</li>
            </ul>
            </p>
EOF;
?>
