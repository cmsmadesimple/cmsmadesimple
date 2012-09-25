<?php
if (!isset($gCms)) exit;
if (!$this->CheckPermission('Manage Menu')) exit;

#$this->DeleteTemplate($params['tplname']);
#$this->Redirect($id, 'defaultadmin', $returnid);

$error = '';
$newtemplate = '';
if (isset($params['newtemplate'])) $newtemplate = $params['newtemplate'];

if (isset($params['cancel']))
{
	$this->Redirect($id, 'defaultadmin', $returnid);
}
else if (isset($params['submit']))
{
	if ($newtemplate == '')
	{
		$error = $this->Lang('notemplatename');
	}
	else if( endswith($newtemplate,'.tpl') )
	  {
	    $error = $this->Lang('error_templatename');
	  }
	else
	{
		if ($this->GetTemplate($newtemplate) == '')
		{
			$contents = file_get_contents(dirname(__FILE__) . '/templates/' . $params['tplname']);
			$this->SetTemplate($newtemplate, $contents);
			$this->Redirect($id, 'defaultadmin', $returnid);
		}
		else
		{
			$error = $this->Lang('templatenameexists');
		}
	}
}

if( !empty($error) )
  {
    echo $this->ShowErrors($error);
  }

$this->smarty->assign('startform', $this->CreateFormStart($id, 'importtemplate', $returnid));
$this->smarty->assign('endform', $this->CreateFormEnd());
$this->smarty->assign('newtemplate', $this->Lang('newtemplate'));
$this->smarty->assign('inputname', $this->CreateInputText($id, 'newtemplate', $newtemplate, 20, 255));
$this->smarty->assign('hidden', $this->CreateInputHidden($id, 'tplname', $params['tplname']));
$this->smarty->assign('submit', $this->CreateInputSubmit($id, 'submit', lang('submit')));
$this->smarty->assign('cancel', $this->CreateInputSubmit($id, 'cancel', lang('cancel')));

echo $this->ProcessTemplate('importtemplate.tpl');

?>
