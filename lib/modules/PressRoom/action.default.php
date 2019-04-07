<?php
namespace PressRoom;
if( !isset($gCms) ) exit;
$artm = $this->articleManager();
$fielddefs = $this->fielddefManager()->loadAllAsHash();

if( isset($params['args']) ) {
    // get params encoded in a string, from the pagination form
    // or from a link... overrides $params
    $tmp = base64_decode( $params['args'] );
    if( $tmp ) $tmp = json_decode( $tmp, TRUE );
    if( is_array($tmp) && count($tmp) ) {
        unset($params['args']);
        $params = array_merge( $params, $tmp );
    }
}
$template = get_parameter_value($params,'summarytemplate');
$template = $this->ResolveTemplate('summary',$template,'default.tpl');

$filter_opts = [ 'limit' => 50 , 'status'=>Article::STATUS_PUBLISHED, 'useperiod'=> 1, 'sortby'=>'news_date' ];
if( ($limit = (int)get_parameter_value( $params, 'limit')) ) {
    $filter_opts['limit'] = max(1,$limit);
}
if( ($category_id = (int) get_parameter_value( $params, 'category_id') ) > 0 ) {
    $filter_opts['category_id'] = $category_id;
}
if( ($alias = trim(get_parameter_value( $params, 'category_alias')) ) ) {
    $category = $this->categoriesManager()->loadByAlias($alias);
    if( $category ) $filter_opts['category_id'] = $category->id;
}
if( isset($filter_opts['category_id']) ) {
    $tmp = cms_to_bool( get_parameter_value( $params, 'withchildren', $this->settings()->bycategory_withchildren ) );
    $filter_opts['withchildren'] = $tmp;
}
if( ($useperiod = (int) get_parameter_value( $params, 'useperiod') ) >= 0 ) {
    $filter_opts['useperiod'] = min(2,max(0,$useperiod));
}
if( ($showall = (int) get_parameter_value( $params, 'showall') ) ) {
    unset( $filter_opts['status'] );
}
if( ($sortby = get_parameter_value( $params, 'sortby')) ) {
    if( endswith($sortby,'_asc') ) {
        $filter_opts['sortorder'] = ArticleFilter::ORDER_ASC;
        $sortby = substr($sortby,0,-4);
    }
    switch( $sortby ) {
        case ArticleFilter::SORT_MODIFIEDDATE:
        case ArticleFilter::SORT_CREATEDATE:
        case ArticleFilter::SORT_TITLE:
        case ArticleFilter::SORT_STATUS:
        case ArticleFilter::SORT_NEWSDATE:
            $filter_opts['sortby'] = $sortby;
            break;
        case ArticleFilter::SORT_FIELD:
            $sortdata = get_parameter_value( $params, 'sortdata' );
            if( $sortdata ) {
                $filter_opts['sortby'] = $sortby;
                // todo: check if the field name actually exists
                $filter_opts['sortdata'] = $sortdata;
            }
            break;
        default:
            break;
    }
}
$tmp_idlist = get_parameter_value( $params, 'idlist' );
if( $tmp_idlist && is_array($tmp_idlist) && count($tmp_idlist) ) {
    $filter_opts['id_list'] = $tmp_idlist;
}
// page defaults to 1... but can be overriden in mact params or request.
$page = (int) get_parameter_value( $params, 'news_page', 1);
$page = (int) get_parameter_value( $_GET, 'news_page', $page);
$page = max(1,$page);
$filter_opts['offset'] = ($page - 1) * $filter_opts['limit'];

$filter = $artm->createFilter( $filter_opts );
$articles = $artm->loadByFilter( $filter );
$tpl = $smarty->CreateTemplate( $this->GetTemplateResource( $template ), null, null, $smarty );
$tpl->assign('fielddefs',$fielddefs);
$tpl->assign('fieldtypes',$this->fieldTypeManager()->getAll());
$tpl->assign('articles',$articles);
$params2 = $params;
unset($params['news_page']);
$tpl->assign('params_str',base64_encode(json_encode($params)));
$tpl->display();
