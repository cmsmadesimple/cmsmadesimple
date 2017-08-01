<?php
namespace AdminLog;
if( !isset($gCms) ) exit;
if( !$this->VisibleToAdminUser() ) exit;

$filter = new filter;
if( isset($_SESSION['adminlog_filter']) && $_SESSION['adminlog_filter'] instanceof \AdminLog\filter ) {
    $filter = $_SESSION['adminlog_filter'];
}
// override the limit to 1000000 lines
$filter->limit = 1000000;
$result = new resultset( $db, $filter );

$dateformat = trim(\cms_userprefs::get_for_user(get_userid(),'date_format_string','%x %X'));
if( empty($dateformat) ) $dateformat = '%x %X';
header('Content-type: text/plain');
header('Content-Disposition: attachment; filename="adminlog.txt"');
while( !$result->EOF() ) {
    $row = $result->GetObject();
    echo strftime($dateformat,$row['timestamp'])."|";
    echo $row['username'] . "|";
    echo (((int)$row['item_id']==-1)?'':$row['item_id']) . "|";
    echo $row['item_name'] . "|";
    echo $row['action'];
    echo "\n";
    $result->MoveNext();
}
exit;

$sql = 'TRUNCATE TABLE '.CMS_DB_PREFIX.'adminlog';
$db->Execute( $sql );
unset($_SESSION['adminlog_filter']);
audit('','Admin log','Cleared');
$this->SetMessage($this->Lang('msg_cleared'));
$this->RedirectToAdminTab();