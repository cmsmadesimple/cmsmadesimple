<?php

global $DONT_LOAD_SMARTY;
if( !isset($DONT_LOAD_SMARTY) ) {
    class Smarty_CMS extends \CMSMS\internal\Smarty {}
}
