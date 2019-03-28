<?php

use CMSMS\HookManager;
use News2\AdminSearchSlave;
use News2\Article;
use News2\Settings;
use News2\AdminHookHandler;
use News2\CommonHookHandler;
use News2\FieldTypeManager;
use News2\CategoriesManager;
use News2\FieldDefManager;
use News2\ArticleManager;
use News2\HookTask;
use News2\FieldType;
use News2\FieldTypes\TextFieldType;
use News2\FieldTypes\TextAreaFieldType;
use News2\FieldTypes\NumberFieldType;
use News2\FieldTypes\SelectFieldType;
use News2\FieldTypes\MultiSelectFieldType;
use News2\FieldTypes\AttachmentFieldType;
use News2\FieldTypes\ImageAttachmentFieldType;
use News2\FieldTypes\BooleanFieldType;
use News2\FieldTypes\StaticFieldType;
use News2\FieldTypes\SeparatorFieldType;
use News2\FieldTypes\RelatedArticlesFieldType;

// NOTE: cannot lazy load this module because smarty plugins need to be registered
class News2 extends CMSModule
{
    /**#@+
     * MANAGE_PERM can do anything with news articles
     * OWN_PERM can create articles and edit their articles, and but not set as 'published'
     * DELOWN_PERM can delete their news articles
     * APPROVE_PERM can view any article, and can set to published, but cannot edit articles
     *
     * @var string
     */
    const MANAGE_PERM = 'Manage News2 Articles';
    const OWN_PERM = 'Create and edit my News2 articles';
    const DELOWN_PERM = 'Delete my News2 articles';
    const APPROVE_PERM = 'Approve News2 articles for display';
    /**#@-*/

    /**#@+
     * @ignore
     */
    public function GetVersion()
    {
        return '0.0.6';
    }

    public function MinimumCMSVersion()
    {
        return '2.2.900';
    }

    public function IsPluginModule()
    {
        return true;
    }

    public function HasAdmin()
    {
        return true;
    }

    public function GetAdminSection()
    {
        return 'content';
    }

    public function GetAuthor()
    {
        return 'Robert Campbell';
    }

    public function GetAuthorEmail()
    {
        return 'calguy1000@gmail.com';
    }

    public function GetHelp()
    {
        return file_get_contents(__DIR__.'/doc/help.inc');
    }

    public function GetChangelog()
    {
        return file_get_contents(__DIR__.'/doc/changelog.txt');
    }

    /**
     * @ignore
     */
    public function VisibleToAdminUser()
    {
        return $this->CheckPermission( self::MANAGE_PERM ) ||
            $this->CheckPermission( self::OWN_PERM ) ||
            $this->CheckPermission( self::APPROVE_PERM );
    }

    public function InitializeAdmin()
    {
        $this->initializeAdminHooks();
        return parent::InitializeAdmin();
    }

    public function InitializeFrontend()
    {
        $this->RegisterModulePlugin();
        return parent::InitializeFrontend();
    }

    // initializecommon??
    public function InitializeCommon()
    {
        // executed from both initializeadmin and initializefrontend
        // may be called multiple times.
        $smarty = $this->app->GetSmarty();
        if( !$smarty ) return;

        static $_smarty_plugins = null;
        if( $_smarty_plugins ) return;
        $_smarty_plugins = new \News2\Smarty_plugins( $this->categoriesManager(), $this->articleManager(), $smarty, $this->app->get_cache_driver() );

        $smarty->assign('News2Tools', new \News2\Smarty_Tools( $this->categoriesManager()) );
        $this->initializeCommonHooks();

        // register field definitions
        $this->registerFieldType( new TextFieldType($this) );
        $this->registerFieldType( new TextAreaFieldType($this) );
        $this->registerFieldType( new NumberFieldType($this) );
        $this->registerFieldType( new SelectFieldType($this) );
        $this->registerFieldType( new AttachmentFieldType($this) );
        $this->registerFieldType( new ImageAttachmentFieldType($this) );
        $this->registerFieldType( new BooleanFieldType($this) );
        $this->registerFieldType( new MultiSelectFieldType($this) );
        $this->registerFieldType( new StaticFieldType($this) );
        $this->registerFieldType( new SeparatorFieldType($this) );
        $this->registerFieldType( new RelatedArticlesFieldType($this) );
    }

