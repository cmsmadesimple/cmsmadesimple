<?php
#--------------------------------------------------
# See DOCS/LICENSE for full license information.
#--------------------------------------------------
if (!defined('CMS_VERSION')) exit;

$caninstall = true;
if( FALSE == can_admin_upload() ) {
    echo $this->ShowErrors($this->Lang('error_permissions'));
    $caninstall = false;
}

// see if there are saved results
$search_data = null;
$term = '';
$advanced = 0;
if( isset($_SESSION['modmgr_search']) ) $search_data = json_decode($_SESSION['modmgr_search'], true);
if( isset($_SESSION['modmgr_searchterm']) ) $term = $_SESSION['modmgr_searchterm'];
if( isset($_SESSION['modmgr_searchadv']) ) $advanced = $_SESSION['modmgr_searchadv'];

$clear_search = function() use (&$search_data) {
    unset($_SESSION['modmgr_search']);
    $search_data = null;
};

// get the modules that are already installed
$instmodules = '';
{
    $result = modmgr_utils::get_installed_modules();
    if( ! $result[0] ) {
        $this->_DisplayErrorPage( $id, $params, $returnid, $result[1] );
        return;
    }
    $instmodules = $result[1];
}

if( isset($params['submit']) ) {
    try {
        $url = $this->GetPreference('module_repository');
        $error = 0;
        $term = cleanvalue(trim($params['term']));
        if( strlen($term) < 3 ) throw new \Exception($this->Lang('error_searchterm'));
        $advanced = (int)$params['advanced'];

        $res = modmgr_rep_client::search($term,$advanced);
        if( !is_array($res) || $res[0] == FALSE ) throw new \Exception($this->Lang('error_search').' '.$res[1]);
        if( !is_array($res[1]) ) throw new \Exception($this->Lang('search_noresults'));

        $res = $res[1];
        $data = array();
        if( count($res) ) $res = modmgr_utils::build_module_data($res, $instmodules);

        $config = cmsms()->GetConfig();
        $moduledir = $config['root_path'].DIRECTORY_SEPARATOR.'modules';
        $writable = is_writable($moduledir);

        $search_data = array();
        for( $i = 0; $i < count($res); $i++ ) {
            $row =& $res[$i];
            $obj = new stdClass();
            foreach( $row as $k => $v ) {
                $obj->$k = $v;
            }
            $obj->name = $this->CreateLink( $id, 'modulelist', $returnid, $row['name'],array('name'=>$row['name']));
            $obj->rawname = $row['name'];
            $obj->version = $row['version'];
            $obj->help_url = $this->create_url( $id, 'modulehelp', $returnid,
                                                array('name'=>$row['name'],'version'=>$row['version'],'filename'=>$row['filename']) );
            $obj->helplink = $this->CreateLink( $id, 'modulehelp', $returnid, $this->Lang('helptxt'),
                                                array('name'=>$row['name'],'version'=>$row['version'],'filename'=>$row['filename']) );
            $obj->depends_url = $this->create_url( $id, 'moduledepends', $returnid,
                                                   array('name' => $row['name'],'version' => $row['version'],'filename' => $row['filename']));
            $obj->dependslink = $this->CreateLink( $id, 'moduledepends', $returnid,
                                                   $this->Lang('dependstxt'),
                                                   array('name' => $row['name'],'version' => $row['version'],'filename' => $row['filename']));
            $obj->about_url = $this->create_url( $id, 'moduleabout', $returnid,
                                                 array('name' => $row['name'],'version' => $row['version'],'filename' => $row['filename']));

            $obj->aboutlink = $this->CreateLink( $id, 'moduleabout', $returnid,
                                                 $this->Lang('abouttxt'),
                                                 array('name' => $row['name'],'version' => $row['version'],'filename' => $row['filename']));
            $obj->age = modmgr_utils::get_status($row['date']);
            $obj->date = $row['date'];
            $obj->downloads = isset($row['downloads'])?$row['downloads']:$this->Lang('unknown');
            $obj->candownload = FALSE;

            switch( $row['status'] ) {
            case 'incompatible':
                $obj->status = $this->Lang('incompatible');
                break;
            case 'uptodate':
                $obj->status = $this->Lang('uptodate');
                break;
            case 'newerversion':
                $obj->status = $this->Lang('newerversion');
                break;
            case 'notinstalled':
                $mod = $moduledir.DIRECTORY_SEPARATOR.$row['name'];
                if( (($writable && is_dir($mod) && is_directory_writable( $mod )) ||
                     ($writable && !file_exists( $mod ) )) && $caninstall ) {
                    $obj->candownload = TRUE;
                    $obj->status = $this->CreateLink( $id, 'installmodule', $returnid,
                                                      $this->Lang('download'),
                                                      array('name' => $row['name'],'version' => $row['version'],'filename' => $row['filename'],
                                                            'size' => $row['size']));
                }
                else {
                    $obj->status = $this->Lang('cantdownload');
                }
                break;

            case 'upgrade':
                $mod = $moduledir.DIRECTORY_SEPARATOR.$row['name'];
                if( (($writable && is_dir($mod) && is_directory_writable( $mod )) ||
                     ($writable && !file_exists( $mod ) )) && $caninstall ) {
                    $obj->candownload = TRUE;
                    $obj->status = $this->CreateLink( $id, 'installmodule', $returnid,
                                                      $this->Lang('upgrade'),
                                                      array('name' => $row['name'],'version' => $row['version'],'filename' => $row['filename'],
                                                            'size' => $row['size']));
                }
                else {
                    $obj->status = $this->Lang('cantdownload');
                }
                break;
            } // case

            $obj->size = (int)((float) $row['size'] / 1024.0 + 0.5);
            if( isset( $row['description'] ) )  $obj->description=$row['description'];
            $search_data[] = $obj;
        }
        $_SESSION['modmgr_search'] = json_encode($search_data);
        $_SESSION['mogmgr_searchterm'] = $term;
        $_SESSION['modmgr_searchadv'] = $params['advanced'];
    }
    catch( \Exception $e ) {
        $clear_search();
        echo $this->ShowErrors($e->GetMessage());
    }
}

$tpl = $smarty->CreateTemplate($this->GetTemplateResource('admin_search_tab.tpl'), null, null, $smarty);
if( is_array($search_data) ) $tpl->assign('search_data',$search_data);
$tpl->assign('term',$term);
$tpl->assign('advanced',$advanced);
$tpl->assign('formstart',$this->CreateFormStart($id,'defaultadmin','','post','',false,'',array('__activetab'=>'search')));
$tpl->assign('formend',$this->CreateFormEnd());
$tpl->assign('actionid',$id);
$tpl->assign('mod',$this);

$tpl->display();
#
# EOF
#
?>