<?php

/**
 * This file describes the class that provides numerous utilities for determining and manipulating files by temp.
 *
 * @package CMS
 * @license GPL
 * @author Robert Campbell <calguy1000@cmsmadesimple.org>
 * @since  2.2
 */

namespace CMSMS;

/**
 * A class to provide utilities for manipulating files by their type.
 *
 * @package CMS
 * @license GPL
 * @author Robert Campbell <calguy1000@cmsmadesimple.org>
 * @since  2.2
 */
class FileTypeHelper
{
    /**
     * @ignore
     */
    private $_mime_ok;
    /**
     * @ignore
     */
    private $_use_mimetype;
    /**
     * @ignore
     */
    private $_config;
    /**
     * @ignore
     */
    private $_image_extensions = ['jpg','jpeg','bmp','wbmp','gif','png','webp'];
    /**
     * @ignore
     */
    private $_archive_extensions = ['.zip', '.tar.gz', '.tar.bz2', '.7z', '.rar', '.s7z', '.gz', '.z' ];
    /**
     * @ignore
     */
    private $_audio_extensions = ['mp3','wav','flac','ra','ram','wm','ogg'];
    /**
     * @ignore
     */
    private $_video_extensions = ['swf','mov','mpg','mp4','mpeg','wmv','rm','avi'];
    /**
     * @ignore
     */
    private $_xml_extensions = ['xml','rss'];
    /**
     * @ignore
     */
    private $_document_extensions = ['doc','docx','odt','ods','odp','odg','odf','txt','pdf','text','xls','xlsx','ppt','pptx'];

    /**
     * Constructor
     *
     * @param cms_config $config
     */
    public function __construct( \cms_config $config )
    {
        $this->_use_mimetype = $this->_mime_ok = function_exists('finfo_open') && function_exists('finfo_file');
        $this->_use_mimetype = $this->_use_mimetype && !$config['FileTypeHelper_usemimetype'];

        $this->update_config_extensions('_image_extensions', $config['FileTypeHelper_image_extensions']);
        $this->update_config_extensions('_audio_extensions', $config['FileTypeHelper_audio_extensions']);
        $this->update_config_extensions('_video_extensions', $config['FileTypeHelper_video_extensions']);
        $this->update_config_extensions('_xml_extensions', $config['FileTypeHelper_xml_extensions']);
        $this->update_config_extensions('_document_extensions', $config['FileTypeHelper_document_extensions']);
    }

    /**
     * A utility method to allow overriding the extensions used to identify files of a specific type
     *
     * @param string $member One of (_archive_extensions, _audio_extensions, _video_extensions, _xml_extensions, _document_extensions)
     * @param string $str A comma separated string of extensions for that file type
     */
    protected function update_config_extensions( $member, $str )
    {
        $str = trim($str);
        if( !$str ) return;

        $out = $this->$member;
        $list = explode(',',$str);
        foreach( $list as $one ) {
            $one = strtolower(trim($one));
            if( !$one || in_array($one,$out) ) continue;
            $out[] = $one;
        }
        $this->$member = $out;
    }

    /**
     * Test if the file specified is readable
     *
     * @param string $filename
     * @return bool
     */
    public function is_readable( $filename )
    {
        $dn = dirname($filename);
        if( $dn && is_dir($dn) && is_file($filename) && is_readable($filename) ) return TRUE;
        return FALSE;
    }

    /**
     * Get the extension of a filename
     *
     * @param string $filename
     * @return string
     */
    public function get_extension( $filename )
    {
        return strtolower(substr($filename,strrpos($filename,'.')+1));
    }

    /**
     * Get the mime type of a filename.
     * requires the finfo_open function.
     *
     * @param string $filename
     * @return string
     */
    public function get_mime_type( $filename )
    {
        if( !$this->_mime_ok ) return;
        $fh = finfo_open(FILEINFO_MIME_TYPE);
        if( $fh ) {
            $mime_type = finfo_file($fh,$filename);
            finfo_close($fh);
            return $mime_type;
        }
    }

