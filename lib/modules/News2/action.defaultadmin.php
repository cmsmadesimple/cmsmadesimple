<?php
namespace News2;
use News2;


if( !isset($gCms) ) exit;
if( !$this->VisibleToAdminUser() ) exit;
const FILTER_KEY = 'News2-defaultadmin-filter';
$artm = $this->articleManager();
$uid = get_userid();

// create our filter options
$orig_filter_opts = $my_filter_opts = [ 'limit'=>50, 'useperiod'=>-1 ];
if( isset($_SESSION[FILTER_KEY]) ) $my_filter_opts = $_SESSION[FILTER_KEY];
if( !empty($_POST) ) {
    if( isset( $_POST['filter_reset']) ) {
        // unset the session
        unset( $_SESSION[FILTER_KEY] );;
        $my_filter_opts = $orig_filter_opts;
    }
    else if( isset( $_POST['filter_submit']) ) {
        // get filter opts from form
        $my_filter_opts['title_substr'] = trim(get_parameter_value($_POST,'filter_title'));
        $my_filter_opts['category_id'] = (int) get_parameter_value($_POST,'filter_category');
        $my_filter_opts['withchildren'] = cms_to_bool(get_parameter_value($_POST,'filter_categorychildren'));
        $my_filter_opts['status'] = trim(get_parameter_value($_POST,'filter_status'));
        $my_filter_opts['useperiod'] = (int) get_parameter_value($_POST,'filter_useperiod');
        $my_filter_opts['limit'] = max(1,min(1000,(int)get_parameter_value($_POST, 'filter_limit')));
        $my_filter_opts['offset'] = 0;
        $_SESSION[FILTER_KEY] = $my_filter_opts;
        unset( $_POST['page'] );
        // save to session
    }
}

$opts = $my_filter_opts;
if( $this->CheckPermission( News2::MANAGE_PERM ) ) {
    // can edit all articles
}
else if( $this->CheckPermission( News2::APPROVE_PERM ) ) {
    // can view all articles, if have own_perm may be able to edit some
}
else if( $this->CheckPermission( News2::OWN_PERM ) ) {
    // can only see their articles
    $opts['author_id'] = $uid;
}
$page = (int) get_parameter_value( $_POST, 'page' );
if( $page > 0 ) $opts['offset'] = $opts['limit'] * ($page - 1);

// load matching articles
$filter = $artm->createFilter( $opts );
$articles = $artm->loadByFilter( $filter );

// get metadata for these articles
$metadata = null;
if( $articles ) {
    foreach( $articles as $article ){
        $aid = $article->id;
        $metadata[$aid]['owner'] = false;
        $metadata[$aid]['canview'] = false;
        $metadata[$aid]['canedit'] = $this->canEditArticle( $article->id );
        $metadata[$aid]['candelete'] = $this->canDeleteArticle( $article->id );
        if( $uid == $article->author_id && $this->CheckPermission( News2::OWN_PERM) ) {
            $metadata[$aid]['owner'] = true;
        }
        if( $this->CheckPermission( News2::APPROVE_PERM ) ) {
            $metadata[$aid]['canview'] = true;
        }
    }
}

$status_list = [
    Article::STATUS_DRAFT => $this->Lang('status_draft'),
    Article::STATUS_PUBLISHED => $this->Lang('status_published'),
    Article::STATUS_DISABLED => $this->Lang('status_disabled'),
    Article::STATUS_NEEDSAPPROVAL => $this->Lang('status_needsapproval')
    ];
$filter_status_list = array_merge( [ ''=>$this->Lang('any') ], $status_list );
$filter_periods_list = [
    -1 => $this->Lang('period_any'),
    1 => $this->Lang('period_displayable'),
    2 => $this->Lang('period_started'),
    4 => $this->Lang('period_unstarted'),
    3 => $this->Lang('period_expired'),
    5 => $this->Lang('period_nodates')
    ];
$bulk_list = [];
if( $this->CheckPermission( News2::MANAGE_PERM ) ) {
    $bulk_list['del'] = $this->Lang('delete');
    $bulk_list['status_published'] = $this->Lang('set_status_published');
    $bulk_list['status_draft'] = $this->Lang('set_status_draft');
    $bulk_list['status_approve'] = $this->Lang('set_status_approve');
    $bulk_list['status_disabled'] = $this->Lang('set_status_disabled');
} else if( $this->CheckPermission( News2::OWN_PERM ) ) {
    if( $this->CheckPermission( News2::DELOWN_PERM ) ) $bulk_list['del'] = $this->Lang('delete');
    if( $this->CheckPermission( News2::APPROVE_PERM ) ) $bulk_list['status_approve'] = $this->Lang('set_status_approve');
} else if( $this->CheckPermission( News2::APPROVE_PERM ) ) {
    // people with only this permission can only set status to approved
    $bulk_list['status_published'] = $this->Lang('set_status_published');
    $bulk_list['status_approve'] = $this->Lang('set_status_approve');
}
$sorting_list = [
    ArticleFilter::SORT_MODIFIEDDATE => $this->Lang('sort_modifieddate'),
    ArticleFilter::SORT_CREATEDATE => $this->Lang('sort_createdate'),
    ArticleFilter::SORT_TITLE => $this->Lang('sort_title'),
    ArticleFilter::SORT_STATUS => $this->Lang('sort_status')
    ];
$tpl = $smarty->CreateTemplate( $this->GetTemplateResource('defaultadmin.tpl'), null, null, $smarty );
$tpl->assign('articles',$articles);
$tpl->assign('metadata',$metadata);
$tpl->assign('status_list',$status_list);
$tpl->assign('filter_status_list',$filter_status_list);
$tpl->assign('filter_periods_list',$filter_periods_list);
$tpl->assign('category_list', $this->categoriesManager()->getCategoryList( [-1 => $this->Lang('none')] ) );
$tpl->assign('sort_list',$sorting_list);
$tpl->assign('bulk_list',$bulk_list);
$tpl->assign('filter_opts',$my_filter_opts);
$tpl->assign('filter_applied',($my_filter_opts != $orig_filter_opts));
$tpl->display();