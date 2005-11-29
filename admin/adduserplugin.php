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

$plugin_name= "";
if (isset($_POST["plugin_name"])) $plugin_name = $_POST["plugin_name"];

$code= "";
if (isset($_POST["code"])) $code = $_POST["code"];

if (isset($_POST["cancel"])) {
	redirect("listusertags.php");
	return;
}

$userid = get_userid();
$access = check_permission($userid, 'Modify Code Blocks');

$use_javasyntax = false;
if (get_preference($userid, 'use_javasyntax') == "1") $use_javasyntax = true;

$smarty = new Smarty_CMS($gCms->config);
load_plugins($smarty);

if ($access) {
	if (isset($_POST["addplugin"])) {

		$validinfo = true;
		if ($plugin_name == "") {
			$error .= "<li>".lang('nofieldgiven',array(lang('name')))."</li>";
			$validinfo = false;
		}
		else
		{
			if (in_array($plugin_name, $gCms->cmsplugins))
			{
				$error .= "<li>".lang('usertagexists')."</li>";
				$validinfo = false;
			}
		}
		if ($code == "") {
			$error .= "<li>".lang('nofieldgiven',array(lang('code')))."</li>";
			$validinfo = false;
		}
		else if (strrpos($code, '{') !== FALSE)
		{
			$lastopenbrace = strrpos($code, '{');
			$lastclosebrace = strrpos($code, '}');
			if ($lastopenbrace > $lastclosebrace)
			{
				$error .= "<li>".lang('invalidcode')."</li>";
				$validinfo = false;
			}
		}
		
		if ($validinfo)
		{
			srand();
			if (@eval('function testfunction'.rand().'() {'.$code.'}') === FALSE)
			{
				$error .= "<li>".lang('invalidcode')."</li>";
				$validinfo = false;
			}
		}

		if ($validinfo) {
			$new_usertag_id = $db->GenID(cms_db_prefix()."userplugins_seq");
			$query = "INSERT INTO ".cms_db_prefix()."userplugins (userplugin_id, userplugin_name, code, create_date, modified_date) VALUES ($new_usertag_id, ".$db->qstr($plugin_name).", ".$db->qstr($code).", '".$db->DBTimeStamp(time())."', '".$db->DBTimeStamp(time())."')";
			$result = $db->Execute($query);
			if ($result) {
				audit($new_usertag_id, $plugin_name, 'Added User Defined Tag');
				redirect("listusertags.php");
				return;
			}
			else {
				$error .= "<li>".lang('errorinsertingtag')."</li>";
			}
		}
	}
}

include_once("header.php");

if (!$access) {
	echo '<div class=\"pageerrorcontainer\"><p class="pageerror">'.lang('noaccessto', array(lang('addusertag'))).'</p></div>';
}
else {
	if ($error != "") {
		echo "<div class=\"pageerrorcontainer\"><ul class=\"error\">".$error."</ul></div>";	
	}
?>

<div class="pagecontainer">
	<p class="pageheader"><?php echo lang("addusertag")?></p>
	<form enctype="multipart/form-data" action="adduserplugin.php" method="post">
		<div class="pageoverflow">
			<p class="pagetext">*<?php echo lang('name')?>:</p>
			<p class="pageinput">
				<input type="text" name="plugin_name" maxlength="255" value="<?php echo $plugin_name?>" />
			</p>
		</div>
		<div class="pageoverflow">
			<p class="pagetext">*<?php echo lang('code')?></p>
			<p class="pageinput"><textarea class="pagetextarea" name="code" rows="" cols=""><?php echo $code ?></textarea></p>
		</div>
		<div class="pageoverflow">
			<p class="pagetext">&nbsp;</p>
			<p class="pageinput">
				<input type="hidden" name="addplugin" value="true" />
				<input type="submit" value="<?php echo lang('submit')?>" class="pagebutton" onmouseover="this.className='pagebuttonhover'" onmouseout="this.className='pagebutton'" />
				<input type="submit" name="cancel" value="<?php echo lang('cancel')?>" class="pagebutton" onmouseover="this.className='pagebuttonhover'" onmouseout="this.className='pagebutton'" />
			</p>
		</div>
	</form>
</div>

<?php
}
echo '<p class="pageback"><a class="pageback" href="'.$themeObject->BackUrl().'">&#171; '.lang('back').'</a></p>';
include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
