<?php
namespace News2;
use News2;

class Smarty_plugins
{

    private $catm;

    private $artm;

    public function __construct( CategoriesManager $catm, ArticleManager $artm, $smarty )
    {
        $this->catm = $catm;
        $this->artm = $artm;
        $smarty->register_function( 'news2_category', [ $this, 'news2_category'] );
        $smarty->register_function( 'news2_category_name', [ $this, 'news2_category_name'] );
        $smarty->register_function( 'news2_nextarticle', [ $this, 'news2_nextpublished'] );
        $smarty->register_function( 'news2_prevarticle', [ $this, 'news2_prevpublished'] );
    }

    public function news2_nextpublished( $params, $template )
    {
        $article_id = (int) get_parameter_value( $params, 'id' );
        $article_id = (int) get_parameter_value( $params, 'from', $article_id );

        // note: we don't handle category filtering here because of category hierarchies
        $out = null;
        if( $article_id > 0 ) {
            $article = $this->artm->loadByID( $article_id );
            if( $article ) $out = $this->artm->loadFirstAvailableAfter( $article );
        }

        $assign = trim(get_parameter_value($params,'assign'));
        if( $assign ) {
            $template->assign($assign, $out);
        } else {
            return $out;
        }
    }

    public function news2_prevpublished( $params, $template )
    {
        $article_id = (int) get_parameter_value( $params, 'id' );
        $article_id = (int) get_parameter_value( $params, 'from', $article_id );

        // note: we don't handle category filtering here because of category hierarchies making the problem more complex
        //       and the article query and resultset classes modified.
        $out = null;
        if( $article_id > 0 ) {
            $article = $this->artm->loadByID( $article_id );
            if( $article ) $out = $this->artm->loadLastAvailableBefore( $article );
        }

        $assign = trim(get_parameter_value($params,'assign'));
        if( $assign ) {
            $template->assign($assign, $out);
        } else {
            return $out;
        }
    }

    public function news2_category( $params, $template )
    {
        $catid = (int) get_parameter_value( $params, 'cat' );
        $catid = (int) get_parameter_value( $params, 'catid', $catid );
        $alias = trim(get_parameter_value( $params, 'alias') );
        if( $catid > 0 ) $alias = null;

        $cat = null;
        if( $alias ) {
            $cat = $this->catm->loadByAlias( $alias );
        } else if( $catid > 0 ) {
            $cat = $this->catm->loadByID( $catid );
        }

        $assign = trim(get_parameter_value( $params, 'assign'));
        if( $assign ) {
            $template->assign($assign, $cat);
            return;
        }
        return $cat;
    }

    public function news2_category_name( $params, $template )
    {
        $assign = trim(get_parameter_value( $params, 'assign'));
        $long = cms_to_bool(get_parameter_value( $params, 'long') );
        unset($params['assign']);
        $cat = self::news2_category( $params, $template );
        $out = null;
        if( $cat ) {
            $out = $cat->name;
            if( $long ) $out = $cat->long_name;
        }

        $assign = trim(get_parameter_value( $params, 'assign'));
        if( $assign ) {
            $template->assign( $assign, $out );
        } else {
            return $out;
        }
    }
} // class
