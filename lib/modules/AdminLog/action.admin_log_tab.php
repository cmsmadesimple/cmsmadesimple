<?php
namespace AdminLog;
if( !isset($gCms) ) exit;
if( !$this->VisibleToAdminUser() ) exit;

$page = 1;
$filter_applied = false;
$filter = new filter;
if( isset($_SESSION['adminlog_filter']) && $_SESSION['adminlog_filter'] instanceof \AdminLog\filter ) {
    $filter = $_SESSION['adminlog_filter'];
    $filter_applied = true;
}

if( isset($params['filter']) ) {
    $filter = new filter;
    unset($_SESSION['adminlog_filter']);
    $filter_applied = false;
}
if( isset($params['filter']) ) {
    $filter->severity = (int) get_parameter_value($params,'f_sev');
    $filter->username = trim(get_parameter_value($params,'f_user'));
    $filter->subject = trim(get_parameter_value($params,'f_subj'));
    $filter->msg = trim(get_parameter_value($params,'f_msg'));
    $_SESSION['adminlog_filter'] = $filter;
    $filter_applied = true;
}
if( ($page = (int) get_parameter_value($params,'page',1)) > 1 ) {
    $filter->offset = ($page - 1) * $filter->limit;
}

$resultset = new resultset( $db, $filter );

$pagelist = null;
if( $resultset->numpages > 0 ) {
    if( $resultset->numpages < 25 ) {
        for( $i = 1; $i <= $resultset->numpages; $i++ ) {
            $pagelist[$i] = $i;
        }
    }
    else {
        // first 5
        for( $i = 0; $i <= 5; $i++ ) {
            $pagelist[$i] = $i;
        }
        $tpage = $page;
        if( $tpage <= 5 || $tpage >= ($resultset->numpages - 5) ) $tpage = $resultset->numpages / 2;
        $x1 = max(1,(int)($tpage - 5 / 2));
        $x2 = min($resultset->numpages,(int)($tpage + 5 / 2));
        for( $i = $x1; $i <= $x2; $i++ ) {
            $pagelist[] = $i;
        }
        for( $i = max(1,$resultset->numpages - 5); $i <= $resultset->numpages; $i++ ) {
            $pagelist[] = $i;
        }
        $pagelist = array_unique($pagelist);
        sort($pagelist);
        $pagelist = array_combine($pagelist,$pagelist);
    }
}
$results = $resultset->GetMatches();

$severity_list = [ 0 => $this->Lang('sev_msg'), 1 => $this->Lang('sev_notice'), $this->Lang('sev_warning'), $this->Lang('sev_error') ];
$tpl = $smarty->CreateTemplate( $this->GetTemplateResource('admin_log_tab.tpl'));
$tpl->assign('filter',$filter);
$tpl->assign('results',$results);
$tpl->assign('pagelist',$pagelist);
$tpl->assign('severity_list',$severity_list);
$tpl->assign('page',$page);
$tpl->assign('filter_applied',$filter_applied);
$tpl->assign('mod',$this);
$tpl->assign('actionid',$id);
$tpl->display();
