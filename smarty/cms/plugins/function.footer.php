<?php

function smarty_function_footer() {
    global $CMS_VERSION;
    echo '<div class="thebody">'."\n";
    echo '<div class="bottom">'."\n";
    echo 'Site powered by <a href="http://cmsmadesimple.sf.net">CMS Made Simple</a> Version: '."$CMS_VERSION<br />\n";
    echo 'Think you\'ve found a bug? <a href="http://cmsmadesimple.sf.net/mantis">Submit</a> it'."\n";
    echo "</div>\n";
    echo "</div>\n";
} ## smarty_function_show_footer

?>
