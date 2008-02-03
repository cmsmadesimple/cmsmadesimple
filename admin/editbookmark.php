<?php // -*- mode:php; tab-width:4; indent-tabs-mode:t; c-basic-offset:4; -*-
#CMS - CMS Made Simple
#(c)2004-2008 by Ted Kulp (ted@cmsmadesimple.org)
#This project's homepage is: http://cmsmadesimple.org
#
#This program is free software; you can redistribute it and/or modify
#it under the terms of the GNU General Public License as published by
#the Free Software Foundation; either version 2 of the License, or
#(at your option) any later version.
#
#This program is distributed in the hope that it will be useful,
#BUT withOUT ANY WARRANTY; without even the implied warranty of
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
global $gCms;
$db =& $gCms->GetDb();

$error = "";

$title = "";
if (isset($_POST["title"])) $title = $_POST["title"];

$url = "";
if (isset($_POST["url"])) $url = $_POST["url"];

$bookmark_id = -1;
if (isset($_POST["bookmark_id"])) $bookmark_id = $_POST["bookmark_id"];
else if (isset($_GET["bookmark_id"])) $bookmark_id = $_GET["bookmark_id"];

if (isset($_POST["cancel"]))
{
	redirect("listbookmarks.php");
	return;
}

$userid = get_userid();

if (isset($_POST["editbookmark"]))
{
	$validinfo = true;
	if ($title == "")
	{
		$validinfo = false;
		$error .= "<li>".lang('nofieldgiven', array(lang('title')))."</li>";
	}
	if ($url == "")
	{
		$validinfo = false;
		$error .= "<li>".lang('nofieldgiven', array(lang('url')))."</li>";
	}

	if ($validinfo)
	{
		$markobj = cmsms()->bookmark->find_by_id($bookmark_id);
		$markobj->title = $title;
		$markobj->url = $url;
		$markobj->user_id = $userid;

		$result = $markobj->save();

		if ($result)
		{
			CmsResponse::redirect("listbookmarks.php");
			return;
		}
		else
		{
			$error .= "<li>".lang('errorupdatingbookmark')."</li>";
		}
	}

}
else if ($bookmark_id != -1)
{
	$row = cmsms()->bookmark->find_by_id($bookmark_id);

	$url = $row["url"];
	$title = $row["title"];
}

if (strlen($title) > 0)
{
	$CMS_ADMIN_SUBTITLE = $title;
}

include_once("header.php");

if ($error != "")
{
	echo '<div class="pageerrorcontainer"><p class="pageerror">'.$error.'</p></div>';
}
?>

	<?php echo $themeObject->ShowHeader('editbookmark'); ?>
	<form method="post" action="editbookmark.php">
		<div class="row">
			<label><?php echo lang('title')?>:</label>
			<input type="text" name="title" maxlength="255" value="<?php echo $title?>" />
		</div>
		<div class="row">
			<label><?php echo lang('url')?>:</label>
			<input type="text" name="url" maxlength="255" value="<?php echo $url ?>" />
		</div>
		<input type="hidden" name="bookmark_id" value="<?php echo $bookmark_id?>" /><input type="hidden" name="editbookmark" value="true" /><input type="hidden" name="userid" value="<?php echo $userid?>" />
		<div class="submitrow">
			<button class="positive disabled" name="submitbutton" type="submit" disabled=""><?php echo lang('submit')?></button>
			<button class="negative" name="cancel" type="submit"><?php echo lang('cancel')?></button>
		</div>
	</form>
<?php

echo '<p class="pageback"><a class="pageback" href="'.$themeObject->BackUrl().'">&#171; '.lang('back').'</a></p>';
include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
