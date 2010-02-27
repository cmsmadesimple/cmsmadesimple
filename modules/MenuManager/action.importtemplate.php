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
			@ob_start();
			@readfile(dirname(__FILE__) . '/templates/' . $params['tplname']);
			$contents = @ob_get_contents();
			@ob_clean();
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

$smarty->assign('startform', $this->CreateFormStart($id, 'importtemplate', $returnid));
$smarty->assign('endform', $this->CreateFormEnd());
$smarty->assign('newtemplate', $this->Lang('newtemplate'));
$smarty->assign('inputname', $this->CreateInputText($id, 'newtemplate', $newtemplate, 20, 255));
$smarty->assign('hidden', $this->CreateInputHidden($id, 'tplname', $params['tplname']));
$smarty->assign('submit', $this->CreateInputSubmit($id, 'submit', lang('submit')));
$smarty->assign('cancel', $this->CreateInputSubmit($id, 'cancel', lang('cancel')));

echo $this->ProcessTemplate('importtemplate.tpl');

?>
