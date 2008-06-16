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
#$Id$

/**
 * This page is in charge with showing the CSS associated with an element, be it
 * a template or anything else.
 *
 * For more informations about CSS associations please see the header of
 * addcssassoc.php
 *
 * There are compulsory arguments, passed by GET
 * - $type	: the type of element
 * - $id	: the id of the element.
 *
 * @since	0.6
 * @author	calexico
 */

$CMS_ADMIN_PAGE=1;

require_once("../include.php");

check_login();

include_once("header.php");
global $gCms;
$db =& $gCms->GetDb();

#******************************************************************************
# global vars definition
#******************************************************************************

# this var is used to store any error that may occur.
$error = "";

# this one is used later to store all the css found, because they won't appear in the dropdown
$csslist = array();

$type = "";

$name = '';

#******************************************************************************
# we now get the parameters
#******************************************************************************

# getting variables
if (isset($_GET["type"])) $type	= trim($_GET["type"]) ;
else $error = lang('typenotvalid');

if (isset($_GET["id"]))	$id	= (int)$_GET["id"] ;
else $error = lang('idnotvalid');

if( isset($_GET['cssid'])) $cssid = (int)$_GET['cssid'];

# if type is template, we get the name
if (isset($type) && "template" == $type) 
{
	$query = "SELECT template_name FROM ".cms_db_prefix()."templates WHERE template_id = ?";
	$result = $db->Execute($query, array($id));

	if ($result)
	{
		$line = $result->FetchRow();
		$name = $line['template_name'];
	}
	else
	{
		$error = lang('errorretrievingtemplate');
	}
}

#******************************************************************************
# first getting all user permissions
#******************************************************************************
$userid = get_userid();

$modify  = check_permission($userid, 'Modify Stylesheet Assoc');
$delasso = check_permission($userid, 'Remove Stylesheet Assoc');
$addasso = check_permission($userid, 'Add Stylesheet Assoc');

#******************************************************************************
# Handle moving of entries
#******************************************************************************
if( isset($_GET['dir']) && $modify )
  {
    switch(trim($_GET['dir'])) 
      {
      case 'up':
	{
	  // get the ord id for this item
	  $q1 = 'SELECT assoc_order FROM '.cms_db_prefix().'css_assoc 
                  WHERE assoc_to_id = ? AND assoc_css_id = ?';
	  $ord = $db->GetOne($q1,array($id,$cssid));

	  if( $ord > 0 )
	    {
	      // get the item with the prev ord id
	      $q2 = 'SELECT assoc_css_id FROM '.cms_db_prefix().'css_assoc
                     WHERE assoc_to_id = ? AND assoc_order = ?';
	      $other_css = $db->GetOne($q2,array($id,$ord-1));
	      if( $other_css )
		{
		  // swap em
		  $q3 = 'UPDATE '.cms_db_prefix().'css_assoc
                         SET assoc_order = ? WHERE
                             assoc_to_id = ? AND assoc_css_id = ?';
		  $db->Execute($q3,array($ord,$id,$other_css));
		  $db->Execute($q3,array($ord-1,$id,$cssid));
		}
	    }
	}
	break;

      case 'down':
	{
	  // get the ord id for this item
	  $q1 = 'SELECT assoc_order FROM '.cms_db_prefix().'css_assoc 
                  WHERE assoc_to_id = ? AND assoc_css_id = ?';
	  $ord = $db->GetOne($q1,array($id,$cssid));

	  // get the item with the prev ord id
	  $q2 = 'SELECT assoc_css_id FROM '.cms_db_prefix().'css_assoc
                     WHERE assoc_to_id = ? AND assoc_order = ?';
	  $other_css = $db->GetOne($q2,array($id,$ord+1));
	  if( $other_css )
	    {
	      // swap em
	      $q3 = 'UPDATE '.cms_db_prefix().'css_assoc
                         SET assoc_order = ? WHERE
                             assoc_to_id = ? AND assoc_css_id = ?';
	      $db->Execute($q3,array($ord,$id,$other_css));
	      $db->Execute($q3,array($ord+1,$id,$cssid));
	    }
	}
	break;
      }
  }

#******************************************************************************
# displaying errors if any
#******************************************************************************
if (isset($_GET["message"])) {
	$message = preg_replace('/\</','',$_GET['message']);
	echo '<div class="pagemcontainer"><p class="pagemessage">'.$message.'</p></div>';
}
	if ("" != $error)
	{
		echo "<div class=\"pageerrorcontainer\"><p class=\"pageerror\">".$error."</p></div>";
	}

	if (!$addasso) {
		echo "<div class=\"pageerrorcontainer\"><p class=\"pageerror\">".lang('noaccessto', array(lang('addcssassociation')))."</p></div>";
	}

