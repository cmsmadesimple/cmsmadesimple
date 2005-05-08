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

check_login();

$error = "";

$title = "";
if (isset($_POST["title"])) $title = $_POST["title"];

$url = "";
if (isset($_POST["url"])) $url = $_POST["url"];

$bookmark_id = -1;
if (isset($_POST["bookmark_id"])) $bookmark_id = $_POST["bookmark_id"];
else if (isset($_GET["bookmark_id"])) $bookmark_id = $_GET["bookmark_id"];

if (isset($_POST["cancel"])) {
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
		$markobj = new Bookmark();
		$markobj->bookmark_id = $bookmark_id;
		$markobj->title = $title;
		$markobj->url = $url;
		$markobj->user_id = $userid;

		$result = $markobj->save();

		if ($result)
			{
			redirect("listbookmarks.php");
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
	$query = "SELECT * from ".cms_db_prefix()."admin_bookmarks WHERE bookmark_id = ?";
	$result = $db->Execute($query, array($bookmark_id));
	
	$row = $result->FetchRow();

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
	echo "<ul class=\"error\">".$error."</ul>";
	}
?>

<form method="post" action="editbookmark.php">

<div class="adminformSmall">

<h3><?php echo lang('editbookmark')?></h3>

<table border="0">

	<tr>
		<td align="left"><?php echo lang('title')?>:</td>
		<td><input type="text" name="title" maxlength="255" value="<?php echo $title?>" /></td>
	</tr>
	<tr>
		<td align="left"><?php echo lang('url')?>:</td>
		<td><input type="text" name="url" maxlength="255" value="<?php echo $url?>" /></td>
	<tr>
		<td colspan="2" align="center"><input type="hidden" name="bookmark_id" value="<?php echo $bookmark_id?>" /><input type="hidden" name="editbookmark" value="true" /><input type="hidden" name="userid" value="<?php echo $userid?>" />
		<input type="submit" value="<?php echo lang('submit')?>" class="button" onmouseover="this.className='buttonHover'" onmouseout="this.className='button'" />
		<input type="submit" name="cancel" value="<?php echo lang('cancel')?>" class="button" onmouseover="this.className='buttonHover'" onmouseout="this.className='button'" /></td>
	</tr>

</table>

</div>

</form>

<?php

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
