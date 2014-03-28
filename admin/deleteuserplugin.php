<?php
#CMS - CMS Made Simple
#(c)2004 by Ted Kulp (wishy@users.sf.net)
#Visit our homepage at: http://www.cmsmadesimple.org
#
#This program is free software; you can redistribute it and/or modify
#it under the terms of the GNU General Public License as published by
#the Free Software Foundation; either version 2 of the License, or
#(at your option) any later version.
#
#This program is distributed in the hope that it will be useful,
#but WITHOUT ANY WARRANTY; without even the implied warranty of
#MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#GNU General Public License for more details.
#You should have received a copy of the GNU General Public License
#along with this program; if not, write to the Free Software
#Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
#$Id$

$CMS_ADMIN_PAGE=1;

require_once("../include.php");
$urlext='?'.CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];

check_login();

$db = cmsms()->GetDb();

$userplugin_id = -1;
if (isset($_GET["userplugin_id"])) {

	$userplugin_id = $_GET["userplugin_id"];
	$userplugin_name = "";
	$userid = get_userid();
	$access = check_permission($userid, 'Modify User-defined Tags');

	if ($access) {

		$query = "SELECT userplugin_name FROM ".cms_db_prefix()."userplugins WHERE userplugin_id = ?";
		$result = $db->Execute($query, array($userplugin_id));

		if ($result && $result->RecordCount()) {
			$row = $result->FetchRow();
			$userplugin_name = $row['userplugin_name'];
		}

		Events::SendEvent('Core', 'DeleteUserDefinedTagPre', array('id' => $userplugin_id, 'name' => &$userplugin_name));

		$query = 'SELECT event_id,handler_id,handler_order FROM '.cms_db_prefix().'event_handlers 
                           WHERE tag_name = ?'; 
		$handlers = $db->GetArray($query,array($userplugin_name));
		if( is_array($handlers) && count($handlers) > 0 )
		  {
		    $q1 = 'DELETE FROM '.cms_db_prefix().'event_handlers WHERE handler_id = ?';
		    $q2 = 'UPDATE '.cms_db_prefix().'event_handlers SET handler_order = (handler_order - 1)
                            WHERE handler_order > ? AND event_id = ?';
		    foreach( $handlers as $tmp )
		      {
			$hid = $tmp['handler_id'];
			$eid = $tmp['event_id'];

			$db->Execute($q1,array($hid));
			$db->Execute($q2,array($tmp['handler_order'],$eid));
		      }
		  }

		$query = "DELETE FROM ".cms_db_prefix()."userplugins where userplugin_id = ?";
		$result = $db->Execute($query,array($userplugin_id));
		if ($result)
		{
			Events::SendEvent('Core', 'DeleteUserDefinedTagPost', array('id' => $userplugin_id, 'name' => &$userplugin_name));
			// put mention into the admin log
			audit($userplugin_id, 'User Defined Tag: '.$userplugin_name, 'Deleted');
		}
	}
}

redirect('listusertags.php'.$urlext.'&message=usertagdeleted');


?>
