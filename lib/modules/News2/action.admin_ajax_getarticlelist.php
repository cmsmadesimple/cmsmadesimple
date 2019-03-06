<?php
namespace News2;

try {
    // for use in the RelatedArticles autocomplete
    $out = null;
    $list = get_parameter_value( $_GET, 'list' );
    if( !$list ) throw new \LogicException('No list provided');
    $tmp = explode(',',$list);
    if( is_array($tmp) && count($tmp) ) {
        $list = null;
        foreach( $tmp as $one ) {
            $one = (int) $one;
            if( $one > 0 ) $list[] = $one;
        }
        $list = array_unique( $list );
    }
    if( !is_array($list) || count($list) == 0 ) throw new \LogicException('No list provided');

    $artm = $this->articleManager();
    $filter = $artm->createFilter( ['id_list'=>$list ] );
    $list = $artm->loadByFilter( $filter );
    if( count($list) ) {
        foreach( $list as $one ) {
            $out[] = [ 'label'=>$one->title, 'value'=>$one->id ];
        }
    }

    $handlers = ob_list_handlers();
    for ($cnt = 0; $cnt < sizeof($handlers); $cnt++) { ob_end_clean(); }

    header('Pragma: public');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Cache-Control: private',false);
    header('Content-Type: application/json');
    echo json_encode($out);
    exit;
}
catch( \Exception $e ) {
    // do error 500
    debug_to_log( 'ERROR: '.get_class($e)."\n---\n".$e->GetMessage()."\n---\n".$e->getTraceAsString()."\n" );
    header('HTTP/1.0 500 Internal Error');
    header('Status: 500 Internal error');
}