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

/**
 * This page is in charge of listing all CSS, and giving acces to editing and
 * deleting.
 *
 * There is no particular argument.
 *
 * @since	0.6
 * @author	calexico
 */



$CMS_ADMIN_PAGE=1;

require_once("../include.php");

check_login();

include_once("header.php");

#******************************************************************************
# first : displaying error message, if any.
#******************************************************************************
if (isset($_GET["message"]))
{
    echo "<p class=\"error\">".$_GET["message"]."</p>";
}

?>

<h3><?php echo lang('currentcss')?></h3>

<?php

#******************************************************************************
# first getting all permission : we only display elements the user has access
# too
#******************************************************************************
	$userid = get_userid();

	$modify = check_permission($userid, 'Modify CSS');
	$addcss = check_permission($userid, 'Add CSS');
	$delcss = check_permission($userid, 'Remove CSS');

	$query = "SELECT * FROM ".cms_db_prefix()."css ORDER BY css_name";
	$result = $db->Execute($query);

	if ($result)
	{
		# displaying the table header
		echo '<table cellspacing="0" class="admintable">'."\n";
		echo "<tr>\n";
		echo "<td>".lang('title')."</td>\n";
		echo "<td>&nbsp;</td>\n";
		echo "<td>&nbsp;</td>\n";
		echo "</tr>\n";

		# this var is used to show each line with different color
		$currow = "row1";

		# we now show each line
		while ($one = $result->FetchRow())
		{
			echo "<tr class=\"$currow\">\n";
			echo "<td>".$one["css_name"]."</td>\n";

			# if user has right to edit
			if ($modify)
			{
				echo "<td width=\"18\"><a href=\"editcss.php?css_id=".$one["css_id"]."\"><img src=\"../images/cms/edit.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"".lang('edit')."\" title=\"".lang('edit')."\"></a></td>\n";
			}
			else
			{
				echo "<td>&nbsp;</td>";
			}

			# if user has right to delete
			if ($delcss)
			{
				echo "<td width=\"18\"><a href=\"deletecss.php?css_id=".$one["css_id"]."\" onclick=\"return confirm('".lang('deleteconfirm')."');\"><img src=\"../images/cms/delete.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"".lang('delete')."\" title=\"".lang('delete')."\"></a></td>\n";
			}
			else
			{
				echo "<td>&nbsp;</td>";
			}

			echo "</tr>\n";

			("row1" == $currow) ? $currow="row2" : $currow="row1";

		} ## foreach

		echo "</table>\n";

	} # end if result
	else
	{
		echo "<p>".lang('nocss')."</p>";
	}

	# if user can add css
	if ($addcss)
	{
?>

<div class="button"><a href="addcss.php"><?php echo lang('addcss')?></a></div>

<?php
	} # end if add css


include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
