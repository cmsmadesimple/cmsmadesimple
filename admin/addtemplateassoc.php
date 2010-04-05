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

require_once(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'cmsms.api.php');

$urlext='?'.CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];
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
	$access = check_permission($userid, 'Add Stylesheet Assoc')
	  || check_permission($userid,'Modify Stylesheet Assoc');

#******************************************************************************
# the user has permissions, and vars are set, we can go on
#******************************************************************************
	if ($access)
	{
	  global $gCms; $db =& $gCms->GetDb();

          # first check if this association already exists
	  $query = "SELECT * FROM ".cms_db_prefix().
		  "css_assoc WHERE assoc_css_id = ? AND assoc_type = ? AND assoc_to_id = ?";
		$result = $db->Execute($query, array($id, $type, $template_id ));

		if ($result && $result->RecordCount() > 0)
		{
			$error = lang('associationexists');
			$doadd = false;
		}

		# we get the name of the element (for logging)
		if ("template" == $type && $doadd)
		{
			$query = "SELECT css_name FROM ".cms_db_prefix()."css WHERE css_id = ?";
			$result = $db->Execute($query, array($id));
			
			if ($result && $result->RecordCount() > 0)
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

		# get the next access_order
		$query = "SELECT max(assoc_order)+1 FROM ".cms_db_prefix()."css_assoc where assoc_to_id = ?";
		$nextord = $db->GetOne($query,array($template_id));
		if( !$nextord ) $nextord = 1;

		# everything is ok, we can insert the element.
		if ($doadd)
		{
			$time = $db->DBTimeStamp(time());
			$query = "INSERT INTO ".cms_db_prefix()."css_assoc (assoc_to_id,assoc_css_id,assoc_type,create_date,modified_date,assoc_order) VALUES (?, ?, ?, ".$time.", ".$time.",?)";
			$result = $db->Execute($query, array($template_id, $id, $type,$nextord));

			if ($result)
			{
				audit($id, (isset($name)?$name:""), 'Added Stylesheet Association');

				if ("template" == $type)
				{
					$time = $db->DBTimeStamp(time());
					$tplquery = "UPDATE ".cms_db_prefix()."templates SET modified_date = ".$time." WHERE template_id = ?";
					$tplresult = $db->Execute($tplquery, array($template_id));
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
	redirect("templatecss.php".$urlext."&id=$id&type=$type");
}
else
{
	redirect("templatecss.php".$urlext."&id=$id&type=$type&message=$error");
}

# vim:ts=4 sw=4 noet
?>