    /**
     * Test if the file specified is an image.
     * This method will use the mime type if possible, otherwise an extension is used to determine if the file is an image.
     *
     * @param string $filename
     * @return bool
     */
    public function is_image( $filename )
    {
        if( $this->_use_mimetype && $this->is_readable( $filename ) ) {
            $type = $this->get_mime_type( $filename );
            $res = startswith( $type, 'image/');
            if( $res ) return TRUE;
        }

        // fall back to extensions
        $ext = $this->get_extension( $filename );
        return in_array( $ext, $this->_image_extensions );
    }

    /**
     * Test if the file specified is a thumbnail
     * This method first tests if the file is an image, and then if it is also a thumbnail.
     *
     * @param string $filename
     * @return bool
     */
    public function is_thumb( $filename )
    {
        $bn = basename( $filename );
        return $this->is_image( $filename ) && startswith($bn,'thumb_');
    }

    /**
     * Using the file extension, test if the filename provided is a known archive.
     *
     * @param string $filename
     * @return bool
     */
    public function is_archive( $filename )
    {
        // extensions only.
        $ext = $this->get_extension( $filename );
        return in_array( $ext, $this->_archive_extensions );
    }

    /**
     * Using mime types if possible, or extensions, test if the filename provided is a known audio file.
     *
     * @param string $filename
     * @return bool
     */
    public function is_audio( $filename )
    {
        if( $this->_use_mimetype && $this->is_readable( $filename ) ) {
            $type = $this->get_mime_type( $filename );
            $res = startswith( $type, 'audio/');
            if( $res ) return TRUE;
        }

        $ext = $this->get_extension( $filename );
        return in_array($ext, $this->_audio_extensions );
    }

    /**
     * Using mime types if possible, or extensions, test if the filename provided is a known audio file.
     *
     * @param string $filename
     * @return bool
     */
    public function is_video( $filename )
    {
        if( $this->_use_mimetype && $this->is_readable( $filename ) ) {
            $type = $this->get_mime_type( $filename );
            $res = startswith( $type, 'video/');
            if( $res ) return TRUE;
        }

        $ext = $this->get_extension( $filename );
        return in_array($ext, $this->_video_extensions );
    }

    /**
     * Test if the file name specified is a known media (image, audio, video) file.
     *
     * @param string $filename
     * @return bool
     */
    public function is_media( $filename )
    {
        if( $this->is_image( $filename ) ) return TRUE;
        if( $this->is_audio( $filename ) ) return TRUE;
        if( $this->is_video( $filename ) ) return TRUE;
        return FALSE;
    }

    /**
     * Test if the file name specified is a known XML file.
     *
     * @param string $filename
     * @return bool
     */
    public function is_xml( $filename )
    {
        if( $this->_use_mimetype && $this->is_readable( $filename ) ) {
            $type = $this->get_mime_type( $filename );
            switch( $type ) {
            case 'text/xml';
            case 'application/xml':
            case 'application/rss+xml':
                return TRUE;
            }
        }
        $ext = strtolower(substr($filename,strrpos($filename,'.')+1));
        return in_array($ext, $this->_video_extensions );
    }

    /**
     * Using the file extension, test if the file name specified is a known document file.
     *
     * @param string $filename
     * @return bool
     */
    public function is_document( $filename )
    {
        // extensions only
        $ext = strtolower(substr($filename,strrpos($filename,'.')+1));
        return in_array($ext, $this->_document_extensions );
    }

    /**
     * Atempt to find a file type for the given filename.
     *
     * @param string $filename
     * @return string A FileType type constant describing the file type, if found.
     */
    public function get_file_type( $filename )
    {
        if( $this->is_image( $filename ) ) return FileType::TYPE_IMAGE;
        if( $this->is_audio( $filename ) ) return FileType::TYPE_AUDIO;
        if( $this->is_video( $filename ) ) return FileType::TYPE_VIDEO;
        if( $this->is_xml( $filename ) ) return FileType::TYPE_XML;
        if( $this->is_document( $filename ) ) return FileType::TYPE_DOCUMENT;
        if( $this->is_archive( $filename ) ) return FileType::TYPE_ARCHIVE;
    }
} // end of class