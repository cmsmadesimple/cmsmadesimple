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
$CMS_LOAD_ALL_PLUGINS=1;

require_once("../include.php");

// function eventhandler_usertag_dropdown( $name, $selitem, $usertags )
// {
//   $text  = '<select name="'.$name.'">';
//   foreach( $usertags as $key => $value )
//     {
//       $text .= '<option value="'.$value.'"';
//       if( $selitem == $value )
// 	{
// 	  $text .= ' selected="selected"';
// 	}
//       $text .= '>'.$key;
//       $text .= '</option>';
//     }
//   $text .= '</select>';
//   return $text;
// }

$userid = get_userid();
$access = check_permission($userid, "Modify Events");

check_login();

// here we'll handle setting $action based on _POST['action']
$action = "";
$module = "";
$event = "";
$modulefilter = '';
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
if( isset( $_GET['modulefilter'] ) && $_GET['modulefilter'] != '' )
  {
    $modulefilter = $_GET['modulefilter'];
  }

// display the page
include_once("header.php");

$editImg = $themeObject->display_image('icons/system/edit.gif', lang('edit'),'','','systemicon');
$infoImg = $themeObject->display_image('icons/system/info.gif', lang('help'),'','','systemicon');

echo '<div class="pagecontainer">';
echo '<div class="pageoverflow">';
echo $themeObject->show_header('eventhandlers');

switch( $action )
{
	case 'showeventhelp':
	{
		if ($module == 'Core')
			$text = CmsEvents::GetEventHelp($event);
		else
			$text = $gCms->modules[$module]['object']->GetEventHelp( $event );
		echo "<h3>$event</h3>";
		if( $text == "" )
		{
			echo "No text returned";
		}
		else
		{
			echo $text;
		}
		break;
	}

	default:
	{
		$events = CmsEventOperations::list_events();

		echo '<br /><p><form action="eventhandlers.php" method="get">'.lang('filterbymodule').': <select name="modulefilter">' . "\n";
		echo '<option value="">'.lang('showall').'</option>';
		$modlist = array();
		if( is_array($events) )
		{
			foreach( $events as $oneevent )
			{
				if (!in_array($oneevent['module_name'], $modlist))
					$modlist[] = $oneevent['module_name'];
			}
		}
		if (count($modlist) > 0)
		{
			foreach($modlist as $onemod)
			{
				echo '<option value="'.$onemod.'"';
				if ($onemod == $modulefilter)
					echo ' selected="selected"';
				echo '>'.$onemod.'</option>';
			}
		}
		echo "</select> <input type=\"submit\" value=\"".lang('submit')."\" /></form></p>\n\n";

		echo "<table cellspacing=\"0\" class=\"pagetable\">\n";
		echo "<thead>\n";
		echo "  <tr>\n";
		echo "    <th>".lang('originator')."</th>\n";
		echo "    <th>".lang('event')."</th>\n";
		echo "    <th width='50%'>".lang('description')."</th>\n";
		echo "    <th class=\"pageicon\">&nbsp;</th>\n";
		echo "    <th class=\"pageicon\">&nbsp;</th>\n";
		echo "  </tr>\n";
		echo "</thead>\n";
		echo "<tbody>\n";

		if( is_array($events) )
		{
			$curclass = 'row1';
			foreach( $events as $oneevent )
			{
				if ($modulefilter == '' || $modulefilter == $oneevent['module_name'])
				{
					echo "<tr class=\"".$curclass."\" onmouseover=\"this.className='".$curclass.'hover'."';\" onmouseout=\"this.className='".$curclass."';\">\n";

					$desctext = '';
					if ($oneevent['module_name'] == 'Core') {
						$desctext = CmsEventOperations::get_event_description($oneevent['event_name']);
						echo "    <td>".lang('core')."</td>\n";
					}
					else if (isset($gCms->modules[$oneevent['module_name']])) {
						$objinstance =& $gCms->modules[$oneevent['module_name']]['object'];
						$desctext = $objinstance->get_event_description($oneevent['event_name']);
						echo "    <td>".$objinstance->get_friendly_name()."</td>\n";
					}
					echo "    <td>";
if ($access) 
{
					echo "<a href=\"editevent.php?action=edit&amp;module=".$oneevent['module_name']."&amp;event=".$oneevent['event_name']."\">";
}
					echo $oneevent['event_name'];
if ($access) 
{
					echo "</a>";
}
					echo "</td>\n";
					echo "    <td>".$desctext."</td>\n";
					echo "    <td class=\"icons_wide\"><a href=\"eventhandlers.php?action=showeventhelp&amp;module=".$oneevent['module_name']."&amp;event=".$oneevent['event_name']."\">".$infoImg."</a></td>\n";
if ($access) 
{
					echo "    <td class=\"icons_wide\"><a href=\"editevent.php?action=edit&amp;module=".$oneevent['module_name']."&amp;event=".$oneevent['event_name']."\">".$editImg."</a></td>\n";
}
					echo "  </tr>\n";
					($curclass=="row1"?$curclass="row2":$curclass="row1");
				}
			}
		}

		echo "</tbody>\n";
		echo "</table>\n";
	} // default action

} // switch


echo "<p class=\"pageback\"><a class=\"pageback\" href=\"".$themeObject->back_url()."\">&#171; ".lang('back')."</a></p>\n";

echo "</div>\n";
echo "</div>\n";

include_once("footer.php");

?>