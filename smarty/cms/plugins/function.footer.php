<?php

function smarty_function_footer() {
    global $CMS_VERSION;
    echo '<div class="bottom">'."\n";
    echo 'Site powered by <a href="http://cmsmadesimple.org">CMS Made Simple</a> Version: '."$CMS_VERSION<br />\n";
    echo 'Think you\'ve found a bug? <a href="http://trac.wishy.org/cms">Submit</a> it'."\n";
    echo "</div>\n";
} ## smarty_function_show_footer

?>
