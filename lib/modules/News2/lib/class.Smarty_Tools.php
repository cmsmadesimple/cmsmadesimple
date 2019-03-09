<?php
namespace News2;

final class Smarty_Tools
{

    private $catm;

    public function __construct( CategoriesManager $catm )
    {
        $this->catm = $catm;
    }

    /**
     * A method to get a category given either an id, or an alias
     *
     * @param mixed $in Either a category id or alias
     * @param Category|null If successful, the category object will be returned.
     */
    public function get_category( $in )
    {
        if( $in instanceof Category ) return $in;
        if( is_int($in) ) {
            return $this->catm->loadByID($in);
        }
        else if( is_string($in) ) {
            return $this->catm->loadByAlias($in);
        }
    }

    /**
     * Test whether the specified category a is a child of category b.
     *
     * @param mixed $a Either a category id or alias, or Category object
     * @param mixed $b Either a category id or alias, or Category object
     * @return bool
     */
    public function category_childof( $a, $b ) : bool
    {
        $a = $this->get_category($a);
        $b = $this->get_category($b);
        if( !$a || !$b ) return FALSE;

        if( $a->id == $b->id ) return false; // trivial exclusion

        // note: this is relatively efficient, and does not always talk to the database thanks to caching.
        //       but another method would be to load all categories as an associative array indexed by id.
        while( $a->parent_id > 0 && $a->parent_id != $b->id ) {
            $a = $this->catm->loadByID( $a->parent_id );
        }
        if( $a->id == $b->id ) return TRUE;
        return FALSE;
    }
} // class
