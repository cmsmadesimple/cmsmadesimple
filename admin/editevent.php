<?php
#CMS - CMS Made Simple
#(c)2004 by Ted Kulp (wishy@users.sf.net)
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

$userid = get_userid();
$access = check_permission($userid, "Modify Modules");


check_login();

$action = "";
$module = "";
$event = "";
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
	if( strstr( $handler, "m:" ) == 0 )
	  {
	    $handler = substr( $handler, 2 );
	    Events::AddEventHandler( $module, $event, false, $handler );
	  }
	else
	  {
	    Events::AddEventHandler( $module, $event, $handler );
	  }
      }
  }
else
  {
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
  }
    
// get the event description
$description = $gCms->modules[$module]['object']->GetEventDescription($event);

// and now get the list of handlers for this event
$handlers = Events::ListEventHandlers( $module, $event );

// and the list of all available handlers
$allhandlers = array();
// we get the list of user tags, and add them to the list
$usertags = UserTags::ListUserTags();
foreach( $usertags as $key => $value )
{
  $allhandlers[$value] = $key;
}
// and the list of modules, and add them
foreach( $gCms->modules as $key => $value )
{
  $allhandlers[$key] = 'm:'.$key;
}

include_once("header.php");
$downImg = $themeObject->DisplayImage('icons/system/arrow-d.gif', lang('down'),'','','systemicon');
$upImg = $themeObject->DisplayImage('icons/system/arrow-u.gif', lang('up'),'','','systemicon');
$deleteImg = $themeObject->DisplayImage('icons/system/delete.gif', lang('delete'),'','','systemicon');


echo "<div class=\"pagecontainer\">\n";
echo "<div class=\"pageoverflow\">\n";
echo $themeObject->ShowHeader('eventhandlers');
echo "</div>\n";

echo "<div class=\"pageoverflow\">\n";
echo "<p class=\"pagetext\">".lang("module_name").":</p>\n";
echo "<p class=\"pageinput\">".$module.":</p>\n";
echo "</div>\n";
echo "<div class=\"pageoverflow\">\n";
echo "<p class=\"pagetext\">".lang("event_name").":</p>\n";
echo "<p class=\"pageinput\">".$event.":</p>\n";
echo "</div>\n";
echo "<div class=\"pageoverflow\">\n";
echo "<p class=\"pagetext\">".lang("event_description").":</p>\n";
echo "<p class=\"pageinput\">".$description.":</p>\n";
echo "</div>\n";

echo "<table cellspacing=\"0\" class=\"pagetable\">\n";
echo "<thead>\n";
echo "  <tr>\n";
echo "    <th>".lang('order')."</th>\n";
echo "    <th>".lang('user_tag')."</th>\n";
echo "    <th>".lang('module')."</th>\n";
echo "    <th>".lang('up')."</cdth>\n";
echo "    <th>".lang('down')."</th>\n";
echo "    <th>".lang('delete')."</th>\n";
echo "  </tr>\n";
echo "</thead>\n";

$rowclass = "row1";
if( $handlers != false )
  {
    echo "<tbody>\n";
    foreach( $handlers as $onehandler )
      {
	echo "<tr class=\"$rowclass\">\n";
	echo "  <td>".$onehandler['handler_order']."</td>\n";
	echo "  <td>".$onehandler['tag_name']."</td>\n";
	echo "  <td>".$onehandler['module_name']."</td>\n";
	echo "  <td><a href=\"editevent.php?action=up&amp;handler=".$onehandler['handler_id']."\">$upImg</a></td>\n";
	echo "  <td><a href=\"editevent.php?action=down&amp;handler=".$onehandler['handler_id']."\">$downImg</a></td>\n";
	echo "  <td><a href=\"editevent.php?action=delete&amp;handler=".$onehandler['handler_id']."\">$deleteImg</a></td>\n";
	echo "</tr>\n";
      }
    echo "</tbody>\n";
  }
echo "</table>\n";

echo "<form action=\"editevent.php?action=create\" method=\"post\">\n";
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
echo "<p class=\"pageback\"><a class=\"pageback\" href=\"".$themeObject->BackUrl()."\">&#171; ".lang('back')."</a></p>\n";

echo "</div>\n";

include_once("footer.php");

?>
