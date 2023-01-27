<?php

// compatibility stuff
if( !function_exists('gzopen') && function_exists('gzopen64') ) {
    function gzopen($filename , $mode , $use_include_path = 0) {
        return gzopen64($filename, $mode, $use_include_path);
    }
}

?>