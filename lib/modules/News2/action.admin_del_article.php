<?php
namespace News2;
use News2;

if( !isset($gCms) ) exit;
if( !$this->VisibleToAdminUser() ) exit;
$artm = $this->articleManager();
$uid = get_userid();

try {
    $news_id = (int) get_parameter_value( $params, 'news_id' );
    if( $news_id < 1 ) throw new \LogicException( 'Invalid or missing parameter' );
    $article = $artm->loadByID( $news_id );
    if( !$article ) throw new \LogicException( 'Article not found' );
    $hm = $this->cms->get_hook_manager();

    if( $this->CheckPermission( News2::MANAGE_PERM ) ||
        ($this->CheckPermission( News2::OWN_PERM) && $this->CheckPermission( News2::DELOWN_PERM) &&
         $article->author_id == $uid) ) {
        // nothing
    }
    else {
        throw new \RuntimeException( $this->Lang('err_permission') );
    }

    $hm->emit( 'News2::beforeDeleteArticle', $article );
    $artm->delete( $article );
    $hm->emit( 'News2::afterDeleteArticle', $article );

    $this->SetMessage( $this->Lang('msg_deleted') );
    $this->RedirectToAdminTab();
}
catch( \Exception $e ) {
    $this->SetError( $e->GetMessage() );
    $this->RedirectToAdminTab();
}
