<?php
namespace News2;

// immitable
class ArticleFilter
{
    const SORT_MODIFIEDDATE = 'modified_date';
    const SORT_CREATEDATE = 'create_date';
    const SORT_NEWSDATE = 'news_date';
    const SORT_STARTDATE = 'start_date';
    const SORT_ENDDATE = 'end_date';
    const SORT_TITLE = 'title';
    const SORT_STATUS = 'status';
    const SORT_FIELD = 'field';
    const ORDER_ASC = 'ASC';
    const ORDER_DESC = 'DESC';

    private $_data = [
        'id_list'=>null, 'title_substr'=>null, 'useperiod'=>1, 'textmatch'=>null,
        'category_id'=>null, 'not_category_id'=>null, 'withchildren'=>false, 'usefields'=>false, 'author_id'=>null,
        'status'=>null, 'searchable'=>null, 'sortby'=>self::SORT_CREATEDATE, 'sortdata'=>null, 'sortorder'=>self::ORDER_DESC,
        'limit'=>1000, 'offset'=>0
        ];

    protected function __construct() {
    }

    public function __get( $key )
    {
        switch( $key ) {
            case 'usefields':
            case 'withchildren':
            case 'searchable':
                return (bool) $this->_data[$key];

            case 'status':
            case 'sortby':
            case 'sortdata':
            case 'sortorder':
                return trim( $this->_data[$key] );

            case 'id_list':
                return $this->_data[$key];

            case 'title_substr':
            case 'textmatch':
                return trim($this->_data[$key]);

            case 'useperiod':
            case 'not_category_id':
            case 'category_id':
            case 'author_id':
                return (int) $this->_data[$key];

            case 'limit':
                return (int) max(1,$this->_data[$key]);

            case 'offset':
                return (int) max(0,$this->_data[$key]);

            default:
                throw new \InvalidArgumentException("$key is not a gettable property of ".get_class($this));
        }
    }

    public function __set( $key, $val )
    {
        throw new \InvalidArgumentException("$key is not a settable property of ".get_class($this));
    }

    public function resetPagination() : ArticleFilter
    {
        $obj = clone $this;
        $obj->_data['limit'] = 1000000;
        $obj->_data['offset'] = 0;
        return $obj;
    }

    /**
     * @internal
     */
    public static function from_row( array $in )
    {
        $obj = new self;
        foreach( $in as $key => $val ) {
            switch( $key ) {
                case 'usefields':
                case 'withchildren':
                case 'searchable':
                    $obj->_data[$key] = cms_to_bool($val);
                    break;

                case 'status':
                case 'sortby':
                case 'sortdata':
                case 'sortorder':
                    $obj->_data[$key] = trim($val);
                    break;

                case 'id_list':
                    if( is_array($val) && count($val) ) $obj->_data[$key] = $val;
                    break;

                case 'title_substr':
                case 'textmatch':
                    $obj->_data[$key] = trim($val);
                    break;

                case 'useperiod':
                case 'not_category_id':
                case 'category_id':
                case 'author_id':
                    $obj->_data[$key] = (int) $val;
                    break;

                case 'limit':
                case 'offset':
                    $obj->_data[$key] = (int) $val;
                    break;
            }
        }

        return $obj;
    }
} // class
