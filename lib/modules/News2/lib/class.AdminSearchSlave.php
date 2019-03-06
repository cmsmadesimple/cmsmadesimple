<?php
namespace News2;
use News2;
use AdminSearch_slave;
use AdminSearch_tools;

final class AdminSearchSlave extends AdminSearch_slave
{
    private $mod;
    private $artm;

    public function __construct( News2 $mod, ArticleManager $artm )
    {
        $this->mod = $mod;
        $this->artm = $artm;
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
        $filter = $this->artm->createFilter( [ 'textmatch'=>$this->get_text(), 'useperiod'=>0, 'limit'=>100 ] );
        $articles = $this->artm->loadByFilter( $filter );
        if( !count($articles) ) return;

        $create_search_text = function( Article $article, string $search_text ) {

            $get_text = function( $text, $search_text ) {
                $search_text = cms_htmlentities($search_text);
                $text = cms_htmlentities($text);
                $pos = strpos($text, $search_text);
                if( $pos === FALSE ) return;

                // the text we return will be 5 chars around the search text
                // and put a span around the matching text.
                // and collapse newlines.
                $start = max(0, $pos - 50);
                $end = min(strlen($text ), $pos+50);
                $text = substr($text, $start, $end-$start );
                $text = str_replace($this->get_text(), '<span class="search-oneresult">'.$search_text.'</span>', $text);
                $text = str_replace("\r", '', $text);
                $text = str_replace("\n", '', $text);
                return $text;
            };

            // find the first occurance of keyword, in summary, content, or in a field
            if( ($tmp = $get_text($article->summary, $search_text)) ) return $tmp;
            if( ($tmp = $get_text($article->content, $search_text)) ) return $tmp;

            foreach( $article->fields as $fdid => $content ) {
                if( is_array( $content ) ) continue;
                if( ($tmp = $get_text($content, $search_text)) ) return $tmp;
            }
        };

        $out = null;
        debug_to_log('searching for '.$this->get_text());
        foreach( $articles as $article ) {
            debug_to_log($article);
            $text = $create_search_text( $article, $this->get_text() );
            if( $text ) {
                $url = $this->mod->create_url('m1_', 'admin_edit_article', '', [ 'article'=> $article->id ]);
                $tmp =
                    [
                        'title' => $article->title,
                        'description' => AdminSearch_tools::summarize($article->summary ?? $article_content),
                        'edit_url' => $url,
                        'text' => $create_search_text( $article, $this->get_text() )
                        ];
                $out[] = $tmp;
            }
        }
        debug_to_log($out,'after adminsearch');
        return $out;
    }
} // class