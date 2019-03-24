<?php
if( !isset($gCms) ) exit;

$handlers = ob_list_handlers();
for ($cnt = 0; $cnt < sizeof($handlers); $cnt++) {
    ob_end_clean();
}

try {
    $term = trim(strip_tags(get_parameter_value($_REQUEST,'term')));
    if( strlen($term) < 2) {
        throw new \RuntimeException('Invalid input');
    }
    $limit = (int) get_parameter_value($_REQUEST,'limit');
    if( $limit < 1 ) $limit = 10;
    $limit = max(1,min(50,$limit));

    if( strpos($term,'%') === FALSE ) $term = "{$term}%";
    $sql = 'SELECT word FROM '.CMS_DB_PREFIX.'module_search_index WHERE word LIKE ? ORDER BY count DESC';
    $dbr = $db->SelectLimit( $sql, $limit, 0, [ $term ] );

    $out = null;
    while( $dbr && !$dbr->EOF() ) {
        $word = $dbr->fields['word'];
        $out[] = [ 'label'=>$word, 'value'=>$word ];
        $dbr->MoveNext();
    }
    if( !empty($out) ) echo json_encode($out);
}
catch( \RuntimeException $e ) {
    header("HTTP/1.0 400 Invalid request");
    header("Status: 404 Invalid request");
}
exit;