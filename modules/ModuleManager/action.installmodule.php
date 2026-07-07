<?php
#--------------------------------------------------
# See DOCS/LICENSE for full license information.
#--------------------------------------------------
if (!defined('CMS_VERSION')) exit;
if( !$this->CheckPermission('Modify Modules') ) return;
$this->SetCurrentTab('modules');

if( isset($params['cancel']) ) {
    $this->SetMessage($this->Lang('msg_cancelled'));
    $this->RedirectToAdminTab();
}

$module_name = get_parameter_value($params,'name');
$module_version  = get_parameter_value($params,'version');
$module_filename  = get_parameter_value($params,'filename');
$module_size = get_parameter_value($params,'size');

// Phase 1: Download and expand XML packages
if( isset($params['submit']) && !empty($params['modlist']) ) {
    try {
        set_time_limit(9999);
        $modlist = json_decode(base64_decode($params['modlist']), TRUE);
        if( !is_array($modlist) || !count($modlist) ) throw new CmsInvalidDataException($this->Lang('error_missingparams'));

        foreach( $modlist as $key => $rec ) {
            if( $rec['action'] != 'i' && $rec['action'] != 'u' ) continue;
            if( !isset($rec['filename']) || !isset($rec['size']) ) throw new CmsInvalidDataException($this->Lang('error_missingparams'));
            modmgr_utils::get_module_xml($rec['filename'], $rec['size']);
        }

        $ops = cmsms()->GetModuleOperations();
        foreach( $modlist as $key => &$rec ) {
            if( $rec['action'] != 'i' && $rec['action'] != 'u' ) continue;
            $xml_filename = modmgr_utils::get_module_xml($rec['filename'], $rec['size'], $rec['md5sum'] ?? '');
            $rec['tmpfile'] = $xml_filename;
            $ops->ExpandXMLPackage($xml_filename, 1);
        }

        $key = 'modmgr_'.md5(__FILE__.time());
        $tmp_file = TMP_CACHE_LOCATION . '/' . $key . '.json';
        file_put_contents($tmp_file, json_encode($modlist));
        $this->Redirect($id, 'installmodule', $returnid, ['doinstall' => $key]);
    }
    catch( Exception $e ) {
        $this->SetError($e->GetMessage() ?: get_class($e));
        $this->RedirectToAdminTab();
    }
}

// Phase 2: Execute queued install/upgrade/activate actions
if( isset($params['doinstall']) ) {
    try {
        modmgr_install_helper::execute_queued_actions($params['doinstall'], $this);
        $this->SetMessage($this->Lang('success'));
    }
    catch( Exception $e ) {
        $this->SetError($e->GetMessage() ?: get_class($e));
    }
    $this->RedirectToAdminTab();
}

// Phase 3: Resolve dependencies and display confirmation
try {
    if( !isset($params['doinstall']) && $module_name == '' || $module_version == '' || $module_filename == '' || $module_size < 100 ) {
        throw new CmsInvalidDataException($this->Lang('error_missingparams'));
    }

    $uselatest = (int) $this->GetPreference('latestdepends', 1);
    $alldeps = modmgr_install_helper::resolve_dependencies($module_name, $module_version, $uselatest);

    // fetch and merge latest info for all dependencies
    $alldeps = modmgr_install_helper::merge_latest_dep_info($alldeps, $uselatest, $this);

    // add our current item
    $alldeps[$module_name] = array('name' => $module_name, 'version' => $module_version, 'filename' => $module_filename, 'size' => $module_size);

    // determine actions and validate
    $alldeps = modmgr_install_helper::assign_actions($alldeps);
    modmgr_install_helper::validate_deps($alldeps, $this);

    if( !count($alldeps) ) {
        $this->SetError($this->Lang('err_nothingtodo'));
        $this->RedirectToAdminTab();
    }

    // display confirmation template
    $tpl = $smarty->CreateTemplate($this->GetTemplateResource('installinfo.tpl'), null, null, $smarty);
    $parms = array('name' => $module_name, 'version' => $module_version, 'filename' => $module_filename, 'size' => $module_size);
    $tpl->assign('return_url', $this->create_url($id, 'defaultadmin', $returnid, array('__activetab' => 'modules')));
    $tpl->assign('form_start', $this->CreateFormStart($id, 'installmodule', $returnid, 'post', '', FALSE, '', $parms).
                    $this->CreateInputHidden($id, 'modlist', base64_encode(json_encode($alldeps))));
    $tpl->assign('formend', $this->CreateFormEnd());
    $tpl->assign('module_name', $module_name);
    $tpl->assign('module_version', $module_version);
    $last_key = array_key_last($alldeps);
    $tpl->assign('is_upgrade', ($alldeps[$last_key]['action'] == 'u') ? 1 : 0);
    $tpl->assign('dependencies', $alldeps);
    $tpl->display();
}
catch( Exception $e ) {
    $this->SetError($e->GetMessage() ?: get_class($e));
    $this->RedirectToAdminTab();
}
#
# EOF
#
