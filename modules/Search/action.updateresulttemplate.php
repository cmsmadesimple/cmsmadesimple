<?php
if (!isset($gCms)) exit;

if( !$this->CheckPermission( 'Modify Templates' ) )
{
	return;
}

if( isset( $params['defaultsbutton'] ) )
{
	// reset to system defaults
	$this->SetTemplate('displayresult',$this->GetResultsHtmlTemplate());
}
else
{
	$this->SetTemplate('displayresult', $params['templatecontent2']);
}

$params = array('tab_message'=> 'resulttemplateupdated', 'active_tab' => 'result_template');
$this->Redirect($id, 'defaultadmin', '', $params);

?>