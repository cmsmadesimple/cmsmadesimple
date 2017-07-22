<?php
if (!isset($gCms)) exit;
if (!$this->CheckPermission('Modify Site Preferences')) return;

if (isset($params['cancel'])) $this->RedirectToAdminTab('customfields','','admin_settings');

$name = '';
if (isset($params['name'])) $name = trim($params['name']);

$type = '';
if (isset($params['type'])) $type = $params['type'];

$max_length = 255;
if (isset($params['max_length'])) $max_length = max(0,(int)$params['max_length']);

$public = 1;
if( isset($params['public']) ) $public = (int)$params['public'];


$arr_options = array();
$options = '';
if( isset($params['options']) ) {
    $options = trim($params['options']);
    $arr_options = news_admin_ops::optionstext_to_array($options);
}

$userid = get_userid();

if (isset($params['submit'])) {
    $error = false;
    if ($name == '') $error = $this->Lang('nonamegiven');

    if( !$error && $type == 'dropdown' && count($arr_options) == 0 ) $error = $this->Lang('error_nooptions');

    if( !$error ) {
        $query = 'SELECT id FROM '.CMS_DB_PREFIX.'module_news_fielddefs WHERE name = ?';
        $exists = $db->GetOne($query,array($name));
        if( $exists ) $error = $this->Lang('nameexists');
    }

    if( !$error ) {
        $max = $db->GetOne('SELECT max(item_order) + 1 FROM ' . CMS_DB_PREFIX . 'module_news_fielddefs');
        if( $max == null ) $max = 1;

        $extra = array('options'=>$arr_options);
        $query = 'INSERT INTO '.CMS_DB_PREFIX.'module_news_fielddefs (name, type, max_length, item_order, create_date, modified_date, public, extra) VALUES (?,?,?,?,?,?,?,?)';
        $parms = array($name, $type, $max_length, $max,
                       trim($db->DBTimeStamp(time()), "'"),
                       trim($db->DBTimeStamp(time()), "'"),
                       $public, serialize($extra));
        $db->Execute($query, $parms );

        // put mention into the admin log
        audit('', 'News custom: '.$name, 'Field definition added');

        // done.
        $params = array('tab_message'=> 'fielddefadded', 'active_tab' => 'customfields');
        $this->SetMessage($this->Lang('fielddefadded'));
        $this->RedirectToAdminTab('customfields','','admin_settings');
    }

    if( $error ) echo $this->ShowErrors($error);
}

#Display template
$smarty->assign('title',$this->Lang('addfielddef'));
$smarty->assign('startform', $this->CreateFormStart($id, 'admin_addfielddef', $returnid));
$smarty->assign('endform', $this->CreateFormEnd());
$smarty->assign('nametext', $this->Lang('name'));
$smarty->assign('typetext', $this->Lang('type'));
$smarty->assign('maxlengthtext', $this->Lang('maxlength'));
$smarty->assign('showinputtype', true);
$smarty->assign('info_maxlength', $this->Lang('info_maxlength'));
$smarty->assign('userviewtext',$this->Lang('public'));

$smarty->assign('name',$name);
$smarty->assign('fieldtypes',$this->GetFieldTypes());
$smarty->assign('type',$type);
$smarty->assign('max_length',$max_length);
$smarty->assign('public',$public);
$smarty->assign('options',$options);

$smarty->assign('mod',$this);
echo $this->ProcessTemplate('editfielddef.tpl');

// EOF
