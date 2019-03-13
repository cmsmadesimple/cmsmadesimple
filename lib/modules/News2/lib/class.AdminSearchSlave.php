<?php
namespace News2;
use News2;
use AdminSearch_slave;

final class AdminSearchSlave extends AdminSearch_slave
{
    private $mod;
    private $mgr;

    public function __construct( News2 $mod, ArticleManager $mgr )
    {
        $this->mod = $mod;
        $this->mgr = $mgr;
    }

    public function get_name()
    {
        return $this->mod->Lang('lbl_adminsearch');
    }

    public function get_description()
    {
        return $this->mod->Lang('desc_adminsearch');
    }

    public function check_permission()
    {
        return $this->mod->CheckPermission( News2::MANAGE_PERM );
    }

    public function get_matches()
    {
        $hilight_contents = function( string $in, string $searchtext ) {
            // note: we don't strip tags, we convert them to entities.
            $searchtext = cms_htmlentities($searchtext);
            $text = cms_htmlentities($in);
            $text = str_replace("\r",' ',$text);
            $text = str_replace("\n",' ',$text);
            $text = str_replace($searchtext,'<span class="search_oneresult">'.$searchtext.'</span>',$text);
            return $text;
        };

        // outputs an array of associative array elements
        // each element has a title, a description, An edit_url, and a text field

        $searchtext = $this->get_text();
        $opts = ['textmatch'=>$searchtext, 'useperiod'=>-1, 'usefields'=>true, 'sortby'=>ArticleFilter::SORT_MODIFIEDDATE, 'sortorder'=>ARticleFilter::ORDER_DESC ];
        $filter = $this->mgr->createFilter( $opts );
        $articles = $this->mgr->loadByFilter( $filter );
        if( empty($articles) ) return;

        $out = null;
        foreach( $articles as $article ) {
            $rec = null;
            $rec['title'] = $article->title;
            $rec['description'] = substr(strip_tags($article->summary),0,255);
            if( !$rec['description'] ) {
                $rec['description'] = substr(strip_tags($article->content),0,255);
            }
            $rec['edit_url'] = $this->mod->create_url( 'm1_', 'admin_edit_article', '', [ 'news_id'=>$article->id ]);

            // now, find the text that has this content in it.
            $text = null;
            if( strpos($article->summary,$searchtext) !== FALSE) {
                $text = $hilight_contents($article->summary,$searchtext);
            }
            else if( strpos($article->content,$searchtext) !== FALSE) {
                $text = $hilight_contents($article->content,$searchtext);
            }
            else {
                foreach( $article->fields as $key => $val ) {
                    if( strpos($val,$searchtext) !== FALSE ) {
                        $text = $hilight_contents($val,$searchtext);
                        break;
                    }
                }
            }
            if( $text ) {
                $rec['text'] = $text;
                $out[] = $rec;
            }
        }
        return $out;
    }
} // end of class

?>
