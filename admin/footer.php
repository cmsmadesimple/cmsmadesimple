<?php
// $USE_THEME inherited from parent scope.
if (! isset($USE_THEME) || $USE_THEME ) {
    // if using the theme... echo the footer to stdout
    $themeObject->do_footer();
}

$gCms = \CmsApp::get_instance();
$config = \cms_config::get_instance();
if ($config["debug"] == true) {
    // echo debug output to stdout
    echo '<div id="DebugFooter">';
    $arr = $gCms->get_errors();
    foreach ($arr as $error) {
        echo $error;
    }
    echo '</div> <!-- end DebugFooter -->';
}

// Pull the stuff out of the buffer...
$bodycontent = '';
if (!(isset($USE_OUTPUT_BUFFERING) && $USE_OUTPUT_BUFFERING == false)) {
  $bodycontent = @ob_get_contents();
  @ob_end_clean();
}
// bodycontent should contain what goes in the main content area of the page.

#Do any header replacements (this is for WYSIWYG stuff)
$formtext = '';
$formsubmittext = '';
$bodytext = '';
$userid = get_userid();

// initialize the requested wysiwyg modules
// because this can change based on module actions etc... it's done in the footer.
$list = CmsFormUtils::get_requested_wysiwyg_modules();
if( is_array($list) && count($list) ) {
    foreach( $list as $module_name => $info ) {

        $obj = cms_utils::get_module($module_name);
        if( !is_object($obj) ) {
            audit('','Core','WYSIWYG module '.$module_name.' requested, but could not be instantiated');
            continue;
        }

        // parse the list once and get all of the stylesheet names (if any)
        // preload all of the named stylesheets.  to minimize queries.
        $css = null;
        $cssnames = array();
        foreach( $info as $rec ) {
            if( $rec['stylesheet'] == '' || $rec['stylesheet'] == CmsFormUtils::NONE ) continue;
            $cssnames[] = $rec['stylesheet'];
        }
        $cssnames = array_unique($cssnames);
        if( is_array($cssnames) && count($cssnames) ) $css = CmsLayoutStylesheet::load_bulk($cssnames);

        // adjust the cssnames array to only contain the list of the stylesheets we actually found.
        if( is_array($css) && count($css) ) {
            $tmpnames = array();
            foreach( $css as $stylesheet ) {
                $name = $stylesheet->get_name();
                if( !in_array($name,$tmpnames) ) $tmpnames[] = $name;
            }
            $cssnames = $tmpnames;
        }
        else {
            $cssnames = null;
        }

        // initialize each 'specialized' textarea.
        $need_generic = FALSE;
        foreach( $info as $rec ) {
            $selector = $rec['id'];
            $cssname = $rec['stylesheet'];

            if( $cssname == CmsFormUtils::NONE ) $cssname = null;
            if( !$cssname || !is_array($cssnames) || !in_array($cssname,$cssnames) || $selector == CmsFormUtils::NONE ) {
                $need_generic = TRUE;
                continue;
            }

            $selector = 'textarea#'.$selector;
            $themeObject->add_headtext($obj->WYSIWYGGenerateHeader($selector,$cssname));
        }

        // now, do we need a generic iniitialization?
        if( $need_generic ) {
            $themeObject->add_headtext($obj->WYSIWYGGenerateHeader());
        }
    }
}

// initialize the requested syntax hilighter modules
$list = CmsFormUtils::get_requested_syntax_modules();
if( is_array($list) && count($list) ) {
    foreach( $list as $one ) {
        $obj = cms_utils::get_module($one);
        if( is_object($obj) ) $themeObject->add_headtext($obj->SyntaxGenerateHeader());
    }
}

$out = \CMSMS\HookManager::do_hook_accumulate('admin_add_footertext');
if( $out && !empty($out) ) {
    foreach( $out as $one ) {
        $one = trim($one);
        if( $one ) $themeObject->add_footertext($one);
    }
}

$bodycontent = $themeObject->postprocess($bodycontent);
echo $bodycontent;

if (!isset($USE_THEME) || $USE_THEME != false) {
    if( strpos($bodycontent,'</body') === FALSE ) echo '</body></html>';
}

if (!isset($USE_THEME) || $USE_THEME != false) {
    if( isset($config['show_performance_info']) ) {
        $db = \Cmsapp::get_instance()->GetDb();
        $endtime = microtime();
        $memory = (function_exists('memory_get_usage')?memory_get_usage():0);
        $memory_net = 'n/a';
        if( isset($orig_memory) ) $memory_net = $memory - $orig_memory;
        $memory_peak = (function_exists('memory_get_peak_usage')?memory_get_peak_usage():0);
        echo '<div style="clear: both;">'.microtime_diff($starttime,$endtime)." / ".(isset($db->query_count)?$db->query_count:'')." queries / Net Memory: {$memory_net} / End: {$memory} / Peak: {$memory_peak}</div>\n";
    }
}

?>
