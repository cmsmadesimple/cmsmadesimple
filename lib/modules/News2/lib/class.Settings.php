<?php
namespace News2;

class Settings
{

    private $_data =
        [
            'editor_summary_enabled'=>true,
            'editor_summary_wysiwyg'=>true,
            'editor_category_required'=>false,
            'editor_urlslug_required'=>false,
            'editor_own_editpublished'=>false,
            'editor_own_setpublished'=>false,
            'detailpage'=>null,
            'expired_searchable'=>false,
            'detail_show_expired'=>false,
            'alert_draft'=>true,
            'alert_needsapproval'=>true,
            'pretty_category_url'=>true,
            'bycategory_withchildren'=>true
        ];

    protected function __construct() {
    }

    public function __get( $key )
    {
        if( isset($this->_data[$key]) ) return $this->_data[$key];
        throw new \InvalidArgumentException("$key is not a gettable property of ".get_class($this));
    }

    public function __set( $key, $val )
    {
        throw new \InvalidArgumentException("$key is not a settable property of ".get_class($this));
    }

    public static function from_row( array $in )
    {
        $obj = new self;
        foreach( $in as $key => $val ) {
            switch( $key ) {
            case 'editor_summary_enabled':
            case 'editor_summary_wysiwyg':
            case 'editor_category_required':
            case 'editor_urlslug_required':
            case 'editor_own_editpublished':
            case 'editor_own_setpublished':
            case 'expired_searchable':
            case 'detail_show_expired':
            case 'alert_draft':
            case 'alert_needsapproval':
            case 'pretty_category_url':
            case 'bycategory_withchildren':
                if( is_bool($val) || !empty($val) ) {
                    $obj->_data[$key] = cms_to_bool( $val );
                }
                break;
            case 'detailpage':
                $obj->_data[$key] = trim($val);
                break;
            }
        }
        return $obj;
    }
} // class
