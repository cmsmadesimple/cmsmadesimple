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

#******************************************************************************
# global vars definition
#******************************************************************************

# this var is used to store any error that may occur.
$error = "";

# this one is used later to store all the css found, because they won't appear in the dropdown
$csslist = array();

$type = "";

#******************************************************************************
# we now get the parameters
#******************************************************************************

# getting variables
if (isset($_GET["type"])) $type	= $_GET["type"] ;
else $error = lang('typenotvalid');

if (isset($_GET["id"]))	$id	= $_GET["id"] ;
else $error = lang('idnotvalid');

# if type is template, we get the name
if (isset($type) && "template" == $type) 
{

	$query = "SELECT template_name FROM ".cms_db_prefix()."templates WHERE template_id = $id";
	$result = $db->Execute($query);

	if ($result)
	{
		$line = $result->FetchRow();
		$name = $line["template_name"];
	}
	else
	{
		$error = lang('errorretrievingtemplate');
	}
}

#******************************************************************************
# displaying erros if any
#******************************************************************************
if (isset($_GET["message"]))
{
    echo "<p class=\"error\">".$_GET["message"]."</p>";
}
if ("" != $error)
{
    echo "<p class=\"error\">$error</p>";
}

#******************************************************************************
# now really starting
#******************************************************************************
?>

<h3><?php echo lang('currentassociations')?> - <?php echo $type?> : <?php echo (isset($name)?$name:"")?></h3>

<?php

#******************************************************************************
# first getting all user permissions
#******************************************************************************
	$userid = get_userid();

	$modify  = check_permission($userid, 'Modify Stylesheet Assoc');
	$delasso = check_permission($userid, 'Remove Stylesheets association');
	$addasso = check_permission($userid, 'Add Stylesheets association');

	$query = "SELECT assoc_css_id, css_name FROM ".cms_db_prefix()."css_assoc, ".cms_db_prefix()."css
		WHERE assoc_type='$type' AND assoc_to_id = '$id' AND assoc_css_id = css_id";
	$result = $db->Execute($query);

	# if any css was found.
	if ($result)
	{
		# table header
		echo '<table cellspacing="0" class="AdminTable" style="width: 400px;">'."\n";
		echo '<thead>';
		echo "<tr>\n";
		echo "<td>".lang('title')."</td>\n";
		echo "<td>&nbsp;</td>\n";
		echo "</tr>\n";
		echo '</thead>';
		echo '<tbody>';

		# this var is used to show each line with different color
		$currow = "row1";

		# now showing each line
		while ($one = $result->FetchRow())
		{

			# we store ids of css found for them not to appear in the dropdown
			array_push($csslist,$one["assoc_css_id"]);
		 
			echo "<tr class=\"$currow\">\n";
			echo "<td>".$one["css_name"]."</td>\n";

			# if user has right to delete
			if ($delasso)
			{
				echo "<td width=\"18\"><a href=\"deletecssassoc.php?id=$id&css_id=".$one["assoc_css_id"]."&type=$type\" onclick=\"return confirm('".lang('deleteconfirm')."');\"><img src=\"../images/cms/delete.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"".('delete')."\" title=\"".('delete')."\" /></a></td>\n";
			}
			else
			{
				echo "<td>&nbsp;</td>";
			}

			echo "</tr>\n";

			("row1" == $currow) ? $currow="row2" : $currow="row1";

		} ## foreach

		echo '</tbody>';
		echo "</table>\n";

	} # end of if result
	

	# if user has right to create assoc
	if ($addasso)
	{

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

		# we generate the dropdown
		if ("" == $notinto)
		{
			$query = "SELECT * FROM ".cms_db_prefix()."css WHERE css_id ORDER BY css_name";
		}
		else
		{
			$query = "SELECT * FROM ".cms_db_prefix()."css WHERE css_id NOT IN ($notinto) ORDER BY css_name";
		}
		$result = $db->Execute($query);

		if ($result && $result->RowCount() > 0)
		{

			$form = "<form action=\"addcssassoc.php\" method=\"post\">";
			
			$dropdown = "<select name=\"css_id\" style=\"width: 250px;\">\n";
			while ($line = $result->FetchRow())
			{
				$dropdown .= "<option value=\"".$line["css_id"]."\">".$line["css_name"]."</option>\n";
			}
			$dropdown .= "</select>";

			echo $form.$dropdown;

?>

<input type="hidden" name="id" value="<?php echo $id?>" />
<input type="hidden" name="type" value="<?php echo $type?>" />
<input type="submit" value="<?php echo lang('addcss')?>" class="button" onmouseover="this.className='buttonHover'" onmouseout="this.className='button'" />
</form>

<?php
		} # end of showing form
	} # end of if has right to add
	else
	{
		echo lang('noaccessto', array(lang('addcssassociation')));
	}

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
