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
#$Id: addcssassoc.php 1112 2004-12-30 14:54:53Z wishy $

/**
 * The goal of this page is to create a CSS association. So firts, what is a css
 * association, and how it works.
 *
 * The idea came that CSS should be broken in small pieces, easily maintanable,
 * instead of one big CSS. So, the user created several little CSS pieces :
 * - css_menu
 * - css_header
 * - css_footer
 * - css_whatever
 *
 * There is a table (css_assoc) on the DB with the following fields :
 * - assoc_to_id	: the id of the element we associate the CSS with
 *					  it can be a template id, a page id, a what you want id
 * - assoc_css_id	: the id of the CSS we link
 * - assoc_type		: what do we link the CSS to ? for the moment, only template
 * - create_date	: the create date of this association
 * - modified_date	: the modified date of this association (which is not used
 *					  at the moment.
 *
 * This page takes arguments as GET variables. 3 arguments are necessary :
 * - $id		: refers to "assoc_to_id", the id of the element
 * - $css_id	: the id of the CSS we link, refers to "assoc_css_id"
 * - $type		: the type of element $id refers to (only template for the
 *				  moment)
 *
 * @since	0.6
 * @author	calexico
 */

	
$CMS_ADMIN_PAGE=1;

require_once("../include.php");

check_login();

#******************************************************************************
# global variables definition
#******************************************************************************

# variable to check if we'll make the association or not
# will be set to false if an error is encountered
$doadd = true;

#******************************************************************************
# start of the treatment
#******************************************************************************
if (isset($_POST["template_id"]) && isset($_POST["id"]) && isset($_POST["type"]))
{

	# we get the arguments as local vars (easier)
	$template_id = $_POST["template_id"];
	$id		= $_POST["id"];
	$type	= $_POST["type"];

	# we then check permissions
	$userid = get_userid();
	$access = check_permission($userid, 'Add Stylesheets association');

#******************************************************************************
# the user has permissions, and vars are set, we can go on
#******************************************************************************
	if ($access)
	{

		# first check if this association already exists
		$query = "SELECT * FROM ".cms_db_prefix()."css_assoc WHERE assoc_css_id = '$id' AND assoc_type = '$type' AND assoc_to_id = '$template_id'";
		$result = $db->Execute($query);

		if ($result && $result->RowCount() > 0)
		{
			$error = lang('associationexists');
			$doadd = false;
		}

		# we get the name of the element (for logging)
		if ("template" == $type && $doadd)
		{
			$query = "SELECT css_name FROM ".cms_db_prefix()."css WHERE css_id = '$id'";
			$result = $db->Execute($query);
			
			if ($result && $result->RowCount() > 0)
			{
				$line = $result->FetchRow();
				$name = $line["css_name"];
			}
			else
			{
				$doadd = false;
				$error = lang('invalidtemplate');
			}
		}

		# everything is ok, we can insert the element.
		if ($doadd)
		{
			$query = "INSERT INTO ".cms_db_prefix()."css_assoc (assoc_to_id,assoc_css_id,assoc_type,create_date,modified_date)
				VALUES ('$template_id','$id','$type','".$db->DBTimeStamp(time())."','".$db->DBTimeStamp(time())."')";
			$result = $db->Execute($query);

			if ($result)
			{
				audit($id, (isset($name)?$name:""), 'Added CSS association');

				if ("template" == $type)
				{
					$tplquery = "UPDATE ".cms_db_prefix()."templates SET modified_date = '".$db->DBTimeStamp(time())."' 
						WHERE template_id = '$template_id'";
					$tplresult = $db->Execute($tplquery);
				}
			}
			else
			{
				$doadd = false;
				$error = lang('errorcreatingassociation');
			}
		} # enf od adding query to db
	} # end of "if has access"
	
	# user does not have the right to create association
	else
	{
		$doadd = false;
		$error = lang('noaccessto', array(lang('addcss')));
	}
} # end if vars are set
else
{
	$doadd = false;
	$error = lang('informationmissing');
}

#******************************************************************************
# end of treatment, we redirect
#******************************************************************************
if ($doadd)
{
	redirect("templatecss.php?id=$id&type=$type");
}
else
{
	redirect("templatecss.php?id=$id&type=$type&message=$error");
}

# vim:ts=4 sw=4 noet
?>
