<?php

/**
 * A class that defines a base profile of information needed to display a filepicker
 * @package CMS
 * @license GPL
 *
 */

namespace CMSMS;

/**
 * A simple class that defines a profile of information used by the filepicker to indicate how it should
 * behave and what functionality should be provided.
 *
 * This is an immutable class.
 *
 * The constructor and overrideWith methods of this class accept an associative array of parameters (see the properties below)
 * to allow building or altering a profile object.  Ths is the only time when properties of a profile can be adjusted.
 *
 * ```php
 * $obj = new \CMSMS\FilePickerProfile( [ 'type'=>FileType::TYPE_IMAGE,
 *    'exclude_prefix'=>'foo' ] );
 *
 * @package CMS
 * @license GPL
 * @author Robert Campbell <calguy1000@cmsmadesimple.org>
 * @since  2.2
 * @property-read string $top The top directory for the filepicker (relative to the CMSMS uploads directory)
 * @property-read FileType $type The CMSMS FileType representing what files can be selected.
 * @property-read string $match_prefix List only files/items that have the specified prefix.
 * @property-read string exclude_prefix  Exclude any files/items that have the specified prefix.
 * @property-read bool $can_mkdir  Users of the filepicker can create new directories.
 * @property-read bool $can_upload  Users of the filepicker can upload new files (of the specified type)
 * @property-read bool $can_delete  Users of the filepicker can remove files.
 * @property-read bool $show_thumbs Whether thumbnail images should be shown in place of normal icons for images.
 * @property-read bool $show_hidden Indicates that hidden files should be shown in the filepicker.
 * @property-read bool $sort Indicates whether files should be sorted before listing them in the filepicker.
 */
class FilePickerProfile
{
    const FLAG_NONE = 0;
    const FLAG_YES = 1;
    const FLAG_BYGROUP = 2;

    /**
     * @ignore
     */
    private $_data = [ 'top'=>null, 'type'=>FileType::TYPE_ANY, 'can_upload'=>self::FLAG_YES, 'show_thumbs'=>1, 'can_delete'=>self::FLAG_YES,
                       'match_prefix'=>null, 'show_hidden'=>FALSE, 'exclude_prefix'=>null, 'sort'=>TRUE, 'can_mkdir'=>TRUE ];

    /**
     * Set a value into this profile
     *
     * @param string $key The key to set
     * @param mixed $val The value to set.
     */
    protected function setValue( $key, $val )
    {
      $val = (string) $val;
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
            case FileType::TYPE_IMAGE:
            case FileType::TYPE_AUDIO:
            case FileType::TYPE_VIDEO:
            case FileType::TYPE_MEDIA:
            case FileType::TYPE_XML:
            case FileType::TYPE_DOCUMENT:
            case FileType::TYPE_ARCHIVE:
            case FileType::TYPE_ANY:
                $this->_data[$key] = $val;
                break;
            case 'file':
                $this->_data[$key] = FileType::TYPE_ANY;
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

    /**
     * Constructor
     *
     * @param array $params An associative array of params suitable for hte setValue method.
     */
    public function __construct( array $params = null )
    {
        if( !is_array($params) || !count($params) ) return;
        foreach( $params as $key => $val ) {
            $this->setValue($key,$val);
        }
    }

    /**
     * @ignore
     */
    public function __get($key)
    {
        switch( $key ) {
        case 'top':
        case 'type':
        case 'match_prefix':
        case 'exclude_prefix':
            return \trim((string)$this->_data[$key]);

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

    /**
     * Create a new profile object based on the current one, with various aadjusments.
     *
     * @param array $params Associative array of paramaters for the setValue method.
     * @return FilePickerProfile
     */
    public function overrideWith( array $params )
    {
        $obj = clone $this;
        foreach( $params as $key => $val ) {
            $obj->setValue( $key, $val );
        }
        return $obj;
    }

    /**
     * Get the raw data of the profile.
     *
     * @internal
     * @return array
     */
    public function getRawData()
    {
        return $this->_data;
    }
} // end of class