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
$CMS_TOP_MENU='bookmarks';
$CMS_ADMIN_TITLE='addbookmark';

require_once("../include.php");

check_login();

$error = "";

$title= "";
if (isset($_POST["title"])) $title = $_POST["title"];
$url = "";
if (isset($_POST["url"])) $url = $_POST["url"];

if (isset($_POST["cancel"])) {
	redirect("listbookmarks.php");
	return;
}

$userid = get_userid();

if (isset($_POST["addbookmark"]))
	{
	$validinfo = true;

	if ($title == "")
		{
		$error .= "<li>".lang('nofieldgiven', array('addbookmark'))."</li>";
		$validinfo = false;
		}

	if ($validinfo)
		{
		$markobj = new Bookmark();
		$markobj->title = $title;
		$markobj->url = $url;
		$markobj->user_id=$userid;

		$result = $markobj->save();

		if ($result)
			{
			redirect("listbookmarks.php");
			return;
			}
		else
			{
			$error .= "<li>".lang('errorinsertingbookmark')."</li>";
			}
		}
	}

include_once("header.php");

if ($error != "")
	{
	echo "<ul class=\"error\">".$error."</ul>";
	}
?>

<form method="post" action="addbookmark.php">

<div class="adminformSmall">

<h3><?php echo lang('addbookmark')?></h3>

<table border="0">

	<tr>
		<td>*<?php echo lang('title')?>:</td>
		<td><input type="text" name="title" maxlength="255" value="<?php echo $title?>" class="standard" /></td>
	</tr>
	<tr>
		<td>*<?php echo lang('url')?>:</td>
		<td><input type="text" name="url" maxlength="255" value="<?php echo $url ?>" class="standard" /></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input type="hidden" name="addbookmark" value="true" />
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
