<?php
namespace News2;
use Iterator;
use Countable;

class ArticleSet implements Iterator, Countable
{

    private $_list;

    private $_total;

    private $_filter;

    public function __construct( ArticleFilter $filter, int $total, array $matches )
    {
        $this->_list = $matches;
        $this->_total = $total;
        $this->_filter = $filter;
    }

    public function rewind() { reset($this->_list);
    }
    public function current() { return current($this->_list);
    }
    public function key() { return key($this->_list);
    }
    public function next() { return next($this->_list);
    }
    public function valid() { return ($this->key() !== null);
    }
    public function count() { return count($this->_list);
    }

    public function __get( $key )
    {
        switch( $key ) {
            case 'pagecount':
                return ceil($this->_total / $this->_filter->limit);

            case 'page':
                return floor($this->_filter->offset / $this->_filter->limit) + 1;

            case 'total':
                return $this->_total;

            case 'filter':
                return $this->_filter;

            default:
                throw new \InvalidArgumentException("$key is not a gettable property of ".get_class($this));
        }
    }

    public function pageList(int $surround = 5)
    {
        $surround = max(2,min(50,$surround));

        $list = array();
        for( $i = 1; $i <= min($surround,$this->pagecount); $i++ ) {
            $list[] = (int)$i;
        }

        $x1 = max(1,(int)($this->page - $surround / 2));
        $x2 = min($this->pagecount - 1,(int)($this->page + $surround / 2) );
        for( $i = $x1; $i <= $x2; $i++ ) {
            $list[] = (int)$i;
        }

        for( $i = max(1,$this->pagecount - $surround); $i < $this->pagecount; $i++) {
            $list[] = (int)$i;
        }

        $list = array_unique($list);
        sort($list);
        return $list;
    }

    public function __set( $key, $val )
    {
        throw new \InvalidArgumentException("$key is not a settable property of ".get_class($this));
    }
} // class