    public function GetAdminMenuItems()
    {
        $out = null;
        if( $this->VisibleToAdminUser() ) $out[] = CmsAdminMenuItem::from_module($this);

        if( $this->CheckPermission(self::MANAGE_PERM) || $this->CheckPermission('Modify Site Preferences') ) {
            $obj = new CmsAdminMenuItem();
            $obj->module = $this->GetName();
            $obj->section = 'siteadmin';
            $obj->title = $this->Lang('title_news_settings');
            $obj->description = $this->Lang('desc_news_settings');
            $obj->action = 'admin_settings';
            $out[] = $obj;
        }
        return $out;
    }

    public function create_url($id, $action, $returnid='', $params=[],
                               $inline=false, $targetcontentonly=false, $prettyurl='')
    {
        $nopretty = cms_to_bool(get_parameter_value($params,'nopretty'));
        if( isset($params['nopretty']) ) unset($params['nopretty']);
        if( $nopretty ) {
            // force ugly URL, but don't want the nopretty or noslug params on the output URL.
            if( isset($params['noslug']) ) unset($params['noslug']);
            $prettyurl = ':NOPRETTY:';
        }
        return parent::create_url( $id, $action, $returnid, $params, $inline, $targetcontentonly, $prettyurl );
    }

    public function get_pretty_url($id, $action, $returnid='', $params=[], $inline=false)
    {
        if( $action == 'default' && $this->settings()->pretty_category_url ) {
            $category_id = (int) get_parameter_value($params,'category_id');
            if( $category_id < 1 ) return;

            // News2/bycategory/$cat_id/$returnid/$category-path
            // want to return this pretty URL, but still add pagination (limit, sorting can be provided by cms_moduel_hint)
            $page = (int) get_parameter_value($params,'news_page');
            $category = $this->categoriesManager()->loadByID( $category_id );
            if( !$category ) return;
            $category_path = str_replace(' | ','_',$category->long_name);
            $category_path = str_replace('__','_',$category_path);
            $out = "News2/bycategory/$category_id/$returnid/".munge_string_to_url($category_path);
            if( $page > 1 ) $out .= "?news_page=$page";
            return $out;
        }

        if( $action != 'detail' ) return;
        $article_id = get_parameter_value($params,'article');
        if( $article_id < 1 ) return;
        $noslug = cms_to_bool(get_parameter_value( $params, 'noslug' ));
        $nopretty = cms_to_bool(get_parameter_value( $params, 'nopretty' ));
        if( $nopretty ) return;

        $article = $this->articleManager()->loadByID( $article_id );
        if( !$article ) return;

        if( !$noslug && $article->url_slug ) return $article->url_slug;

        if( !$returnid ) $returnid = $this->getDefaultDetailPage();
        $date_str = strftime('%Y-%m-%d',$article->news_date);
        $out = "News2/$article_id/$returnid/{$date_str}-".munge_string_to_url( $article->title );
        return $out;
    }

    public function CreateStaticRoutes()
    {
        $artm = $this->articleManager();
        $str = $this->GetName();
        $upper = strtoupper($str[0]);
        $lower = strtolower($str[0]);
        $suffix = substr($str,1);
        $prefix = "[{$upper}{$lower}]{$suffix}";

        cms_route_manager::del_static('',$this->GetName());
        $route = new CmsRoute('/'.$prefix.'\/(?P<article>[0-9]+)\/(?P<returnid>[0-9]+)\/(?P<junk>.*?)$/',
                              $this->GetName(), ['action'=>'detail'] );
        cms_route_manager::add_static( $route );
        $route = new CmsRoute('/'.$prefix.'\/bycategory\/(?P<category_id>[0-9]+)\/(?P<returnid>[0-9]+)\/(.*?)$/',
                              $this->GetName(), ['action'=>'default'] );
        cms_route_manager::add_static( $route );

        $offset = 0;
        $detailpage = $this->getDefaultDetailPage();
        while( 1 ) {
            $list = $artm->loadArticlesWithURLSlug($offset);
            if( !$list || empty($list) ) break;
            foreach( $list as $article ) {
                $artm->registerRouteForArticle( $article, $detailpage );
            }
            $offset += count($list);
        }
    }

