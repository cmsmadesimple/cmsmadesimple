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
 * This page is responsible for showing the interface to add a new CSS. There is
 * a form that returns back to this page. The content of the form is then
 * checked to verify that all parameters are valid.
 *
 * - If all parameter are valid, the result is stored in the DB
 * - If one or more parameters are not valid, the form is redisplayed. An error
 *   message indictaes to the user what went wrong.
 *
 * There is no GET parameters.
 *
 * @since	0.6
 * @author  calexico
 */

 
$CMS_ADMIN_PAGE=1;

require_once("../include.php");

check_login();

#******************************************************************************
# global variables definitions
#******************************************************************************

# this variable is used to store an eventual error message.
$error = "";

#******************************************************************************
# we get the content of the form if there are not empty.
#******************************************************************************

# first ; the content of the css
$css_text = "";
if (isset($_POST["css_text"])) $css_text = $_POST["css_text"];

# then its name
$css_name = "";
if (isset($_POST["css_name"])) $css_name = $_POST["css_name"];

#******************************************************************************
# if the form was cancelled, we get back to the CSS list
#******************************************************************************
if (isset($_POST["cancel"]))
{
	redirect("listcss.php");
	return;
}

#******************************************************************************
# we now check that user has access to add CSS
#******************************************************************************
$userid = get_userid();
$access = check_permission($userid, 'Add CSS');

if ($access)
{

#******************************************************************************
# if the var "addcss" is set, this means that the form has been submitted.
# we check if params are valid
#******************************************************************************
	if (isset($_POST["addcss"]))
	{

		# used to check if we will save the form or not
		$validinfo = true;

		# if no CSS name was given
		if ("" == $css_name)
		{
			$error .= "<li>".lang('nofieldgiven', array(lang('name')))."</li>";
			$validinfo = false;
			
		}
		# the CSS has a name, we check if it already exists or not
		else 
		{
			$query = "SELECT css_id from ".cms_db_prefix()."css WHERE css_name = " . $db->qstr($css_name);
			$result = $db->Execute($query);

			if ($result && $result->RowCount() > 0)
			{
				$error .= "<li>".lang('cssalreadyused')."</li>";
				$validinfo = false;
			}
		}

		# now checking the content of the CSS
		if ("" == $css_text)
		{
			$error .= "<li>".lang('nofieldgiven', array(lang('content')))."</li>";
			$validinfo = false;
		}

#******************************************************************************
# everythings seems to be ok, we can try to save the form
#******************************************************************************
		if ($validinfo)
		{
			# this is used to get a unique ID for the CSS
			$new_css_id = $db->GenID(cms_db_prefix()."css_seq");

			# we then generate the request
			$query = "INSERT INTO ".cms_db_prefix()."css (css_id, css_name, css_text, create_date, modified_date) VALUES ('$new_css_id', ".$db->qstr($css_name).", ".$db->qstr($css_text).", ".$db->DBTimeStamp(time()).", ".$db->DBTimeStamp(time()).")";

			# and execute it
			$result = $db->Execute($query);

			# we now have to check that everything went well
			if ($result)
			{
				# it's ok, we record the operation in the admin log
				audit($new_css_id, $css_name, 'Added CSS');

				# and goes back to the css list
				redirect("listcss.php");
				return;
			}
			else
			{
				$error .= "<li>".lang('errorinsertingcss')."</li>";
			}
		}
	}
}

include_once("header.php");

#******************************************************************************
# the user does not have access : error message 
#******************************************************************************
if (!$access)
{
	print "<h3>".lang('noaccessto', array(lang('addcss')))."</h3>";
}
#******************************************************************************
# the user has access, we display the form
#******************************************************************************
else
{

	# the user has correct rights, we display error message if any
	if ("" != $error)
	{
		echo "<ul class=\"error\">".$error."</ul>";
	}

#******************************************************************************
# we now display the content of the form, in HTML
#******************************************************************************
?>

<form method="post" action="addcss.php">

<div class="adminform">

<h3><?php echo lang('addcss')?></h3>

<table width="100%" border="0">

	<tr>
		<td width="100"><?php echo lang('name')?>:</td>
		<td><input type="text" name="css_name" maxlength="25" value="<?php echo $css_name?>"></td>
	</tr>
	<tr>
		<td><?php echo lang('content')?>:</td>
		<td><textarea name="css_text" cols="90" rows="18"><?php echo $css_text?></textarea></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>
			<input type="hidden" name="addcss" value="true">
			<input type="submit" value="<?php echo lang('submit')?>" class="button" onmouseover="this.className='buttonHover'" onmouseout="this.className='button'">
			<input type="submit" name="cancel" value="<?php echo lang('cancel')?>" class="button" onmouseover="this.className='buttonHover'" onmouseout="this.className='button'">
		</td>
	</tr>

</table>

</div>

</form>

<?php

} # end of displaying the form
include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
