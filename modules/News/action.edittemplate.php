<?php

if (!is_object(cmsms())) exit;

#---------------------
# Check params
#---------------------

if( ! $this->CheckPermission('Modify Templates') ) {

    // todo, permissions message here
    return;
}

if( isset( $params['cancel'] ) ) {

    if( isset( $params['active_tab'] ) && $params['active_tab'] != '' ) {
	
		$this->myRedirectToTab($id, $params['active_tab']);
    }
	
    $this->myRedirect($id,'defaultadmin');
}

// check if we have a template name (Useless?)
if( !(isset($params['template']) || isset($params['prefix'])) ) {

    $params['errors'] = $this->Lang('error_insufficientparams');
    $this->Redirect($id,'defaultadmin','',$params);
}

// (Useless?)
if( !isset($params['mode']) || !isset($params['title']) ) {

    $params['errors'] = $this->Lang('error_insufficientparams');
    $this->Redirect($id,'defaultadmin','',$params);
}

$templatename = "";
if(isset($params['template'])) {

	$templatename = $params['template'];
}

$contents = "";
if( isset($params['templatecontent']) ) {

	$contents = $params['templatecontent']; // <- Try to get template from params
}
else if( isset($params['defaulttemplatepref']) && $params['defaulttemplatepref'] != '' ) {

	$contents = $this->GetPreference($params['defaulttemplatepref']); // <- Try to get template from who knows where Calguy check this
}
else {

	$contents = $this->GetTemplate($params['prefix'].$params['template']); // <- Others failed, let's try to get it from database
}

#---------------------
# Submit
#---------------------

if(isset($params['submitbutton']) || isset($params['applybutton'])) {

	$errors = array();
	
	if (empty($params['templatecontent'])) {
	
		$errors[] = $this->Lang('error_insufficientparams');
	}
	
	if (empty($params['template'])) {
	
		$errors[] = $this->Lang('error_insufficientparams');
	}	

	if (empty($errors)) {

		$this->SetTemplate( $params['prefix'].$params['template'], $contents );

		if(isset($params['submitbutton'])) {

			if( isset( $params['active_tab'] ) && $params['active_tab'] != '' ) {
			
				$this->myRedirectToTab($id, $params['active_tab']);
			}
			
			$this->myRedirect($id,'defaultadmin');		
		}
	}
}

#---------------------
# Errors
#---------------------

if (!empty($errors)) {

    echo $this->ShowErrors($errors);
}
#---------------------
# Smarty processing
#---------------------

if( $params['mode'] == 'add' ) {

    $smarty->assign('templatename',$this->CreateInputText( $id, 'template', $templatename, 40 ));
}
else {

    $smarty->assign('templatename',$params['template']);
    $smarty->assign('apply',$this->CreateInputSubmit ($id, 'applybutton', $this->Lang('apply')));
}

$smarty->assign('formstart',$this->CreateFormStart ($id, 'edittemplate',$returnid,'post','',false, '', $params));
$smarty->assign('formend',$this->CreateFormEnd());
$smarty->assign('warning_preview',$this->Lang('warning_preview'));
$smarty->assign('title',cms_html_entity_decode($params['title']));
$smarty->assign('prompt_templatename',$this->Lang('prompt_templatename'));
$smarty->assign('prompt_template',$this->Lang('prompt_template'));
$smarty->assign('template',$this->CreateSyntaxArea ($id, $contents, 'templatecontent'));
$smarty->assign('submit',$this->CreateInputSubmit ($id, 'submitbutton', $this->Lang ('submit')));
$smarty->assign('cancel',$this->CreateInputSubmit ($id, 'cancel', $this->Lang ('cancel')));

echo $this->ProcessTemplate('edittemplate.tpl');

?>