    public function SearchResultWithParams($returnid, $articleid, $attr = '', $params = '')
    {
        $result = [];
        if( $attr != 'article' ) return $result;

        $article = $this->articleManager()->loadByID( $articleid );
        if( !$article ) return $result;

        $result[0] = $this->GetFriendlyName();
        $result[1] = $article->title;
        $detailpage = $returnid;
        if( isset($params['detailpage']) ) $detailpage = $this->resolvePageAlias( $params['detailpage'] );
        // do not accept detailtemplateparam here, because pretty urls would get in the way anyway.
        $result[2] = $this->create_url( 'cntnt01', 'detail', $detailpage, ['article'=>$articleid] );
        return $result;
    }

    public function SearchReindex(&$searchModule)
    {
        $artm = $this->articleManager();

        $opts = ['status'=>Article::STATUS_PUBLISHED, 'searchable'=>1 ];
        $filter = $artm->createFilter( $opts );
        $articles = $artm->loadByFilter( $filter );

        foreach( $articles as $article ) {
            $expiry = null;
            if( $this->settings()->expired_searchable || $article->end_time > time() ) {
                if( $article->end_time > 0 ) $expiry = $article->end_time;
            }
            $searchModule->AddWords( $this->GetName(), $article->id, 'article',
                                     $article->content.' '.$article->summary.' '.$article->title.' '.$article->title,
                                     $expiry );
        }
    }

    public function HasCapability($capability, $params = array())
    {
        switch( $capability ) {
            case CmsCoreCapabilities::PLUGIN_MODULE:
            case CmsCoreCapabilities::ADMINSEARCH:
            case CmsCoreCapabilities::TASKS:
                return TRUE;
        }
        return FALSE;
    }

    public function get_adminsearch_slaves()
    {
        $out = null;
        $out[] = new AdminSearchSlave($this, $this->articleManager());
        return $out;
    }

    public function get_tasks()
    {
        $out = null;
        if( $this->settings()->alert_draft ) {
            $out[] = new HookTask( 'News2::createDraftAlerts' );
        }
        if( $this->settings()->alert_needsapproval ) {
            $out[] = new HookTask( 'News2::createNeedsApprovalAlerts' );
        }
        return $out;
    }

    /**#@-*/

    ////  MY FUNCTIONS ////

    /**
     * Register a new field type
     *
     * This should be done in the InitializeAdmin method of your module.
     *
     * @param FieldType $type
     */
    public function registerFieldType( FieldType $type )
    {
        $this->fieldTypeManager()->registerType( $type );
    }

    /**
     * @internal
     */
    public function resolvePageAlias( $alias )
    {
        $txt = trim($alias);
        if( !$txt ) return;

        $manager = cmsms()->GetHierarchyManger();
        $node = null;
        if( is_numeric($txt) && (int) $txt > 0 ) {
            $node = $manager->find_by_tag('id',(int)$txt);
        }
        else {
            $node = $manager->find_by_tag('alias',$txt);
        }
        if( $node ) return (int)$node->get_tag('id');
    }

    /**
     * @internal
     */
    protected function initializeCommonHooks()
    {
        static $_obj;
        if( !$_obj ) $_obj = new CommonHookHandler( $this, $this->settings(), $this->articleManager() );
    }

    /**
     * @internal
     */
    protected function initializeAdminHooks()
    {
        static $_obj;
        if( !$_obj ) $_obj = new AdminHookHandler( $this, $this->settings(), $this->articleManager() );
    }

    /**
     * @internal
     */
    protected function settings() : Settings
    {
        static $_obj;
        if( !$_obj ) {
            $config = $this->GetConfig();
            $opts = [];
            $opts['editor_summary_enabled'] = $config['news2_summary_enabled'];
            $opts['editor_summary_wysiwyg'] = $config['news2_summary_usewysiwyg'];
            $opts['editor_urlslug_required'] = $config['news2_urlslug_required'];
            $opts['editor_category_required'] = $config['news2_category_required'];
            $opts['detailpage'] = $config['news2_detailpage'];
            $opts['editor_own_editpublished'] = $config['news2_own_editpublsiehd'];
            $opts['editor_own_setpublished'] = $config['news2_own_setpublished'];
            $opts['expired_searchable'] = $config['news2_expired_searchable'];
            $opts['detail_show_expired'] = $config['news2_detail_show_expired'];
            $opts['alert_draft'] = $config['news2_alert_on_draft'];
            $opts['alert_needsapproval'] = $config['news2_alert_needsapproval'];
            $opts['pretty_category_url'] = get_parameter_value($config,'news2_default_pretty_bycategory_url',true);
            $opts['bycategory_withchildren'] = get_parameter_value($config,'news2_default_bycategory_withchildren',true);
            $_obj = Settings::from_row( $opts );
        }
        return $_obj;
    }

