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

// get the active wysiwyg and syntax modules initialized.
$list = CmsFormUtils::get_requested_wysiwyg_modules();
if( is_array($list) && count($list) ) {
  foreach( $list as $one ) {
    $obj = cms_utils::get_module($one);
    if( is_object($obj) ) $headertext .= $obj->WYSIWYGGenerateHeader($htmlresult);
  }
}
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
# vim:ts=4 sw=4 noet
?>