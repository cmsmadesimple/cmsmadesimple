<?php
namespace CMSMS;
class FileTypeHelper
{
    private $_mime_ok;
    private $_use_mimetype;
    private $_config;
    private $_image_extensions = ['jpg','jpeg','bmp','wbmp','gif','png','webp'];
    private $_archive_extensions = ['.zip', '.tar.gz', '.tar.bz2', '.7z', '.rar', '.s7z', '.gz', '.z' ];
    private $_audio_extensions = ['mp3','wav','flac','ra','ram','wm','ogg'];
    private $_video_extensions = ['swf','mov','mpg','mp4','mpeg','wmv','rm','avi'];
    private $_xml_extensions = ['xml','rss'];
    private $_document_extensions = ['doc','docx','odt','ods','odp','odg','odf','txt','pdf','text','xls','xlsx','ppt','pptx'];

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

    public function is_readable( $filename )
    {
        $dn = dirname($filename);
        if( $dn && is_dir($dn) && is_file($filename) && is_readable($filename) ) return TRUE;
        return FALSE;
    }

    public function get_extension( $filename )
    {
        return strtolower(substr($filename,strrpos($filename,'.')+1));
    }

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

    public function is_thumb( $filename )
    {
        $bn = basename( $filename );
        return $this->is_image( $filename ) && startswith($bn,'thumb_');
    }

    public function is_archive( $filename )
    {
        // extensions only.
        $ext = $this->get_extension( $filename );
        return in_array( $ext, $this->_archive_extensions );
    }

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

    public function is_media( $filename )
    {
        if( $this->is_image( $filename ) ) return TRUE;
        if( $this->is_audio( $filename ) ) return TRUE;
        if( $this->is_video( $filename ) ) return TRUE;
        return FALSE;
    }

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

    public function is_document( $filename )
    {
        // extensions only
        $ext = strtolower(substr($filename,strrpos($filename,'.')+1));
        return in_array($ext, $this->_document_extensions );
    }

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