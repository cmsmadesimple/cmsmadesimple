<?php
if (!function_exists("cmsms")) exit;
if (!$this->CheckPermission("Modify Files") && !$this->AdvancedAccessAllowed()) exit;
if (isset($params["cancel"])) $this->Redirect($id,"defaultadmin",$returnid,$params);

$selall = $params['selall'];
if( !is_array($selall) ) $selall = unserialize($selall);
if (count($selall)==0) {
    $params["fmerror"]="nofilesselected";
    $this->Redirect($id,"defaultadmin",$returnid,$params);
}

foreach( $selall as &$one ) {
    $one = $this->decodefilename($one);
}

$config=cmsms()->GetConfig();
$cwd = filemanager_utils::get_cwd();
$dirlist = filemanager_utils::get_dirlist();
if( !count($dirlist) ) {
    $params["fmerror"]="nodestinationdirs";
    $this->Redirect($id,"defaultadmin",$returnid,$params);
}

$errors = array();
$destloc = '';
if( isset($params['submit']) ) {
    $destdir = trim($params['destdir']);
    if( $destdir == $cwd ) $errors[] = $this->Lang('movedestdirsame');

    $advancedmode = filemanager_utils::check_advanced_mode();
    $basedir = $config['uploads_path'];
    if( $advancedmode ) $basedir = $config['root_path'];

    if( count($errors) == 0 ) {
        $destloc = filemanager_utils::join_path($basedir,$destdir);
        if( !is_dir($destloc) || ! is_writable($destloc) ) $errors[] = $this->Lang('invalidmovedir');
    }

    if( count($errors) == 0 ) {
        foreach( $selall as $file ) {
            $src = filemanager_utils::join_path($config['root_path'],$cwd,$file);
            $dest = filemanager_utils::join_path($basedir,$destdir,$file);

            if( !file_exists($src) ) {
                $errors[] = $this->Lang('filenotfound')." $file";
                continue;
            }
            if( !is_readable($src) ) {
                $errors[] = $this->Lang('insufficientpermission',$file);
                continue;
            }
            if( file_exists($dest) ) {
                $errors[] = $this->Lang('fileexistsdest',$file);
                continue;
            }
            if( is_dir($src) && startswith($dest,$src) ) {
                $errors[] = $this->Lang('filemovesame',$file);
                continue;
            }

            $thumb = '';
            $src_thumb = '';
            $dest_thumb = '';
            if( filemanager_utils::is_image_file($file) ) {
                $tmp = 'thumb_'.$file;
                $src_thumb = filemanager_utils::join_path($config['root_path'],$cwd,$tmp);
                $dest_thumb = filemanager_utils::join_path($basedir,$destdir,$tmp);

                if( file_exists($src_thumb) ) {
                    // have a thumbnail
                    $thumb = $tmp;
                    if( !is_readable($src_thumb) ) {
                        $errors[] = $this->Lang('insufficientpermission',$thumb);
                        continue;
                    }
                    if( file_exists($dest_thumb) ) {
                        $errors[] = $this->Lang('fileexistsdest',$thumb);
                        continue;
                    }
                }
            }

            // here we can move the file/dir
            $res = rename($src,$dest);
            if( !$res ) {
                $errors[] = $this->Lang('movefailed',$file);
                continue;
            }
            if( $thumb ) {
                $res = rename($src_thumb,$dest_thumb);
                if( !$res ) {
                    $errors[] = $this->Lang('movefailed',$thumb);
                    continue;
                }
            }
        } // foreach
    } // no errors

    if( count($errors) == 0 ) {
        $paramsnofiles["fmmessage"]="movesuccess"; //strips the file data
        $this->Redirect($id,"defaultadmin",$returnid,$paramsnofiles);
    }
} // submit

if( count($errors) ) echo $this->ShowErrors($errors);
if( is_array($params['selall']) ) $params['selall'] = serialize($params['selall']);
$smarty->assign('startform', $this->CreateFormStart($id, 'fileaction', $returnid,"post","",false,"",$params));
$smarty->assign('endform', $this->CreateFormEnd());
$smarty->assign('cwd','/'.$cwd);
$smarty->assign('dirlist',$dirlist);
$smarty->assign('selall',$selall);
$smarty->assign('mod',$this);
echo $this->ProcessTemplate('move.tpl');

?>