<?php
namespace Search;
use StdClass;

class SearchItemCollection
{

    var $_ary;

    var $maxweight;

    public function __construct()
    {
        $this->_ary = array();
        $this->maxweight = 1;
    }

    public function AddItem($title, $url, $txt, $weight = 1, $module = '', $modulerecord = 0)
    {
        if( $txt == '' ) $txt = $url;
        $exists = false;

        foreach ($this->_ary as $oneitem) {
            if ($url == $oneitem->url) {
                $exists = true;
                break;
            }
        }

        if (!$exists) {
            $newitem = new StdClass();
            $newitem->url = $url;
            $newitem->urltxt = search_CleanupText($txt);
            $newitem->title = $title;
            $newitem->intweight = intval($weight);
            if (intval($weight) > $this->maxweight) $this->maxweight = intval($weight);
            if (!empty($module) ) {
                $newitem->module = $module;
                if( intval($modulerecord) > 0 )	$newitem->modulerecord = $modulerecord;
            }
            $this->_ary[] = $newitem;
        }
    }

    public function CalculateWeights()
    {
        foreach ($this->_ary as $oneitem) {
            $oneitem->weight = intval(($oneitem->intweight / $this->maxweight) * 100);
        }
    }

    public function Sort()
    {
        $fn = function($a,$b) {
            if ($a->urltxt == $b->urltxt) return 0;
            return ($a->urltxt < $b->urltxt ? -1 : 1);
        };

        usort($this->_ary, $fn);
    }
} // end of class
