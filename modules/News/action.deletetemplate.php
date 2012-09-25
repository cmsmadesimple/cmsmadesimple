<?php
if( !isset($gCms) ) exit;
if( ! $this->CheckPermission('Modify Templates') )
  {
    // todo, permissions message here
    return;
  }

if( !(isset($params['template'])) )
  {
    $this->_DisplayErrorPage( $id, $params, $returnid, 
			      $this->Lang('error_insufficientparams'));
    return;
  }

$template = cms_html_entity_decode($params['template']);
$this->DeleteTemplate($template);
$this->myRedirectToTab($id, 'uploadform_template');

?>