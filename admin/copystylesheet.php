<?php
#CMS - CMS Made Simple
#(c)2004-2008 by Ted Kulp (ted@cmsmadesimple.org)
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

$CMS_ADMIN_PAGE=1;

require_once("../include.php");

check_login();

$error = "";

$stylesheet = "";
if (isset($_POST["stylesheet"])) $stylesheet = $_POST["stylesheet"];

$stylesheet_id = -1;
if (isset($_POST["stylesheet_id"])) $stylesheet_id = $_POST["stylesheet_id"];
else if (isset($_GET["stylesheet_id"])) $stylesheet_id = $_GET["stylesheet_id"];

$stylesheet_name = '';
if (isset($_REQUEST["stylesheet_name"])) { $stylesheet_name = $_REQUEST["stylesheet_name"]; }

if (isset($_POST["cancel"]))
{
	redirect("listcss.php");
	return;
}

$userid = get_userid();
$access = check_permission($userid, 'Add Stylesheets');

if ($access)
{
	if (isset($_POST["copystylesheet"]))
	{
		global $gCms;
		$styleops =& $gCms->GetStylesheetOperations();
		$validinfo = true;
		if ($stylesheet == "")
		{
			$error .= "<li>".lang('nofieldgiven',array(lang('name')))."</li>";
			$validinfo = false;
		}
		else
		{
			if ($styleops->CheckExistingStylesheetName($stylesheet))
			{
				$error .= "<li>".lang('stylesheetexists')."</li>";
				$validinfo = false;
			}
		}

		if ($validinfo)
		{
			$onestylesheet = $styleops->LoadStylesheetByID($stylesheet_id);
			$onestylesheet->id = -1; //Reset id so it will insert a new record
			$onestylesheet->name = $stylesheet; //Change name
			$result = $onestylesheet->Save();

			if ($result)
			{
				audit($onestylesheet->id, $onestylesheet->name, 'Copied Stylesheet');
				redirect("listcss.php");
				return;
			}
			else
			{
				$error .= "<li>".lang('errorcopyingstylesheet')."</li>";
			}
		}

	}
}

include_once("header.php");

if (!$access)
{
	echo "<div class=\"pageerrorcontainer\"><p class=\"pageerror\">".lang('noaccessto',array(lang('copystylesheet')))."</p></div>";
}
else
{
	if ($error != "")
	{
		echo "<div class=\"pageerrorcontainer\"><ul class=\"pageerror\">".$error."</ul></div>";		
	}

?>


<div class="pagecontainer">
	<div class="pageheader"><?php echo lang('copystylesheet')?></div>
	<form method="post" action="copystylesheet.php">
		<div class="row">
			<label><?php echo lang('stylesheet'); ?>:</label>
			<?php echo $stylesheet_name; ?>
		</div>
		<div class="row">
			<label for="new_stylesheet_name"><?php echo lang('newstylesheetname'); ?>:</label>
			<input type="text" name="stylesheet" id="new_stylesheet_name" maxlength="255" value="<?php echo $stylesheet?>">
		</div>
		<input type="hidden" name="stylesheet_id" value="<?php echo $stylesheet_id?>" /><input type="hidden" name="copystylesheet" value="true" />
		<div class="submitrow">
			<button class="positive disabled" name="submitbutton" type="submit" disabled=""><?php echo lang('submit')?></button>
			<button class="negative" name="cancel" type="submit"><?php echo lang('cancel')?></button>
		</div>
	</form>
</div>

<?php

}

echo '<p class="pageback"><a class="pageback" href="listcss.php">&#171; '.lang('back').'</a></p>';

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
