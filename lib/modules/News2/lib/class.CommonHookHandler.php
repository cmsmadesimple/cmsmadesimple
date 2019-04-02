<?php
namespace News2;
use News2;
use cms_utils;

class CommonHookHandler
{

    private $mod;

    private $settings;

    private $artm;

    public function __construct( News2 $mod, Settings $settings, ArticleManager $artm )
    {
        $this->mod = $mod;
        $this->settings = $settings;
        $this->artm = $artm;

        $this->mod->cms->get_hook_manager()->emit( 'News2::createDraftAlerts', [ $this, 'createDraftAlerts'] );
        $this->mod->cms->get_hook_manager()->emit( 'News2::createNeedsApprovalAlerts', [ $this, 'createPendingAlerts'] );
    }

    public function createDraftAlerts()
    {
        debug_to_log(__METHOD__);
        $opts = [ 'status'=>Article::STATUS_DRAFT, 'useperiod'=>1 ];
        $filter = $this->artm->createFilter( $opts );
        $articles = $this->artm->loadByFilter( $filter );
        if( !count($articles) ) return;

        $alert = new DraftArticlesAlert( $this->mod, count($articles) );
        $alert->save();
    }

    public function createPendingAlerts()
    {
        debug_to_log(__METHOD__);
        $opts = [ 'status'=>Article::STATUS_NEEDSAPPROVAL, 'useperiod'=>1 ];
        $filter = $this->artm->createFilter( $opts );
        $articles = $this->artm->loadByFilter( $filter );
        if( !count($articles) ) return;

        $alert = new NeedsApprovalArticlesAlert( $this->mod, count($articles) );
        $alert->save();
    }
} // class
