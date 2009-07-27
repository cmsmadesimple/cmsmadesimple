<?php
if (!isset($gCms)) exit;
if (!$this->CheckPermission('Manage Menu')) exit;

$smarty->assign_by_ref('mod', $this);

$error = '';
$newtemplate = '';
if (isset($params['newtemplate'])) $newtemplate = $params['newtemplate'];
$content = '';
if (isset($params['templatecontent'])) $content = $params['templatecontent'];
$recursive = '1';
if (isset($params['recursive'])) $recursive = $params['recursive'];

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
	else if ($content == '')
	{
		$error = $this->Lang('nocontent');
	}
	else
	{
		if ($this->GetTemplate($newtemplate) == '')
		{
			$this->SetTemplate($newtemplate, $content);
			$this->SetPreference('tpl_' . $newtemplate . '_recursive', $recursive);
			$this->Redirect($id, 'defaultadmin', $returnid);
		}
		else
		{
			$themeObject = $gCms->variables['admintheme'];
			$error = $themeObject->ShowErrors($this->Lang('templatenameexists'));
		}
	}
}

if( !empty($error) )
{
	echo $this->ShowErrors($error);
}

$smarty->assign('startform', $this->CreateFormStart($id, 'addtemplate', $returnid));
$smarty->assign('endform', $this->CreateFormEnd());
$smarty->assign('newtemplate', $this->Lang('newtemplate'));
$smarty->assign('inputname', $this->CreateInputText($id, 'newtemplate', $newtemplate, 20, 255));
$smarty->assign('content', $this->Lang('templatecontent'));
$smarty->assign('inputcontent', $this->CreateSyntaxArea($id, $content, 'templatecontent'));
$smarty->assign('inputrecursive', $this->CreateInputHidden($id, 'recursive', '0') . $this->CreateInputCheckbox($id, 'recursive', '1', ($recursive == '1')));
#$smarty->assign('hidden', $this->CreateInputHidden($id, 'tplname', $params['tplname']));
$smarty->assign('submit', $this->CreateInputSubmit($id, 'submit', lang('submit')));
$smarty->assign('cancel', $this->CreateInputSubmit($id, 'cancel', lang('cancel')));

echo $this->ProcessTemplate('edittemplate.tpl');

?>
