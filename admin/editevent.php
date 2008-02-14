<?php
#CMS - CMS Made Simple
#(c)2004-2008 by Ted Kulp (ted@cmsmadesimple.org)
#This project's homepage is: http://cmsmadesimple.sf.net
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
#$Id: listtags.php 2772 2006-05-17 02:25:27Z wishy $

$CMS_ADMIN_PAGE=1;

require_once("../include.php");
global $gCms;
$db = cms_db();

$userid = get_userid();
$access = check_permission($userid, "Modify Events");

function display_error( $text )
{
  echo "<div class=\"pageerrorcontainer\"><p class=\"pageerror\">$text</p></div>\n";
}

check_login();

include_once("header.php");

$downImg = $themeObject->display_image('icons/system/arrow-d.gif', lang('down'),'','','systemicon');
$upImg = $themeObject->display_image('icons/system/arrow-u.gif', lang('up'),'','','systemicon');
$deleteImg = $themeObject->display_image('icons/system/delete.gif', lang('delete'),'','','systemicon');


echo "<div class=\"pagecontainer\">\n";
echo "<div class=\"pageoverflow\">\n";
echo $themeObject->show_header('editeventhandler');
echo "</div>\n";

if ($access)
{
	$action = "";
	$module = "";
	$event = "";
	$handler = "";
	if( isset( $_POST['add'] ) )
	{
		// we're adding some funky event handler
		if( isset( $_POST['module'] ) && $_POST['module'] != '' )
		{
			$module = $_POST['module'];
		}
		if( isset( $_POST['event'] ) && $_POST['event'] != '' )
		{
			$event = $_POST['event'];
		}
		if( isset( $_POST['handler'] ) )
		{
			// handler is set, we just have to try to set it
			$handler = $_POST['handler'];
		}
		if( $module && $event && $handler )
		{
			if( substr( $handler, 0, 2 ) == "m:" )
			{
				$handler = substr( $handler, 2 );
				CmsEventOperations::add_event_handler( $module, $event, false, $handler );
			}
			else
			{
				CmsEventOperations::add_event_handler( $module, $event, $handler );
			}
		}
	}
	else
	{
		$cur_order = -1;

		// we're processing an up/down or delete
		if( isset( $_GET['action'] ) && $_GET['action'] != '' )
		{
			$action = $_GET['action'];
		}
		if( isset( $_GET['module'] ) && $_GET['module'] != '' )
		{
			$module = $_GET['module'];
		}
		if( isset( $_GET['event'] ) && $_GET['event'] != '' )
		{
			$event = $_GET['event'];
		}
		if( isset( $_GET['handler'] ) && $_GET['handler'] != '' )
		{
			$handler = $_GET['handler'];
		}
		if( isset( $_GET['order'] ) && $_GET['order'] != '' )
		{
			$cur_order = $_GET['order'];
		}

		switch( $action )
		{
			case 'up':
				// move an item up (decrease the order)
				// increases the previous order, and decreases the current handler id
				if( $handler == "" || $module == "" || $event == "" || $action == "" ||
					($cur_order == "" && $action != "delete") )
				{
					display_error( lang("missingparams" ) );
				}

				$event_handler = cms_orm('CmsEventHandler')->find_by_id($handler);
				if ($event_handler != null)
				{
					$event_handler->move_up();
				}

				break;

			case 'down':
				// move an item down (increase the order)
				// move an item up (decrease the order)
				// increases the previous order, and decreases the current handler id
				if( $handler == "" || $module == "" || $event == "" || $action == "" ||
					($cur_order == "" && $action != "delete") )
				{
					display_error( lang("missingparams" ) );
				}

				$event_handler = cms_orm('CmsEventHandler')->find_by_id($handler);
				if ($event_handler != null)
				{
					$event_handler->move_down();
				}

				break;

			case 'delete':

				if( $handler == "" || $module == "" || $event == "" || $action == "" ||
					($cur_order == "" && $action != "delete") )
				{
					display_error( lang("missingparams" ) );
				}

				$event_handler = cms_orm('CmsEventHandler')->find_by_id($handler);
				if ($event_handler != null)
				{
					$event_handler->delete();
				}
				break;

			default:
			// unknown or unset action
			break;
		} // switch
	}
		
	// get the event description
	global $gCms;
	$usertagops =& $gCms->GetUserTagOperations();
	//$description = $gCms->modules[$module]['object']->GetEventDescription($event);
	
	$description = '';
	$modulename = '';
	if ($module == 'Core') {
		$description = CmsEventOperations::get_event_description($event);
		$modulename = lang('core');
	}
	else if (isset($gCms->modules[$module])) {
		$objinstance =& $gCms->modules[$module]['object'];
		$description = $objinstance->get_event_description($event);
		$modulename = $objinstance->get_friendly_name();
	}
	
	// and now get the list of handlers for this event
	$handlers = CmsEventOperations::list_event_handlers( $module, $event );
	//print_r( $handlers ); echo "<br/><br/>";
	
	// and the list of all available handlers
	$allhandlers = array();
	// we get the list of user tags, and add them to the list
	$usertags = cms_orm('CmsUserTag')->find_all(array('order' => 'name ASC'));
	foreach( $usertags as $usertag )
	{
	$allhandlers[$usertag->name] = $usertag->name;
	}
	// and the list of modules, and add them
	foreach( $gCms->modules as $key => $value )
	{
	if( $key == $modulename )
		{
		continue;
		}
	
	if( $gCms->modules[$key]['object']->handles_events() )
		{
		$allhandlers[$key] = 'm:'.$key;
		}
	}
	
	echo "<div class=\"pageoverflow\">\n";
	echo "<p class=\"pagetext\">".lang("module_name").":</p>\n";
	echo "<p class=\"pageinput\">".$modulename."</p>\n";
	echo "</div>\n";
	echo "<div class=\"pageoverflow\">\n";
	echo "<p class=\"pagetext\">".lang("event_name").":</p>\n";
	echo "<p class=\"pageinput\">".$event."</p>\n";
	echo "</div>\n";
	echo "<div class=\"pageoverflow\">\n";
	echo "<p class=\"pagetext\">".lang("event_description").":</p>\n";
	echo "<p class=\"pageinput\">".$description."</p>\n";
	echo "</div>\n";
	
	echo "<br/><table cellspacing=\"0\" class=\"pagetable\">\n";
	echo "<thead>\n";
	echo "  <tr>\n";
	echo "    <th>".lang('order')."</th>\n";
	echo "    <th>".lang('user_tag')."</th>\n";
	echo "    <th>".lang('module')."</th>\n";
	echo "    <th class=\"pageicon\">&nbsp;</th>\n";
	echo "    <th class=\"pageicon\">&nbsp;</th>\n";
	echo "    <th class=\"pageicon\">&nbsp;</th>\n";
	echo "  </tr>\n";
	echo "</thead>\n";
	
	$rowclass = "row1";
	if( $handlers != false )
	{
		echo "<tbody>\n";
		$idx = 0;
		$url = "editevent.php?module=".$module."&amp;event=".$event;
		foreach( $handlers as $onehandler )
		{
		echo "<tr class=\"$rowclass\">\n";
		echo "  <td>".$onehandler['order_num']."</td>\n";
		echo "  <td>".$onehandler['tag_name']."</td>\n";
		echo "  <td>".$onehandler['module_name']."</td>\n";
		if( $idx != 0 ) 
		{
			echo "  <td><a href=\"".$url."&amp;action=up&amp;order=".$onehandler['order_num']."&amp;handler=".$onehandler['id']."\">$upImg</a></td>\n";
		}
		else
		{
			echo "<td>&nbsp;</td>";
		}
		if( $idx + 1 != count($handlers) )
		{
			echo "  <td><a href=\"".$url."&amp;action=down&amp;order=".$onehandler['order_num']."&amp;handler=".$onehandler['id']."\">$downImg</a></td>\n";
		}
		else
		{
			echo "<td>&nbsp;</td>";
		}
		if( $onehandler['removable'] == 1 )
		{
			echo "  <td><a href=\"".$url."&amp;action=delete&amp;order=".$onehandler['order_num']."&amp;handler=".$onehandler['id']."\">$deleteImg</a></td>\n";
		}
		else
		{
			echo "  <td>&nbsp;</td>\n";
		}
		echo "</tr>\n";
	
		$idx++;
		}
		echo "</tbody>\n";
	}
	echo "</table>\n";
	
	echo "<br/><form action=\"editevent.php?action=create\" method=\"post\">\n";
	echo "<select name=\"handler\">\n";
	foreach( $allhandlers as $key => $value )
	{
	echo "<option value=\"$value\">$key</option>\n";
	}
	echo "</select>\n";
	echo "<input type=\"hidden\" name=\"module\" value=\"$module\">\n";
	echo "<input type=\"hidden\" name=\"event\" value=\"$event\">\n";
	echo "<input type=\"submit\" name=\"add\" value=\"".lang('add')."\">";
	echo "</form>\n";
	echo "<p class=\"pageback\"><a class=\"pageback\" href=\"".$themeObject->back_url()."\">&#171; ".lang('back')."</a></p>\n";
	
	echo "</div>\n";
}
else
{
	display_error(lang('noaccessto', array(lang('editeventhandler'))));
}
include_once("footer.php");

?>
