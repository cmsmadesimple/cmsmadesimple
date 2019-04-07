<?php
namespace PressRoom;
use PressRoom;

if( !$gCms ) exit;
if( !$this->VisibleToAdminUser() ) exit;

try {
    $artm = $this->articleManager();
    $hm = $this->cms->get_hook_manager();

    // validate params
    $action = trim(filter_var( $_POST['bulk_action'], FILTER_SANITIZE_STRING ));
    $items_str = trim(filter_var( $_POST['bulk_items'], FILTER_SANITIZE_STRING ));
    if( !$action ) throw new \LogicException('Missing/Invalid bulk action');
    if( !$items_str ) throw new \LogicException('Missing/Invalid bulk_items (1)');
    $items = explode(',', $items_str);
    array_walk( $items, function( &$item ){
            $item = (int) $item;
    });
    $items = array_unique($items);
    if( !count($items) ) throw new \LogicException('Missing/Invalid bulk_items (2)');
    // this is to help ensure performance, and reduce the possibility of memory isues.
    if( count($items) > 250 ) throw new \RuntimeException( $this->Lang('err_toomanybulk') );

    $can_delete = $this->CheckPermission( PressRoom::MANAGE_PERM ) ||
        ($this->CheckPermission( PressRoom::OWN_PERM) && $this->CheckPermission( PressRoom::DELOWN_PERM ) );

    // get articles and check permission.
    $articles = null;
    if( $this->CheckPermission( PressRoom::MANAGE_PERM ) ) {
        $filter = $artm->createFilter( [ 'id_list'=>$items ] );
        $articles = $artm->loadByFilter( $filter );
    }
    else if( $this->CheckPermission( PressRoom::OWN_PERM ) ) {
        if( $action == 'del' && !$this->CheckPermission( PressRoom::DELOWN_PERM ) ) {
            throw new \RuntimeException( $this->Lang('err_permission') );
        }
        // load only the items we own.
        $uid = get_userid();
        $filter = $artm->createFilter( [ 'author_id'=>$uid, 'id_list'=>$items ] );
        $articles = $artm->loadByFilter( $filter );
    } else if( $this->CheckPermission( PressRoom::APPROVE_PERM ) ) {
        $opts = [ 'id_list'=>$items ];
        if( $action == 'status_published' ) {
            $opts['status'] = Article::STATUS_NEEDSAPPROVAL;
        }
        else if( $action == 'status_approve' ) {
            $opts['status'] = Article::STATUS_PUBLISHED;
        }
        else {
            throw new \RuntimeException( $this->Lang('err_permission') );
        }

        // load only the items that need approval.
        $filter = $artm->createFilter( $opts );
        $articles = $artm->loadByFilter( $filter );
        if( !count($article) ) throw new \RuntimeException( $this->Lang('err_approve_nomatching'));
    } else {
        throw new \RuntimeException( $this->Lang('err_permission') );
    }
    if( !count($articles) ) throw new \LogicException('Could not find requested articles' );

    switch( $action ) {
        case 'del':
            if( !$can_delete ) throw new \RuntimeException( $this->Lang('err_permission') );
            $db->StartTrans();
            foreach( $articles as $article ) {
                $hm->emit( 'PressRoom::beforeDeleteArticle', $article );
                $artm->delete( $article );
                $hm->emit( 'PressRoom::afterDeleteArticle', $article );
            }
            $db->CompleteTrans();
            audit('', $this->GetName(),'Bulk delete of '.count($articles).' articles');
            break;
        case 'status_published':
            $db->StartTrans();
            foreach( $articles as $article ) {
                $article->status = $article::STATUS_PUBLISHED;
                $hm->emit( 'PressRoom::beforeSaveArticle', $article );
                $artm->save( $article );
                $hm->emit( 'PressRoom::afterSaveArticle', $article, $article->id );
            }
            $db->CompleteTrans();
            audit('', $this->GetName(),'Bulk status change of '.count($articles).' articles to published');
            break;
        case 'status_draft':
            $db->StartTrans();
            foreach( $articles as $article ) {
                $article->status = $article::STATUS_DRAFT;
                $hm->emit( 'PressRoom::beforeSaveArticle', $article );
                $artm->save( $article );
                $hm->emit( 'PressRoom::afterSaveArticle', $article, $article->id );
            }
            $db->CompleteTrans();
            audit('', $this->GetName(),'Bulk status change of '.count($articles).' articles to draft');
            break;
        case 'status_approve':
            $db->StartTrans();
            foreach( $articles as $article ) {
                $article->status = $article::STATUS_NEEDSAPPROVAL;
                $hm->emit( 'PressRoom::beforeSaveArticle', $article );
                $artm->save( $article );
                $hm->emit( 'PressRoom::afterSaveArticle', $article, $article->id );
            }
            $db->CompleteTrans();
            audit('', $this->GetName(),'Bulk status change of '.count($articles).' articles to needsapproval');
            break;
        case 'status_disabled':
            $db->StartTrans();
            foreach( $articles as $article ) {
                $article->status = $article::STATUS_DISABLED;
                $hm->emit( 'PressRoom::beforeSaveArticle', $article );
                $artm->save( $article );
                $hm->emit( 'PressRoom::afterSaveArticle', $article, $article->id );
            }
            $db->CompleteTrans();
            audit('', $this->GetName(),'Bulk status change of '.count($articles).' articles to disabled');
            break;
    }

    $this->SetMessage( $this->Lang('msg_bulkdone', count($articles)) );
    $this->RedirectToAdminTab();
}
catch( \Exception $e ) {
    $this->SetError( $e->GetMessage() );
    $this->RedirectToAdminTab();
}
