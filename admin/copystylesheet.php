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

$CMS_ADMIN_PAGE=1;

require_once("../include.php");
$urlext='?'.CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];

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
	redirect("listcss.php".$urlext);
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
				redirect("listcss.php".$urlext);
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
	<p class="pageheader"><?php echo lang('copystylesheet')?></p>
	<form method="post" action="copystylesheet.php">
        <div>
          <input type="hidden" name="<?php echo CMS_SECURE_PARAM_NAME ?>" value="<?php echo $_SESSION[CMS_USER_KEY] ?>" />
        </div>
		<div class="pageoverflow">
			<p class="pagetext"><?php echo lang('stylesheet'); ?>:</p>
			<p class="pageinput"><?php echo $stylesheet_name; ?></p>
		</div>
		<div class="pageoverflow">
			<p class="pagetext"><?php echo lang('newstylesheetname'); ?>:</p>
			<p class="pageinput"><input type="text" name="stylesheet" maxlength="255" value="<?php echo $stylesheet?>" /></p>
		</div>
		<div class="pageoverflow">
			<p class="pagetext">&nbsp;</p>
			<p class="pageinput">
				<input type="hidden" name="stylesheet_id" value="<?php echo $stylesheet_id?>" /><input type="hidden" name="copystylesheet" value="true" />
				<input type="submit" value="<?php echo lang('submit')?>" class="pagebutton" onmouseover="this.className='pagebuttonhover'" onmouseout="this.className='pagebutton'" />
				<input type="submit" name="cancel" value="<?php echo lang('cancel')?>" class="pagebutton" onmouseover="this.className='pagebuttonhover'" onmouseout="this.className='pagebutton'" />
			</p>
		</div>
	</form>
</div>

<?php

}

echo '<p class="pageback"><a class="pageback" href="listcss.php'.$urlext.'">&#171; '.lang('back').'</a></p>';

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
