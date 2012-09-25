<?php
if (!isset($gCms)) exit;

if( !$this->CheckPermission( 'Modify Templates' ) )
{
	return;
}

if( isset( $params['defaultsbutton'] ) )
{
	// reset to system defaults
	$this->SetTemplate('displaydetail',$this->GetDetailHtmlTemplate());
}
else
{
	$this->SetTemplate('displaydetail', $params['templatecontent2']);
}

$params = array('tab_message'=> 'detailtemplateupdated', 'active_tab' => 'detail_template');
$this->Redirect($id, 'defaultadmin', '', $params);

?>
