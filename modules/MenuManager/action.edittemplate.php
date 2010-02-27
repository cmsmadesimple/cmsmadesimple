<?php
if (!isset($gCms)) exit;
if (!$this->CheckPermission('Manage Menu')) exit;

$smarty->assign_by_ref('mod', $this);

$error = '';
$tplname = '';
if (isset($params['tplname'])) $tplname = $params['tplname'];
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
else if (isset($params['submit']) || isset($params['apply']))
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
		if ($newtemplate != $tplname && $this->GetTemplate($newtemplate) != '')
		{
			$error = $this->Lang('templateexists');
		}
		else
		{
			if ($newtemplate != $tplname)
			{
				$this->DeleteTemplate($tplname);
			}
			$this->SetTemplate($newtemplate, $content);
			$this->SetPreference('tpl_' . $newtemplate . '_recursive', $recursive);
			$default_template = $this->GetPreference('default_template');
			if( $tplname == $default_template )
			{
				$this->SetPreference('default_template',$newtemplate);
			}
			if (isset($params['submit']))
				$this->Redirect($id, 'defaultadmin', $returnid);
		}
	}
}
else
{
	if ($newtemplate == '')
	{
		$newtemplate = $tplname;
	}
	$blah = $this->GetTemplate($newtemplate);
	$recursive = $this->GetPreference('tpl_' . $newtemplate . '_recursive', '1');
	if ($blah != '')
	{
		$content = $blah;
	}
}

if( !empty($error) )
{
	echo $this->ShowErrors($error);
}

$smarty->assign('startform', $this->CreateFormStart($id, 'edittemplate', $returnid));
$smarty->assign('endform', $this->CreateFormEnd());
$smarty->assign('newtemplate', $this->Lang('newtemplate'));
$smarty->assign('inputname', $this->CreateInputText($id, 'newtemplate', $newtemplate, 20, 255));
$smarty->assign('content', $this->Lang('templatecontent'));
$smarty->assign('inputcontent', $this->CreateSyntaxArea($id, $content, 'templatecontent'));
$smarty->assign('inputrecursive', $this->CreateInputHidden($id, 'recursive', '0') . $this->CreateInputCheckbox($id, 'recursive', '1', ($recursive == '1')));
$smarty->assign('hidden', $this->CreateInputHidden($id, 'tplname', $params['tplname']));
$smarty->assign('submit', $this->CreateInputSubmit($id, 'submit', lang('submit')));
$smarty->assign('cancel', $this->CreateInputSubmit($id, 'cancel', lang('cancel')));
$smarty->assign('apply', $this->CreateInputSubmit($id, 'apply', lang('apply')));

echo $this->ProcessTemplate('edittemplate.tpl');

?>
