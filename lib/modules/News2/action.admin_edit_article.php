<?php
namespace News2;
use News2;
use CMSMS\HookManager;
use cms_route_manager;

if( !isset($gCms) ) exit;
if( !$this->CheckPermission( News2::MANAGE_PERM ) && !$this->CheckPermission( News2::OWN_PERM ) ) exit;

try {
    $artm = $this->articleManager();
    $catm = $this->categoriesManager();
    $fdm  = $this->fielddefManager();
    $fielddefs = $fdm->loadAllAsHash();
    $fieldtypes = $this->fieldTypeManager()->getAll();

    $get_config_status = function($val) {
        switch( $val ) {
            case Article::STATUS_PUBLISHED:
            case Article::STATUS_DRAFT:
            case Article::STATUS_NEEDSAPPROVAL:
            case Article::STATUS_DISABLED:
                return $val;
            default:
                return Article::STATUS_DRAFT;
        }
    };

    $opts =
        [
            'author_id' => get_userid(),
            'news_date' => strtotime('00:00'), // start of today
            'status' => $get_config_status($config['news2_dflt_status']),
            'use_endtime' => cms_to_bool(get_parameter_value($config,'news2_dflt_useendtime',0)),
            'searchable' => isset($config['news2_dflt_searchable']) ? cms_to_bool($config['news2_dflt_searchable']) : true
        ];
    if( $opts['use_endtime'] ) {
        $opts['start_time'] = strtotime('-1 day 00:00'); // starts tomorrow
        $opts['end_time'] = strtotime('+1 year 00:00');  // ends in one year
    }

    $article = $artm->createNew( $opts );
    $news_id = get_parameter_value( $params, 'news_id' );
    if( $news_id ) {
        $article = $artm->loadByID( $news_id );
        if( !$this->CheckPermission( News2::MANAGE_PERM ) ) {
            if( $article->author_id != get_userid() ) throw new \RuntimeException( $this->Lang('err_edit_nopermission' ) );
            if( $article->status == $article::STATUS_PUBLISHED && !$this->settings()->editor_own_editpublished ) {
                throw new \RuntimeException( $this->Lang('err_edit_cannoteditpublished') );
            }
        }
    }

    HookManager::do_hook( 'News2::beforeEditArticle', $article );

    if( !empty($_POST) ) {
        if( isset($_POST['cancel']) ) {
            $this->RedirectToAdminTab();
        }

        try {
            // fill in the article object.
            $mktime = function( array $in, string $prefix, $is_start = false ) {
                $mo = (int) $in[$prefix.'Month'];
                $dd = (int) $in[$prefix.'Day'];
                $yr = (int) $in[$prefix.'Year'];
                $hh = (int) $in[$prefix.'Hour'];
                $mm = (int) $in[$prefix.'Minute'];
                $ss = $is_start ? 00 : 59;

                // if the date is before jan-1-1970 ... that's no date.
                if( $yr <= 1970 && $mo == 1 && $dd == 1 ) return;
                return mktime( $hh, $mm, $ss, $mo, $dd, $yr );
            };

            $article->title = filter_var( $_POST['title'], FILTER_SANITIZE_STRING );
            $article->category_id = (int) get_parameter_value($_POST,'category_id');
            $article->summary = $_POST['summary'] ?? null;
            $article->content = $_POST['content'];
            $article->status = filter_var( $_POST['status'], FILTER_SANITIZE_STRING );
            $article->searchable = cms_to_bool( $_POST['searchable']);
            $article->url_slug = filter_var( $_POST['url_slug'], FILTER_SANITIZE_STRING );
            $article->news_date = $mktime( $_POST, 'newsdate_', true );
            $article->start_time = null;
            $article->end_time = null;
            if( cms_to_bool( $_POST['use_endtime']) ) {
                $article->start_time = $mktime( $_POST, 'starttime_', true );
                $article->end_time = $mktime( $_POST, 'endtime_' );
            }
            foreach( $fielddefs as $fd ) {
                if( !isset($fieldtypes[$fd->type]) ) continue;
                $fldtype = $fieldtypes[$fd->type];
                $value = $fldtype->handleForArticle( $fd, $_POST );
                $article->setFieldVal( $fd->name, $value );
            }
            if( $this->CheckPermission( News2::MANAGE_PERM ) ) {
                // only managers can adjust an article's author.
                if( isset($_POST['author_id']) ) {
                    $val = (int) $_POST['author_id'];
                    if( $val == -1000000 ) $val = 0;
                    $article->author_id = $val;
                }
            }

            // do validations
            if( ! $article->content ) throw new \RuntimeException( $this->Lang('err_contentrequired') );
            if( $article->end_time && $article->start_time ) {
                if( $article->end_time <= $article->start_time ) throw new \RuntimeException( $this->Lang('err_invaliddates') );
            }
            if( $this->settings()->editor_urlslug_required && !$article->urlslug ) {
                throw new \RuntimeException( $this->Lang('err_urlslug_empty') );
            }
            if( $article->start_time && $article->end_time && $article->end_time < $article->start_time ) {
                throw new \RuntimeException( $this->Lang('err_startendtime_invalid') );
            }
            if( $article->url_slug ) {
                // test the url slug if supplied
                if( startswith( $article->url_slug, '/' ) || endswith( $article->url_slug, '/' ) ) {
                    throw new \RuntimeException( $this->Lang('err_edit_urlsluginvalid') );
                }
                if( $artm->urlSlugExists( $article->url_slug, $article->id ) ) {
                    throw new \RuntimeException( $this->Lang('err_edit_urlslugused') );
                }
            }

            HookManager::do_hook( 'News2::beforeSaveArticle', $article );

            // save the thing
            $article_id = $artm->save( $article );

            // this will update the search and stuff
            HookManager::do_hook( 'News2::afterSaveArticle', $article, $article_id );

            if( $article->id ) {
                audit($article->id,$this->GetName(),'Edited article: '.$article->title);
            } else {
                audit($article->id,$this->GetName(),'Created article: '.$article->title);
            }

            // done
            if( !isset( $_POST['apply']) ) {
                $this->SetMessage( $this->Lang('msg_saved') );
                $this->RedirectToAdminTab();
            } else {
                echo $this->showMessgae( $this->Lang('msg_saved') );
            }
        }
        catch( \Exception $e ) {
            echo $this->ShowErrors( $e->GetMessage() );
        }
    }

    // todo: display warning here if not setup for pretty urls, or no default detail page configured.

    $status_list = [ $article::STATUS_DRAFT => $this->Lang('status_draft') ];
    if( $this->CheckPermission( News2::MANAGE_PERM ) ) {
        $status_list[$article::STATUS_PUBLISHED] = $this->Lang('status_published');
        $status_list[$article::STATUS_DISABLED] = $this->Lang('status_disabled');
    } else if( $this->CheckPermission( News2::OWN_PERM ) ) {
        $status_list[$article::STATUS_NEEDSAPPROVAL] = $this->Lang('status_needsapproval');
        if( $article->status == $article::STATUS_PUBLISHED || $this->settings()->editor_own_setpublished ||
            $this->CheckPermission( News2::APPROVE_PERM ) ) {
            $status_list[$article::STATUS_PUBLISHED] = $this->Lang('status_published');
        }
    }

    $author_list = $author_name = null;
    if( $this->CheckPermission( News2::MANAGE_PERM )) {
        $users = $gCms->GetUserOperations()->LoadUsers();
        if( !empty($users) ) {
            $author_list['-1000000'] = $this->Lang('none');
            array_walk($users, function($user) use (&$author_list){
                    if( check_permission( $user->id, News2::MANAGE_PERM) || check_permission($user->id, News2::OWN_PERM ) ) {
                        $author_list[$user->id] = $user->username;
                    }
                });
        }
        // owner list should be an array of uids => usernames
        // FOR active admin users WHO have either the News2::MANAGE_PERM OR the News2::OWN_PERM
    }
    if( empty($author_list) && $article->author_id > 0 ) {
        // user can own perms, AND can approve them
        // so we will display the users username.
        $user = $gCms->GetUserOperations()->LoadUsersByID( $article->author_id );
        if( $user ) $author_name = $user->username;
    }

    $category_tree_list = $catm->getCategoryList( [-1 => $this->Lang('none')] );
    $tpl = $smarty->CreateTemplate( $this->GetTemplateResource('admin_edit_article.tpl'), null, null, $smarty );
    $tpl->assign('article',$article);
    $tpl->assign('author_list',$author_list);
    $tpl->assign('author_name',$author_name);
    $tpl->assign('fieldtypes',$fieldtypes);
    $tpl->assign('fielddef_list',$fielddefs);
    $tpl->assign('category_tree_list',$category_tree_list);
    $tpl->assign('status_list',$status_list);
    $tpl->assign('settings',$this->settings());
    $tpl->display();
}
catch( \Exception $e ) {
    $this->SetError( $e->GetMessage() );
    $this->RedirectToAdminTab();
}