#******************************************************************************
# now really starting
#******************************************************************************
else {

  $query = "SELECT assoc_css_id, css_name, assoc_order FROM ".cms_db_prefix()."css_assoc ca INNER JOIN ".cms_db_prefix()."css ON assoc_css_id = css_id WHERE assoc_type=? AND assoc_to_id = ? ORDER BY ca.assoc_order";
  $result = $db->Execute($query, array($type, $id));

  global $gcms;
  $smarty =& $gCms->GetSmarty();
  $smarty->assign('text_move',lang('move'));
  $smarty->assign('text_template',lang('template'));
  $smarty->assign('text_title',lang('title'));
  $smarty->assign('edittemplate_link','<a href="edittemplate.php?template_id='.$_GET['id'].'"  name="edittemplate">'.(isset($name)?$name:"").'</a>');
  $cssassoc = array();
  $count = $result->RecordCount();
  $idx = 0;
  while( $result && $row = $result->FetchRow() )
    {
      $csslist[] = $row["assoc_css_id"];

      $tmp = array();
      $url = "editcss.php?css_id=".$row['assoc_css_id']."&amp;from=templatecssassoc&amp;templateid=".$id;
      $tmp['editlink'] = '<a href="'.$url.'">'.$row['css_name'].'</a>';
      $tmp['editimg'] = '<a href="'.$url.'">'.$themeObject->DisplayImage('icons/system/edit.gif',lang('editcss'),'','','systemicon').'</a>';

      if( $modify )
	{
	  $downurl = 'listcssassoc.php?dir=down&cssid='.$row['assoc_css_id'].'&id='.$id.'&type=template';
	  $upurl = 'listcssassoc.php?dir=up&cssid='.$row['assoc_css_id'].'&id='.$id.'&type=template';
	  if( $idx > 0 )
	    {
	      $tmp['uplink'] = '<a href="'.$upurl.'">'.$themeObject->DisplayImage('icons/system/arrow-u.gif',lang('up'),'','','systemicon').'</a>';
	    }
	  if( $idx + 1 < $count )
	    {
	      $tmp['downlink'] = '<a href="'.$downurl.'">'.$themeObject->DisplayImage('icons/system/arrow-d.gif',lang('down'),'','','systemicon').'</a>';
	    }
	  $idx++;
	}
	  
      if( $delasso )
	{
	  $tmp['deletelink'] = "<a href=\"deletecssassoc.php?id=$id&amp;css_id=".$row["assoc_css_id"]."&amp;type=$type\" onclick=\"return confirm('".lang('deleteassociationconfirm', $row["css_name"])."');\">".$themeObject->DisplayImage('icons/system/delete.gif', lang('delete'),'','','systemicon')."</a>";
	}

      $cssassoc[] = $tmp;
    }
  if( count($cssassoc) )
    {
      $smarty->assign('cssassoc',$cssassoc);
    }
	
  # this var is used to store the css ids that should not appear in the
  # dropdown
  $notinto = "";
  
  foreach($csslist as $key)
    {
      $notinto .= "$key,";
    }
  $notinto = substr($notinto, 0, strlen($notinto) - 1);
  
  # this var contains the dropdown
  $dropdown = "";
  $result = '';
  
  # we generate the dropdown
  if ("" == $notinto)
    {
      $query = "SELECT * FROM ".cms_db_prefix()."css ORDER BY css_name";
    }
  else
    {
      $query = "SELECT * FROM ".cms_db_prefix()."css WHERE css_id NOT IN (".$notinto.") ORDER BY css_name";
    }
  $result = $db->Execute($query);
  
  if ($result && $result->RecordCount() > 0)
    {
      $smarty->assign('formstart',"<form action=\"addcssassoc.php\" method=\"post\">");
      $dropdown = "<select name=\"css_id\">\n";
      while ($line = $result->FetchRow())
	{
	  $dropdown .= "<option value=\"".$line["css_id"]."\">".$line["css_name"]."</option>\n";
	}
      $dropdown .= "</select>";
      $smarty->assign('dropdown',$dropdown);

      $hidden = '<input type="hidden" name="id" value="'.$id.'" />';
      $hidden .= '<input type="hidden" name="type" value="'.$type.'" />';
      $smarty->assign('hidden',$hidden);

      $submit = '<input type="submit" value="'.lang('addcss').' class="pagebutton" onmouseover="this.className=\'pagebuttonhover\';" onmouseout="this.className=\'pagebutton\';" />';
      $smarty->assign('submit',$submit);

    } # end of showing form
}

# begin output
echo '<div class="pagecontainer">'.$themeObject->ShowHeader('currentassociations');
echo $smarty->fetch('listcssassoc.tpl');
echo '<p class="pageback"><a class="pageback" href="'.$themeObject->BackUrl().'">&#171; '.lang('back').'</a></p>';
include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
