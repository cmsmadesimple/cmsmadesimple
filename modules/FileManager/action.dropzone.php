<?php
if (!function_exists("cmsms")) exit;
if (!$this->CheckPermission('Modify Files')) return;

if (isset($_SERVER['HTTP_USER_AGENT']) && (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false)) {
    $smarty->assign('is_ie',1);
}
$smarty->assign('formstart',$this->CreateFormStart($id,'upload',$returnid,'post','multipart/form-data'));
$smarty->assign('formend',$this->CreateFormEnd());
$post_max_size = filemanager_utils::str_to_bytes(ini_get('post_max_size'));
$upload_max_filesize = filemanager_utils::str_to_bytes(ini_get('upload_max_filesize'));
$smarty->assign('max_chunksize',min($upload_max_filesize,$post_max_size-1024));
$smarty->assign('action_url',$this->create_url('m1_','upload',$returnid));
$smarty->assign('prompt_dropfiles',$this->Lang('prompt_dropfiles'));
$smarty->assign('chdir_url',$this->create_url('m1_','changedir',$returnid));
$advancedmode = $this->GetPreference('advancedmode',0);
if( strlen($advancedmode) > 1 ) $advancedmode = 0;

// get a folder list...
{
    $cwd = filemanager_utils::get_cwd();
    $smarty->assign('cwd',$cwd);

    $startdir = $config['uploads_path'];
    if( $this->AdvancedAccessAllowed() && $advancedmode ) $startdir = $config['root_path'];

    // now get a simple list of all of the directories we have 'write' access to.
    $basedir = dirname($startdir);
    function get_dirs($startdir,$prefix = '/') {
        $res = array();
        if( !is_dir($startdir) ) return;

        global $showhiddenfiles;
        $dh = opendir($startdir);
        while( false !== ($entry = readdir($dh)) ) {
            if( $entry == '.' ) continue;
            if( $entry == '..' ) continue;
            $full = filemanager_utils::join_path($startdir,$entry);
            if( !is_dir($full) ) continue;
            if( !is_readable($full) ) continue;
            if( !$showhiddenfiles && ($entry[0] == '.' || $entry[0] == '_') ) continue;

            if( $entry == '.svn' || $entry == '.git' ) continue;
            if( is_writable($full) ) $res[$prefix.$entry] = $prefix.$entry;
            $tmp = get_dirs($full,$prefix.$entry.'/');
            if( is_array($tmp) && count($tmp) ) $res = array_merge($res,$tmp);
        }
        closedir($dh);
        return $res;
    }

    $output = get_dirs($startdir,'/'.basename($startdir).'/');
    $output['/'.basename($startdir)] = '/'.basename($startdir);
    if( count($output) ) {
        ksort($output);
        $smarty->assign('dirlist',$output);
    }
}

$smarty->assign('FileManager',$this);
$template = 'dropzone.tpl';
if( isset($params['template']) ) {
    $template = trim($params['template']);
    if( !endswith($template,'.tp;') )  $template .= '.tpl';
}
echo $this->ProcessTemplate($template);