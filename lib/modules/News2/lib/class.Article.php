<?php
namespace News2;

class Article
{
    const STATUS_PUBLISHED = 'published';
    const STATUS_DRAFT = 'draft';
    const STATUS_NEEDSAPPROVAL = 'needsapprove';
    const STATUS_DISABLED = 'disabled';

    private $_data = [
        'id'=>null, 'category_id'=>null, 'title'=>null, 'summary'=>null, 'content'=>null, 'status'=>self::STATUS_DRAFT,
        'news_date'=>null, 'start_time'=>null, 'end_time'=>null, 'create_date'=>null, 'modified_date'=>null,
        'author_id'=>null, 'author_name'=>null, 'extra'=>null, 'url_slug'=>null, 'searchable'=>TRUE
        ];

    private $_fields;

    protected function __construct() {
    }

    public function __get( $key )
    {
        switch( $key ) {
            case 'id':
            case 'category_id':
            case 'news_date':
            case 'start_time':
            case 'end_time':
            case 'create_date':
            case 'modified_date':
            case 'author_id':
                if( !empty($this->_data[$key]) ) return (int) $this->_data[$key];
                return;

            case 'title':
            case 'summary':
            case 'content':
            case 'author_name':
            case 'url_slug':
                if( !empty($this->_data[$key]) ) return trim( $this->_data[$key] );
                return;

            case 'status':
                if( empty($this->_data[$key]) ) return self::STATUS_DRAFT;
                return $this->_data[$key];

            case 'searchable':
                return (bool) $this->_data[$key];

            case 'fields':
                return $this->_fields;

            default:
                throw new \LogicException("$key is not a gettable property of ".get_class($this));
        }
    }

    public function __set( $key, $val )
    {
        switch( $key ) {
            case 'title':
            case 'summary':
            case 'content':
                $this->_data[$key] = trim($val);
                break;

            case 'category_id':
            case 'news_date':
            case 'start_time':
            case 'end_time':
                if( is_null($val) || $val < 1 ) {
                    $this->_data[$key] = null;
                } else {
                    $this->_data[$key] = (int) $val;
                }
                break;

            case 'status':
                switch( $val ) {
                    case self::STATUS_PUBLISHED:
                    case self::STATUS_DRAFT:
                    case self::STATUS_NEEDSAPPROVAL:
                    case self::STATUS_DISABLED:
                        $this->_data[$key] = $val;
                        break;
                    default:
                        throw new \LogicException("Invalid value for status");
                }
                break;

            case 'author_id':
                $this->_data[$key] = (int) $val;
                break;

            case 'url_slug':
                $this->_data[$key] = trim($val);
                break;

            case 'searchable':
                $this->_data[$key] = cms_to_bool( $val );
                break;

            default:
                throw new \LogicException("$key is not a settable property of ".get_class($this));
        }
    }

    public function isValidStatus( string $in )
    {
        $arr = [ self::STATUS_PUBLISHED, self::STATUS_DRAFT, self::STATUS_NEEDSAPPROVAL, self::STATUS_DISABLED ];
        return in_array( $in, $arr );
    }

    public function setFieldVal( string $key, $val )
    {
        $this->_fields[$key] = $val;
    }

    public function fieldVal( string $name )
    {
        if( !empty($this->_fields) && isset($this->_fields[$name]) ) return $this->_fields[$name];
    }

    public static function from_row( array $in ) : Article
    {
        $obj = new self;
        foreach( $in as $key => $val ) {
            switch( $key ) {
                case 'category_id':
                case 'author_id':
                case 'id':
                    $obj->_data[$key] = (int) $val;
                    break;

                case 'title':
                case 'summary':
                case 'content':
                case 'url_slug':
                case 'extra':
                    $obj->_data[$key] = trim($val);
                    break;

                case 'news_date':
                    $obj->_data[$key] = (int) $val;
                    break;

                case 'start_time':
                case 'end_time':
                    if( (int) $val < 1000 ) $val = null;
                    $obj->_data[$key] = $val;
                    break;

                case 'status':
                    if( $obj->isValidStatus( $val ) ) $obj->_data[$key] = $val;
                    break;

                case 'create_date':
                case 'modified_date':
                    $obj->_data[$key] = ((int) $val < 1000) ? null : (int) $val;
                    break;

                case 'searchable':
                    $obj->_data[$key] = cms_to_bool( $val );
                    break;

                case 'fields':
                    if( is_array($val) && count($val) ) {
                        foreach( $val as $fkey => $fval ) {
                            $obj->setFieldVal( $fkey, $fval );
                        }
                    }
                    break;
            }
        }
        return $obj;
    }
} // class
