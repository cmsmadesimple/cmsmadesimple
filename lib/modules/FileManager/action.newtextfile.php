<?php
namespace FileManager;
use filemanager_utils;

if( !isset($gCms) ) exit;
if( !$this->AdvancedAccessAllowed() ) exit;

try {
    if( isset($_POST['cancel']) ) {
        die('cancel');
        $this->Redirect($id,"defaultadmin",$returnid,$params);
    }

    $filename = $content = null;
    if( isset($_POST['submit']) ) {
        try {
            $filename = cleanValue($_POST['filename']);
            $content  = $_POST['content'];

            // validate the filename
            if( !preg_match('/^[a-z0-9][a-z0-9\ \-_]*\.[a-z0-9]{1,5}$/ui',$filename) ) throw new \RuntimeException($this->Lang('invalidfilename'));
            $ext = strtolower(substr($filename, strrpos($filename, '.')+1));
            if( startswith($ext,'php') ) throw new \RuntimeException($this->Lang('filetypenoteditable'));
            if( startswith($content,'<?') !== FALSE ) throw new \RuntimeException($this->Lang('filetypenoteditable'));

            // make sure we can write, and that the file does not exist)
            $dir = filemanager_utils::join_path(CMS_ROOT_PATH, filemanager_utils::get_cwd());
            if( !is_dir($dir) || !is_writable($dir) ) throw new \RuntimeException('insufficientpermission',$dir);
            $destfile = filemanager_utils::join_path($dir,$filename);
            if( is_file($destfile) ) throw new \RuntimeException('fileexistsdst', $filename);
            file_put_contents($destfile, $content);
            $this->Redirect($id,"defaultadmin",$returnid,$params);
        }
        catch( \Exception $e ) {
            echo $this->ShowErrors($e->GetMessage());
        }
    }

    $tpl = $smarty->CreateTemplate( $this->GetTemplateResource('admin_newtextfile.tpl'));
    $tpl->assign('cwd', filemanager_utils::get_cwd());
    $tpl->assign('filename',$filename);
    $tpl->assign('content',$content);
    $tpl->display();
}
catch( \Exception $e ) {
    $this->SetError($e->GetMessage());
    $this->Redirect($id,"defaultadmin",$returnid,$params);
}
