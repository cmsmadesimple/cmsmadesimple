<?php
if (!isset($gCms)) exit;
if (!$this->CheckPermission('Manage Menu')) exit;

$error = '';
$tplname = '';
if (isset($params['tplname'])) $tplname = $params['tplname'];
$newtemplate = '';
if (isset($params['newtemplate'])) $newtemplate = $params['newtemplate'];
$content = '';
if (isset($params['templatecontent'])) $content = $params['templatecontent'];

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
	else if ($newtemplate != $tplname && $this->GetTemplate($newtemplate) != '')
	{
	  // template name changed, and new template already exists.
	  $error = $this->Lang('templateexists');
	}
	else
	{
	  if ($newtemplate != $tplname)
	  {
	    // template name changed, so delete the old one.
	    $this->DeleteTemplate($tplname);
	  }

	  $this->clear_cache();
	  $this->SetTemplate($newtemplate, $content);
	  audit('',$this->GetName(),'Edited template '.$newtemplate);
	  $params['tplname'] = $newtemplate;
	  
	  $default_template = $this->GetPreference('default_template');
	  if( $tplname == $default_template )
	  {
	    $this->SetPreference('default_template',$newtemplate);
	    audit('',$this->GetName(),$newtemplate.' set as default');
	  }
	  
	  if (isset($params['submit']))
	  {
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
	if ($blah != '')
	{
		$content = $blah;
	}
}

if( !empty($error) )
  {
    echo $this->ShowErrors($error);
  }

$this->smarty->assign('startform', $this->CreateFormStart($id, 'edittemplate', $returnid));
$this->smarty->assign('endform', $this->CreateFormEnd());
$this->smarty->assign('newtemplate', $this->Lang('newtemplate'));
$this->smarty->assign('inputname', $this->CreateInputText($id, 'newtemplate', $newtemplate, 20, 255));
$this->smarty->assign('content', $this->Lang('templatecontent'));
$this->smarty->assign('inputcontent', $this->CreateSyntaxArea($id, $content, 'templatecontent'));
$this->smarty->assign('hidden', $this->CreateInputHidden($id, 'tplname', $params['tplname']));
$this->smarty->assign('submit', $this->CreateInputSubmit($id, 'submit', lang('submit')));
$this->smarty->assign('cancel', $this->CreateInputSubmit($id, 'cancel', lang('cancel')));
$this->smarty->assign('apply', $this->CreateInputSubmit($id, 'apply', lang('apply')));

echo $this->ProcessTemplate('edittemplate.tpl');

?>
