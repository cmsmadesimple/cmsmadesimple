<?php
if (!isset($gCms)) exit;

if( !$this->CheckPermission( 'Modify Templates' ) )
{
  return;
}
if( isset( $params['defaultsbutton'] ) )
{
   // reset to system defaults
   $this->SetTemplate('displaysearch',$this->GetSearchHtmlTemplate());
}
else
{
  $this->SetTemplate('displaysearch', $params['templatecontent']);
}
$params = array('tab_message'=> 'searchtemplateupdated', 'active_tab' => 'search_template');
$this->Redirect($id, 'defaultadmin', '', $params);
?>