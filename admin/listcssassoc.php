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
if (isset($_GET["type"])) $type	= $_GET["type"] ;
else $error = lang('typenotvalid');

if (isset($_GET["id"]))	$id	= $_GET["id"] ;
else $error = lang('idnotvalid');

$db = cms_db();

# if type is template, we get the name
if (isset($type) && "template" == $type) 
{

	$query = "SELECT template_name FROM ".cms_db_prefix()."templates WHERE id = ?";
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

	$query = "SELECT assoc_css_id, css_name FROM ".cms_db_prefix()."css_assoc ca INNER JOIN ".cms_db_prefix()."css ON assoc_css_id = id WHERE assoc_type=? AND assoc_to_id = ? ORDER BY ca.create_date";
	$result = $db->Execute($query, array($type, $id));

#******************************************************************************
# displaying erros if any
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
?>

<div class="pagecontainer">
	<?php echo $themeObject->ShowHeader('currentassociations'); ?>
		<div class="pageoverflow">
			<p class="pagetext"><?php echo lang('template')?> :</p>
		        <p class="pageinput"><?php echo '<a href="edittemplate.php?template_id='.$_GET['id'].'"  name="edittemplate">'.(isset($name)?$name:"").'</a>';?></p>
		</div>

<?php

	# if any css was found.
	if ($result && $result->RecordCount() > 0)
	{
		echo "<table cellspacing=\"0\" class=\"pagetable\">\n";
		echo '<thead>';
		echo "<tr>\n";
		echo "<th>".lang('title')."</th>\n";
		echo "<th class=\"pageicon\">&nbsp;</th>\n";
		echo "</tr>\n";
		echo '</thead>';
		echo '<tbody>';

		# this var is used to show each line with different color
		$currow = "row1";

		# now showing each line
		while ($one = $result->FetchRow())
		{

			# we store ids of css found for them not to appear in the dropdown
			$csslist[] = $one["assoc_css_id"];
		 
			echo "<tr class=\"$currow\" onmouseover=\"this.className='".$currow.'hover'."';\" onmouseout=\"this.className='".$currow."';\">\n";		 
			echo "<td><a href=\"editcss.php?css_id=".$one["assoc_css_id"]."&amp;from=templatecssassoc&amp;templateid=".$id."\">".$one["css_name"]."</a></td>\n";

			# if user has right to delete
			if ($delasso)
			{
				echo "<td><a href=\"deletecssassoc.php?id=$id&amp;css_id=".$one["assoc_css_id"]."&amp;type=$type\" onclick=\"return confirm('".lang('deleteassociationconfirm', $one["css_name"])."');\">";
                echo $themeObject->DisplayImage('icons/system/delete.gif', lang('delete'),'','','systemicon');
                echo "</a></td>\n";
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
		$query = "SELECT * FROM ".cms_db_prefix()."css WHERE id NOT IN (".$notinto.") ORDER BY css_name";
	}
	$result = $db->Execute($query);

	if ($result && $result->RecordCount() > 0)
	{
		$form = "<form action=\"addcssassoc.php\" method=\"post\">";
	
		$dropdown = "<select name=\"css_id\">\n";
		while ($line = $result->FetchRow())
		{
			$dropdown .= "<option value=\"".$line["id"]."\">".$line["css_name"]."</option>\n";
		}
		$dropdown .= "</select>";

		echo $form.'<div class="pageoverflow"><p class="pageoptions">'.$dropdown.' ';
?>
		<input type="hidden" name="id" value="<?php echo $id?>" />
		<input type="hidden" name="type" value="<?php echo $type?>" />
		<input type="submit" value="<?php echo lang('addcss')?>" class="pagebutton" onmouseover="this.className='pagebuttonhover';" onmouseout="this.className='pagebutton';" />
		</p>
		</div>
		</form>

<?php
		} # end of showing form
	echo '</div>';
}
echo '<p class="pageback"><a class="pageback" href="'.$themeObject->BackUrl().'">&#171; '.lang('back').'</a></p>';

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
