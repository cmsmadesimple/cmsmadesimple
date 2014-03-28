<?php
if (isset($USE_THEME) && $USE_THEME == false) {
  echo '<!-- admin theme disabled -->';
}
else {
  $themeObject->do_footer();
}

$gCms = cmsms();
if ($gCms->config["debug"] == true) {
  echo '<div id="DebugFooter">';
  $arr = $gCms->get_errors();
  foreach ($arr as $error) {
    echo $error;
  }
  echo '</div> <!-- end DebugFooter -->';
}

if (!isset($USE_THEME) || $USE_THEME != false) {
  echo '</body>';
  echo '</html>';
}

#Pull the stuff out of the buffer...
$htmlresult = '';
if (!(isset($USE_OUTPUT_BUFFERING) && $USE_OUTPUT_BUFFERING == false)) {
  $htmlresult = @ob_get_contents();
  @ob_end_clean();
}

#Do any header replacements (this is for WYSIWYG stuff)
$headertext = '';
$formtext = '';
$formsubmittext = '';
$bodytext = '';
$userid = get_userid();

// initialize the requested wysiwyg modules
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
      $headertext .= $obj->WYSIWYGGenerateHeader($selector,$cssname);
    }

    // now, do we need a generic iniitialization?
    if( $need_generic ) {
      $headertext .= $obj->WYSIWYGGenerateHeader();
    }
  }
}

// initialize the requested syntax hilighter modules
$list = CmsFormUtils::get_requested_syntax_modules();
if( is_array($list) && count($list) ) {
  foreach( $list as $one ) {
    $obj = cms_utils::get_module($one);
    if( is_object($obj) ) $headertext .= $obj->SyntaxGenerateHeader($htmlresult);
  }
}

$htmlresult = $themeObject->postprocess($htmlresult);
$htmlresult = str_replace('<!-- THIS IS WHERE HEADER STUFF SHOULD GO -->', $headertext, $htmlresult);
echo $htmlresult;

if( isset($gCms->config['show_performance_info']) ) {
  $db = cmsms()->GetDb();
  $endtime = microtime();
  $memory = (function_exists('memory_get_usage')?memory_get_usage():0);
  $memory_net = $memory - $orig_memory;
  $memory_peak = (function_exists('memory_get_peak_usage')?memory_get_peak_usage():0);
  echo "<div>".microtime_diff($starttime,$endtime)." / ".(isset($db->query_count)?$db->query_count:'')." / {$memory_net} / {$memory} / {$memory_peak}</div>\n";
}

?>