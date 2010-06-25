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
$CMS_LOAD_ALL_PLUGINS=1;

require_once("../include.php");
$urlext='?'.CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];

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
check_login();
$userid = get_userid();
$access = check_permission($userid, "Modify Events");

if (!$access) {
	die('Permission Denied');
return;
}


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

$editImg = $themeObject->DisplayImage('icons/system/edit.gif', lang('edit'),'','','systemicon');
$infoImg = $themeObject->DisplayImage('icons/system/info.gif', lang('help'),'','','systemicon');

echo '<div class="pagecontainer">';
echo '<div class="pageoverflow">';
echo $themeObject->ShowHeader('eventhandlers');

switch( $action )
{
	case 'showeventhelp':
	{
	  $text = '';
		if ($module == 'Core')
		  $text = Events::GetEventHelp($event);
		else if( isset($gCms->modules[$module]) )
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
		$events = Events::ListEvents();

		echo '<br /><form action="eventhandlers.php" method="get">';
		echo '<div><input type="hidden" name="'.CMS_SECURE_PARAM_NAME.'" value="'.$_SESSION[CMS_USER_KEY].'" /></div>';

		echo lang('filterbymodule').': <select name="modulefilter">' . "\n";
		echo '<option value="">'.lang('showall').'</option>';
		$modlist = array();
		if( is_array($events) )
		{
			foreach( $events as $oneevent )
			{
				if (!in_array($oneevent['originator'], $modlist))
					$modlist[] = $oneevent['originator'];
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
		echo "</select> <input type=\"submit\" value=\"".lang('submit')."\" /></form>\n\n";

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
				if ($modulefilter == '' || $modulefilter == $oneevent['originator'])
				{
					echo "<tr class=\"".$curclass."\" onmouseover=\"this.className='".$curclass.'hover'."';\" onmouseout=\"this.className='".$curclass."';\">\n";

					$desctext = '';
					if ($oneevent['originator'] == 'Core') {
						$desctext = Events::GetEventDescription($oneevent['event_name']);
						echo "    <td>".lang('core')."</td>\n";
					}
					else if (isset($gCms->modules[$oneevent['originator']])) {
						$objinstance =& $gCms->modules[$oneevent['originator']]['object'];
						$desctext = $objinstance->GetEventDescription($oneevent['event_name']);
						echo "    <td>".$objinstance->GetFriendlyName()."</td>\n";
					}
					echo "    <td>";
if ($access) 
{
					echo "<a href=\"editevent.php".$urlext."&amp;action=edit&amp;module=".$oneevent['originator']."&amp;event=".$oneevent['event_name']."\">";
}
					echo $oneevent['event_name'];
if ($access) 
{
					echo "</a>";
}
					echo "</td>\n";
					echo "    <td>".$desctext."</td>\n";
					echo "    <td class=\"icons_wide\"><a href=\"eventhandlers.php".$urlext."&amp;action=showeventhelp&amp;module=".$oneevent['originator']."&amp;event=".$oneevent['event_name']."\">".$infoImg."</a></td>\n";
if ($access) 
{
					echo "    <td class=\"icons_wide\"><a href=\"editevent.php".$urlext."&amp;action=edit&amp;module=".$oneevent['originator']."&amp;event=".$oneevent['event_name']."\">".$editImg."</a></td>\n";
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


echo "<p class=\"pageback\"><a class=\"pageback\" href=\"".$themeObject->BackUrl()."\">&#171; ".lang('back')."</a></p>\n";

echo "</div>\n";
echo "</div>\n";

include_once("footer.php");

?>