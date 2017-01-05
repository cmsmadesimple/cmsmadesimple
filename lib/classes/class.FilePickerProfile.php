<?php
namespace CMSMS;
class FilePickerProfile
{
    const TYPE_IMAGE = 'image';
    const TYPE_AUDIO = 'audio';
    const TYPE_VIDEO = 'video';
    const TYPE_XML   = 'xml';
    const TYPE_DOCUMENT = 'document';
    const TYPE_ARCHIVE = 'archive';
    const TYPE_ANY = 'any';
    const FLAG_NONE = 0;
    const FLAG_YES = 1;
    const FLAG_BYGROUP = 2;

    private $_data = [ 'top'=>null, 'type'=>self::TYPE_ANY, 'can_upload'=>self::FLAG_YES, 'show_thumbs'=>1, 'can_delete'=>self::FLAG_YES,
                       'match_prefix'=>null, 'show_hidden'=>FALSE, 'exclude_prefix'=>null, 'sort'=>TRUE, 'can_mkdir'=>TRUE ];

    protected function setValue( $key, $val )
    {
        switch( $key ) {
        case 'top':
            $val = trim($val);
            $this->_data[$key] = $val;
            break;

        case 'match_prefix':
        case 'exclude_prefix':
            $this->_data[$key] = trim($val);
            break;

        case 'type':
            $val = trim($val);
            switch( $val ) {
            case self::TYPE_IMAGE:
            case self::TYPE_AUDIO:
            case self::TYPE_VIDEO:
            case self::TYPE_XML:
            case self::TYPE_DOCUMENT:
            case self::TYPE_ARCHIVE:
            case self::TYPE_ANY:
                $this->_data[$key] = $val;
                break;
            default:
                throw new \CmsInvalidDataException("$val is an invalid value for type in ".__CLASS__);
                break;
            }
            break;

        case 'can_mkdir':
        case 'can_delete':
        case 'can_upload':
            $val = (int) $val;
            switch( $val ) {
            case self::FLAG_NONE:
            case self::FLAG_YES:
            case self::FLAG_BYGROUP:
                $this->_data[$key] = $val;
                break;
            default:
                die('val is '.$val);
                throw new \CmsInvalidDataException("$val is an invalid value for $key in ".__CLASS__);
            }
            break;

        case 'show_thumbs':
        case 'show_hidden':
        case 'sort':
            $this->_data[$key] = (bool) $val;
            break;
        }
    }

    public function __construct( array $params = null )
    {
        if( !is_array($params) || !count($params) ) return;
        foreach( $params as $key => $val ) {
            $this->setValue($key,$val);
        }
    }

    public function __get($key)
    {
        switch( $key ) {
        case 'top':
        case 'type':
        case 'match_prefix':
        case 'exclude_prefix':
            return trim($this->_data[$key]);

        case 'can_mkdir':
        case 'can_upload':
        case 'can_delete':
            return (int) $this->_data[$key];

        case 'show_thumbs':
        case 'show_hidden':
        case 'sort':
            return (bool) $this->_data[$key];
        }
    }

    public function overrideWith( array $params )
    {
        $obj = clone $this;
        foreach( $params as $key => $val ) {
            $obj->setValue( $key, $val );
        }
        return $obj;
    }

    public function getRawData()
    {
        return $this->_data;
    }
} // end of class