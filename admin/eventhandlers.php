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

function eventhandler_usertag_dropdown( $name, $selitem, $usertags )
{
  $text  = '<select name="'.$name.'">';
  foreach( $usertags as $key => $value )
    {
      $text .= '<option value="'.$value.'"';
      if( $selitem == $value )
	{
	  $text .= ' selected="selected"';
	}
      $text .= '>'.$key;
      $text .= '</option>';
    }
  $text .= '</select>';
  return $text;
}

$userid = get_userid();
$access = check_permission($userid, "Modify Modules");

check_login();

// handle a submit
if( isset( $_POST['goforit'] ) )
  {
    foreach( $_POST as $key => $value )
      {
	if( strstr( $key, 'handler_' ) != FALSE )
	  {
	    $key = substr( $key, strlen('handler_') );
	    $p1 = strpos( $key, '_' );
	    $modulename = substr( $key, 0, $p1 );
	    $eventname  = substr( $key, $p1+1 );
	    Events::AddEventHandler( $modulename, $eventname, $value );
	  }
      }
  }

// display the page
include_once("header.php");
echo '<div class="pagecontainer">';
echo '<div class="pageoverflow">';
echo $themeObject->ShowHeader('eventhandlers');

$tmp1 = array('None' => null);
$tmp2 = UserTags::ListUserTags();
$tags = array_merge( $tmp1, $tmp2 );
$events = Events::ListEvents();

echo '<form id="eventhandlerform" method="post" action="eventhandlers.php">';
echo "<table cellspacing=\"0\" class=\"pagetable\">\n";
echo '<thead>';
echo "  <tr>\n";
echo "    <th>".lang('module')."</th>\n";
echo "    <th>".lang('event')."</th>\n";
echo "    <th>".lang('description')."</th>";
echo "    <th>".lang('handler')."</th>";
echo "  </tr>\n";
echo '</thead>';
echo '<tbody>';

if( is_array($events) )
  {
    $curclass = 'row1';
    foreach( $events as $oneevent )
      {
	echo "<tr class=\"".$curclass."\" onmouseover=\"this.className='".$curclass.'hover'."';\" onmouseout=\"this.className='".$curclass."';\">\n";

	echo "    <td>".$oneevent['module_name']."</td>\n";
	echo "    <td>".$oneevent['event_name']."</td>\n";
	echo "    <td>".$gCms->modules[$oneevent['module_name']]['object']->GetEventDescription($oneevent['event_name']);
	echo "    <td>".eventhandler_usertag_dropdown( 'handler_'.$oneevent['module_name'].'_'.$oneevent['event_name'],
						       $oneevent['handler_name'], $tags );
	echo "  </tr>\n"; 
	($curclass=="row1"?$curclass="row2":$curclass="row1");
      }
  }


echo '</tbody>';
echo '</table>';
echo '<input type="hidden" name="goforit" value="1">';
echo '<input type="submit" value="'.lang('submit').'" class="pagebutton" onmouseover="this.className=\'pagebuttonhover\';" onmouseout="this.className=\'pagebutton\';" />';
echo "</form>\n";
echo '<p class="pageback"><a class="pageback" href="'.$themeObject->BackUrl().'">&#171; '.lang('back').'</a></p>';
echo '</div>';
echo '</div>';


include_once("footer.php");

?>