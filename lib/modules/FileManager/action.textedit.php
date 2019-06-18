<?php
namespace FileManager;
use filemanager_utils;

if( !isset($gCms) ) exit;
if( !$this->AdvancedAccessAllowed() ) exit;

try {
    if( isset($_POST['cancel']) ) {
        $this->Redirect($id,"defaultadmin",$returnid,$params);
    }

    $encoded = get_parameter_value($params,'encoded');
    if( !$encoded ) {
        if( !isset($params['selall']) || !is_array($params['selall']) || empty($params['selall']) ) {
            throw new \RuntimeException($this->Lang('nofileselected'));
        }
        if( count($params['selall']) !== 1 ) throw new \RuntimeExeption($this->Lang('morethanonefiledirselected'));
        $encoded = $params['selall'][0];
    }

    $filename = $this->decodefilename($encoded);
    $src = filemanager_utils::join_path($this->config['root_path'],filemanager_utils::get_cwd(),$filename);
    if( !is_file($src) ) throw new \RuntimeException($this->Lang('filenotfound'));
    if( !is_writable($src) ) throw new \RuntimeException($this->Lang('insufficientpermission',$filename));

    // do some analysis, to see if this thing is editable.
    $mimetype = filemanager_utils::mime_content_type($src);
    $ext = strtolower(substr($filename, strrpos($filename, '.')+1));
    if( !startswith($mimetype, 'text/') ) throw new \RuntimeException($this->Lang('filenottexttype'));
    if( strpos($mimetype, 'php') !== FALSE ) throw new \RuntimeException($this->Lang('filetypenoteditable'));
    if( strpos($mimetype, 'script') !== FALSE ) throw new \RuntimeException($this->Lang('filetypenoteditable'));
    if( startswith($ext,'php') ) throw new \RuntimeException($this->Lang('filetypenoteditable'));

    $size = filesize($src);
    $mtime = filemtime($src);
    if( $size > 5 * 1024 * 1024 ) throw new \RuntimeException($this->Lang('filetoolarge'));

    // determining the file type for the editor (can be improved)
    $filetype = 'text';
    switch( $ext ) {
    case 'html':
        $filetype = 'html';
    case 'md':
        $filetype = "markdown";
        break;
    case 'js':
        $filetype = 'javascript';
        break;
    case 'css':
        $filetype = 'css';
        break;
    case 'tpl':
        $filetype = 'smarty';
        break;
    case 'json':
        $filetype = 'json';
        break;
    }
    $content = file_get_contents($src);

    if( isset($_POST['submit']) ) {
        // todo: shold lock the file
        $content = get_parameter_value($_POST,'content');
        file_put_contents($src,$content);
        $this->Redirect($id,"defaultadmin",$returnid,$params);
    }

    $tpl = $smarty->CreateTemplate( $this->GetTemplateResource('admin_textedit.tpl'));
    $tpl->assign('cwd', filemanager_utils::get_cwd());
    $tpl->assign('filename', $filename);
    $tpl->assign('content', $content);
    $tpl->assign('encoded_file', $encoded);
    $tpl->assign('filetype', $filetype);
    $tpl->display();
}
catch( \Exception $e ) {
    $this->SetError($e->GetMessage());
    $this->Redirect($id,"defaultadmin",$returnid,$params);
}