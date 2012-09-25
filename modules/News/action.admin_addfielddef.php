<?php
if (!isset($gCms)) exit;
if (!$this->CheckPermission('Modify Site Preferences')) return;

if (isset($params['cancel']))
  {
    $params = array('active_tab' => 'customfields');
    $this->Redirect($id, 'defaultadmin', $returnid, $params);
  }

$name = '';
if (isset($params['name']))
  {
    $name = $params['name'];
  }

$type = '';
if (isset($params['type']))
  {
    $type = $params['type'];
  }

$max_length = 255;
if (isset($params['max_length']))
  {
    $max_length = (int)$params['max_length'];
  }

$public = 0;
if( isset($params['public']) )
  {
    $public = (int)$params['public'];
  }

$userid = get_userid();

if (isset($params['submit']))
  {
    if ($name != '')
      {
	if (is_numeric($max_length))
	  {
	    $query = 'SELECT id FROM '.cms_db_prefix().'module_news_fielddefs
                       WHERE name = ?';
	    $exists = $db->GetOne($query,array($name));
	    if( $exists ) 
	      {
		echo $this->ShowErrors($this->Lang('nameexists'));
	      }
	    else
	      {
		$max = $db->GetOne('SELECT max(item_order) + 1 FROM ' . cms_db_prefix() . 'module_news_fielddefs');
		if( $max == null ) $max = 1;
		$query = 'INSERT INTO '.cms_db_prefix().'module_news_fielddefs (name, type, max_length, item_order, create_date, modified_date, public) VALUES (?,?,?,?,?,?,?)';
		$parms = array($name, $type, $max_length, 
			       $max, 
			       trim($db->DBTimeStamp(time()), "'"), 
			       trim($db->DBTimeStamp(time()), "'"),
			       $public);
		$db->Execute($query, $parms );
		
		$params = array('tab_message'=> 'fielddefadded', 'active_tab' => 'customfields');
		// put mention into the admin log
		audit($name, 'News custom: '.$name, 'Field definition added');
		$this->Redirect($id, 'defaultadmin', $returnid, $params);

	      }
	  }
	else
	  {
	    echo $this->ShowErrors($this->Lang('notanumber'));
	  }
      }
    else
      {
	echo $this->ShowErrors($this->Lang('nonamegiven'));
      }
  }

#Display template
$smarty->assign('startform', $this->CreateFormStart($id, 'admin_addfielddef', $returnid));
$smarty->assign('endform', $this->CreateFormEnd());
$smarty->assign('title',$this->Lang('addfielddef'));
$smarty->assign('nametext', $this->Lang('name'));
$smarty->assign('typetext', $this->Lang('type'));
$smarty->assign('maxlengthtext', $this->Lang('maxlength'));
$smarty->assign('inputname', $this->CreateInputText($id, 'name', $name, 30, 255));
$smarty->assign('showinputtype', true);
$smarty->assign('inputtype', $this->GetTypesDropdown($id, 'type', $type));
$smarty->assign('inputmaxlength', $this->CreateInputText($id, 'max_length', $max_length, 30, 255));
$smarty->assign('info_maxlength', $this->Lang('info_maxlength'));
$smarty->assign('userviewtext',$this->Lang('public'));
$smarty->assign('input_userview',
		$this->CreateInputcheckbox($id, 'public', 1, $public));

$smarty->assign('hidden', '');
$smarty->assign('submit', $this->CreateInputSubmit($id, 'submit', lang('submit')));
$smarty->assign('cancel', $this->CreateInputSubmit($id, 'cancel', lang('cancel')));

echo $this->ProcessTemplate('editfielddef.tpl');

// EOF
?>