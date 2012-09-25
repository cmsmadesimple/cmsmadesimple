<?php
if (!isset($gCms)) exit;
if (!$this->CheckPermission('Manage Menu')) exit;

$error = '';
$newtemplate = '';
if (isset($params['newtemplate'])) $newtemplate = $params['newtemplate'];
$content = '';
if (isset($params['templatecontent'])) $content = $params['templatecontent'];

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
		        $this->clear_cache();
			$this->SetTemplate($newtemplate, $content);
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

$this->smarty->assign('startform', $this->CreateFormStart($id, 'addtemplate', $returnid));
$this->smarty->assign('endform', $this->CreateFormEnd());
$this->smarty->assign('newtemplate', $this->Lang('newtemplate'));
$this->smarty->assign('inputname', $this->CreateInputText($id, 'newtemplate', $newtemplate, 20, 255));
$this->smarty->assign('content', $this->Lang('templatecontent'));
$this->smarty->assign('inputcontent', $this->CreateSyntaxArea($id, $content, 'templatecontent'));
#$this->smarty->assign('hidden', $this->CreateInputHidden($id, 'tplname', $params['tplname']));
$this->smarty->assign('submit', $this->CreateInputSubmit($id, 'submit', lang('submit')));
$this->smarty->assign('cancel', $this->CreateInputSubmit($id, 'cancel', lang('cancel')));

echo $this->ProcessTemplate('edittemplate.tpl');

?>