    /**
     * @internal
     */
    public function getDefaultDetailPage() : int
    {
        $detailpage = $this->settings()->detailpage;
        if( $detailpage ) $detailpage = $this->resolvePageAlias( $detailpage );
        if( !$detailpage ) $detailpage = cmsms()->GetContentOperations()->GetDefaultContent();
        return $detailpage;
    }

    /**
     * @internal
     */
    protected function fieldTypeManager() : FieldTypeManager
    {
        static $_obj;
        if( !$_obj ) $_obj = new FieldTypeManager( $this );
        return $_obj;
    }

    /**
     * @internal
     */
    protected function categoriesManager() : CategoriesManager
    {
        static $_obj;
        if( !$_obj ) $_obj = new CategoriesManager( $this->GetDb(), $this,
       	      $this->app->get_cache_driver() );
        return $_obj;
    }

    /**
     * @internal
     */
    protected function fielddefManager() : FieldDefManager
    {
        static $_obj;
        if( !$_obj ) {
            $db = $this->GetDb();
            $_obj = new FielddefManager( $db, $this, $this->fieldTypeManager(),
            $this->app->get_cache_driver() );
        }
        return $_obj;
    }

    /**
     * @internal
     */
    protected function articleManager() : ArticleManager
    {
        static $_obj;
        if( !$_obj ) {
            $cache_driver = $this->app->get_cache_driver();
            $_obj = new ArticleManager( $this->GetDb(), $this,
                                        $this->fielddefManager(), $this->categoriesManager(),
                                        $cache_driver);
        }
        return $_obj;
    }

    /**
     * @internal
     */
    public function canAddArticle() : bool
    {
        return $this->CheckPermission( self::MANAGE_PERM ) || $this->CheckPermission( self::OWN_PERM );
    }

    /**
     * @internal
     */
    public function canEditArticle(int $id) : bool
    {
        if( $this->CheckPermission( self::MANAGE_PERM ) ) return TRUE;
        if( !$this->CheckPermission( self::OWN_PERM ) ) return FALSE;

        $article = $this->articleManager()->loadByID( $id );
        if( !$article ) return FALSE;
        if( $article->author_id > 0 && get_userid(false) == $article->author_id ) {
            // owners cannot edit disabled articles
            if( $article->status == $article::STATUS_DISABLED ) return FALSE;
            if( $article->status == $article::STATUS_PUBLISHED && !$this->settings()->editor_own_editpublished ) return FALSE;
            return TRUE;
        }
        return FALSE;
    }

    /**
     * @internal
     */
    public function canDeleteArticle(int $id) : bool
    {
        if( $this->CheckPermission( self::MANAGE_PERM ) ) return TRUE;
        if( !$this->CheckPermission( self::OWN_PERM ) ) return FALSE;
        if( !$this->CheckPermission( self::DELOWN_PERM ) ) return FALSE;

        $article = $this->articleManager()->loadByID( $id );
        if( $article && $article->author_id > 0 && get_userid(false) == $article->author_id ) return TRUE;
        return FALSE;
    }

    /**
     * @internal
     */
    protected function ResolveTemplate(string $type_suffix, string $template, string $dflt = null)
    {
        $tempate = trim($template);
        if( !$template ) {
            $template = $dflt;
            try {
                $tpl = CmsLayoutTemplate::load_dflt_by_type('News2::'.$type_suffix);
                if( !is_object($tpl) ) $template = $tpl->get_name();
            }
            catch( CmsDataNotFoundException $e ) {
                // nothing here.
            }
        }
        return $template;
    }
} // class
