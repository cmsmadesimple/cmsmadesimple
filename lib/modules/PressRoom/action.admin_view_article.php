<?php
namespace PressRoom;
use PressRoom;

if( !isset($gCms) ) exit;
if( !$this->VisibleToAdminUser() ) exit;

try {
    $artm = $this->articleManager();
    $catm = $this->categoriesManager();
    $fdm  = $this->fielddefManager();
    $fielddefs = $fdm->loadAllAsHash();
    $fieldtypes = $this->fieldTypeManager()->getAll();
    $hm = $this->cms->get_hook_manager();

    $news_id = (int) get_parameter_value( $params, 'news_id' );
    $news_id = (int) get_parameter_value( $params, 'article', $news_id );
    if( $news_id < 1 ) throw new \LogicException( 'Missing or invalid article id');

    $article = $artm->loadByID( $news_id );
    if( $this->CheckPermission( PressRoom::MANAGE_PERM ) || $this->CheckPermission( PressRoom::APPROVE_PERM) ) {
        // nothing
    } else if( $this->CheckPermission( PressRoom::OWN_PERM ) ) {
        if( $article->author_id != get_userid() ) throw new \RuntimeException( $this->Lang('err_permission') );
    } else {
        throw new \RuntimeException( $this->Lang('err_permission') );
    }

    if( !empty($_POST) ) {
        if( isset( $_POST['cancel']) ) {
            $this->RedirectToAdminTab();
        }
        else if( isset( $_POST['setpublished']) ) {
            $article->status = $article::STATUS_PUBLISHED;
            $hm->emit( 'PressRoom::beforeSaveArticle', $article );
            $artm->save( $article );
            $hm->emit( 'PressRoom::afterSaveArticle', $article, $article->id );
            audit($article->id,$this->GetName(),'Article status set to published');
            $this->SetMessage( $this->Lang('msg_saved') );
            $this->RedirectToAdminTab();
        }
        else if( isset( $_POST['setneedsapproval']) ) {
            $article->status = $article::STATUS_NEEDSAPPROVAL;
            $hm->emit( 'PressRoom::beforeSaveArticle', $article );
            $artm->save( $article );
            $hm->emit( 'PressRoom::afterSaveArticle', $article, $article->id );
            audit($article->id,$this->GetName(),'Article status set to needs approval');
            $this->SetMessage( $this->Lang('msg_saved') );
            $this->RedirectToAdminTab();
        }
    }
    $status_list = [
        Article::STATUS_DRAFT => $this->Lang('status_draft'),
        Article::STATUS_PUBLISHED => $this->Lang('status_published'),
        Article::STATUS_DISABLED => $this->Lang('status_disabled'),
        Article::STATUS_NEEDSAPPROVAL => $this->Lang('status_needsapproval')
    ];
    $tpl = $smarty->CreateTemplate( $this->GetTemplateResource( 'admin_view_article.tpl' ), null, null, $smarty );
    $tpl->assign('article',$article);
    $tpl->assign('settings', $this->settings());
    $tpl->assign('status_list',$status_list);
    $category = null;
    if( $article->category_id > 0 ) {
        $category = $this->categoriesManager()->loadByID( $article->category_id);
    }
    $tpl->assign('category',$category);
    $tpl->assign('fielddef_list',$fielddefs);
    $tpl->assign('can_approve',
                 $article->status == $article::STATUS_NEEDSAPPROVAL &&
                 ($this->CheckPermission( PressRoom::MANAGE_PERM ) || $this->CheckPermission( PressRoom::APPROVE_PERM) ) );
    $tpl->display();
}
catch( \Exception $e ) {
    $this->SetError( $e->GetMessage() );
    $this->RedirectToAdminTab();
}
