<?php
namespace FileManager;
use FileManager;
use filemanager_utils;
use cms_config;
use cms_utils;
if (!function_exists("cmsms")) exit;
if (!$this->AccessAllowed()) exit;

class FileManagerUploadHandler extends jquery_upload_handler
{
    private $mod;

    public function __construct(FileManager $mod, $options=null)
    {
	$this->mod = $mod;
        if( !is_array($options) ) $options = [];

        // remove image handling, we're gonna handle this another way
        $options['orient_image'] = false;  // turn off auto image rotation
        $options['image_versions'] = array();

        $options['upload_dir'] = filemanager_utils::get_full_cwd().'/';
        $options['upload_url'] = filemanager_utils::get_cwd_url().'/';

        // set everything up.
        parent::__construct($options);
    }

    protected function is_file_acceptable( $file )
    {
        $config = $this->mod->config;
        if( !$config['developer_mode'] ) {
            $ext = strtolower(substr(strrchr($file, '.'), 1));
            if( startswith($ext,'php') || endswith($ext,'php') ) return FALSE;
        }
        return TRUE;
    }

    protected function after_uploaded_file($fileobject)
    {
        // here we may do image handling, and other cruft.
        debug_to_log(__METHOD__.' 1');
        if( is_object($fileobject) && $fileobject->name != '' ) {
            debug_to_log(__METHOD__.' 2');

            $mod = cms_utils::get_module('FileManager');
            $parms = array();
            $parms['file'] = filemanager_utils::join_path(filemanager_utils::get_full_cwd(),$fileobject->name);

            if( $mod->GetPreference('create_thumbnails') ) {
                debug_to_log('create thumbnails');
                $thumb = filemanager_utils::create_thumbnail($parms['file']);
                if( $thumb ) $params['thumb'] = $thumb;
            }

            $str = $fileobject->name.' uploaded to '.filemanager_utils::get_full_cwd();
            if( isset($params['thumb']) ) $str .= ' and a thumbnail was generated';
            audit('',$mod->GetName(),$str);

            $this->mod->cms->get_hook_manager()->emit( 'FileManager::OnFileUploaded', $parms );
        }
    }
}

$options = array('param_name'=>$id.'files');
$upload_handler = new FileManagerUploadHandler($this, $options);

header('Pragma: no-cache');
header('Cache-Control: private, no-cache');
header('Content-Disposition: inline; filename="files.json"');
header('X-Content-Type-Options: nosniff');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: OPTIONS, HEAD, GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: X-File-Name, X-File-Type, X-File-Size');

switch ($_SERVER['REQUEST_METHOD']) {
    case 'OPTIONS':
        break;
    case 'HEAD':
    case 'GET':
        $upload_handler->get();
        break;
    case 'POST':
        $upload_handler->post();
        break;
    case 'DELETE':
        $upload_handler->delete();
        break;
    default:
        header('HTTP/1.1 405 Method Not Allowed');
}

exit;

#
# EOF
#
