<?php
namespace News2;
use News2;
use cms_utils;

class AdminHookHandler
{

    private $mod;

    private $settings;

    private $artm;

    public function __construct( News2 $mod, Settings $settings, ArticleManager $artm )
    {
        $this->mod = $mod;
        $this->settings = $settings;
        $this->artm = $artm;
        $hm = $mod->cms->get_hook_manager();

        $hm->emit( 'News2::afterSaveArticle', [ $this, 'afterSaveArticle_Search'] );
        $hm->emit( 'AdminSearch::get_slave_classes', [ $this, 'adminsearch_get_slave_classes' ] );
        $hm->emit( 'localizeperm', [ $this, 'localizePerm'] );
        $hm->emit( 'getperminfo', [ $this, 'getPermDesc'] );
    }

    public function localizePerm( string $source, string $name )
    {
        if( $source != $this->mod->GetName() ) return;
        switch( $name ) {
            case News2::MANAGE_PERM:
                return $this->mod->Lang('perm_manageperm' );
            case News2::OWN_PERM:
                return $this->mod->Lang('perm_ownperm' );
            case News2::DELOWN_PERM:
                return $this->mod->Lang('perm_delownperm' );
            case News2::APPROVE_PERM:
                return $this->mod->Lang('perm_approveperm' );
        }
    }

    public function getpermDesc( string $source, string $name )
    {
        if( $source != $this->mod->GetName() ) return;
        switch( $name ) {
            case News2::MANAGE_PERM:
                return $this->mod->Lang('permdesc_manageperm' );
            case News2::OWN_PERM:
                return $this->mod->Lang('permdesc_ownperm' );
            case News2::DELOWN_PERM:
                return $this->mod->Lang('permdesc_delownperm' );
            case News2::APPROVE_PERM:
                return $this->mod->Lang('permdesc_approveperm' );
        }
    }

    public function afterSaveArticle_Search( Article $article, int $article_id )
    {
        $searchModule = cms_utils::get_search_module();
        if( !is_object($searchModule) ) return;
        if( $article->status != $article::STATUS_PUBLISHED || ! $article->searchable ) {
            $searchModule->DeleteWords( $this->mod->GetName(), $article_id, 'article' );
        } else {
            $expiry = null;
            if( $this->settings->expired_searchable || $article->end_time > time() ) {
                if( $article->end_time > 0 ) $expiry = $article->end_time;
            }
            $searchModule->AddWords( $this->mod->GetName(), $article->id, 'article',
                                     $article->content.' '.$article->summary.' '.$article->title.' '.$article->title,
                                     $expiry );
        }
    }

    public function adminsearch_get_slave_classes()
    {
        $obj = new AdminSearchSlave( $this->mod, $this->artm );
        $tmp = [];
        $tmp['module'] = get_class( $this->mod );
        $tmp['class'] = get_class( $obj );
        $tmp['name'] = $obj->get_name();
        $tmp['description'] = $obj->get_description();
        $tmp['section_description'] = $obj->get_section_description();
        $tmp['object'] = $obj;
        return $tmp;
    }
} // class
