<?php
if (!isset($gCms)) exit;

if( !$this->CheckPermission( 'Modify Templates' ) )
{
  return;
}
if( isset( $params['defaultsbutton'] ) )
{
   // reset to system defaults
   $this->SetTemplate('displaysummary',$this->GetSummaryHtmlTemplate());
}
else
{
  $this->SetTemplate('displaysummary', $params['templatecontent']);
}
$params = array('tab_message'=> 'summarytemplateupdated', 'active_tab' => 'summary_template');
$this->Redirect($id, 'defaultadmin', '', $params);
?>
