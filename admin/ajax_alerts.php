<?php
#This file is Copyright (c) 2016 by Robert Campbell <calguy1000@cmsmadesimple.org>
#CMS - CMS Made Simple (CMSMS)
#CMSMS is copyright (c) 2004 by Ted Kulp.
#Visit our homepage at: http://www.cmsmadesimple.org
#
#This program is free software; you can redistribute it and/or modify
#it under the terms of the GNU General Public License as published by
#the Free Software Foundation; either version 2 of the License, or
#(at your option) any later version.
#
#This program is distributed in the hope that it will be useful,
#but WITHOUT ANY WARRANthe TY; without even the implied warranty of
#MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#GNU General Public License for more details.
#You should have received a copy of the GNU General Public License
#along with this program; if not, write to the Free Software
#Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
#$Id: moduleinterface.php 8558 2012-12-10 00:59:49Z calguy1000 $

$CMS_ADMIN_PAGE=1;
require_once("../lib/include.php");
$urlext='?'.CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];
try {
    $out = null;
    $uid = get_userid(FALSE);
    if( !$uid ) throw new \Exception('Permission Denied'); // should be a 403, but meh.

    $op = cleanValue($_POST['op']);
    if( !$op ) $op = 'delete';
    $alert_name = cleanValue($_POST['alert']);

    switch( $op ) {
    case 'delete':
        $alert = \CMSMS\AdminAlerts\Alert::load_by_name($alert_name);
        $alert->delete();
        break;
    default:
        throw new \Exception('Unknown operation '.$op);
    }
    echo $out;
}
catch( \Exception $e ) {
    // do 500 error.
    $handlers = ob_list_handlers();
    for ($cnt = 0; $cnt < sizeof($handlers); $cnt++) { ob_end_clean(); }

    header("HTTP/1.0 500 ".$e->GetMessage());
    header("Status: 500 Server Error");
    echo $e->GetMessage();
}
exit;

#
# EOF
#
?>
