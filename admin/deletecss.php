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
 * This page is used to delete CSS. It doesn't show any HTML and only does
 * treatments.
 *
 * Only one parameter is required and must be passed by GET :
 * - $css_id : the id of the CSS to delete
 *
 * At the end of treatment, user is redirected to the CSS list
 *
 * @since	0.6
 * @author	calexico
 */


$CMS_ADMIN_PAGE=1;

require_once("../include.php");

check_login();

#******************************************************************************
# Definition of global vars
#******************************************************************************

# this var is used to check if we'll delete or not the css
# it is turned to false if any error is encountered
$dodelete = true;

#******************************************************************************
# start of the treatment
#******************************************************************************
if (isset($_GET["css_id"]))
{

	# we get the params
	$css_id		= $_GET["css_id"];

	# css name will be used for logging
	$css_name	= "";

	$userid		= get_userid();
	$access		= check_permission($userid, 'Remove CSS');

	# checking of users permissions
	if ($access)
	{

		# first we get the name of the css for logging
		$query = "SELECT css_name FROM ".cms_db_prefix()."css WHERE css_id = '$css_id'";
		$result = $db->Execute($query);
		
		if ($result && $result->RowCount())
		{
			$row = $result->FetchRow();
			$css_name = $row['css_name'];
		}
		else
		{
			$dodelete = false;
			$error = $gettext->gettext("Error getting the CSS name");
		}

		# we test on dodelete only to avoid too many queries
		if ($dodelete)
		{
			# then we check if this CSS has associations
			$query = "SELECT * FROM ".cms_db_prefix()."css_assoc WHERE assoc_css_id = '$css_id'";
			$result = $db->Execute($query);
			
			if ($result && $result->RowCount())
			{
				$dodelete = false;
				$error =  $gettext->gettext("This CSS is still used by templates or pages. Please remove those association first!");
			}
		}

		# everything should be ok
		if ($dodelete)
		{
			$query = "DELETE FROM ".cms_db_prefix()."css where css_id = '$css_id'";
			$result = $db->Execute($query);

			if ($result)
			{
				audit($_SESSION["cms_admin_user_id"], $_SESSION["cms_admin_username"], $css_id, $css_name, 'Deleted CSS');
			}
			else
			{
				$dodelete = false;
				$error = $gettext->gettext("Error deleting CSS!");
			}
		} # end of deletion
	} # end of if access

	# there the user does not have access
	else
	{
		$dodelete = false;
		$error = $gettext->gettext("You do not have the right to delete CSS");
	}
} # end of isset params

else
{
	$dodelete = false;
	$error = $gettext->gettext("The CSS ID is not valid!");
}

#******************************************************************************
# end of treatment, we now redirect
#******************************************************************************
if ($dodelete)
{
	redirect("listcss.php");
}
else
{
	redirect("listcss.php?message=$error");
}

# vim:ts=4 sw=4 noet
?>
