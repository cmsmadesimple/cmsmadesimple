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
require_once("../lib/classes/class.user.inc.php");

$error = "";

if (isset($_POST["logincancel"]))
{
	redirect($config["root_url"].'/index.php', true);
}

if (isset($_POST["username"]) && isset($_POST["password"])) {

	$username = "";
	if (isset($_POST["username"])) $username = $_POST["username"];

	$password = "";
	if (isset($_POST["password"])) $password = $_POST["password"];

	$oneuser = UserOperations::LoadUserByUsername($username, $password, true, true);

	if ($username != "" && $password != "" && $oneuser && isset($_POST["loginsubmit"]))
	{
		generate_user_object($oneuser->id);
		$default_cms_lang = get_preference($oneuser->id, 'default_cms_language');
		if ($default_cms_lang != '')
		{
			setcookie('cms_language', $default_cms_lang);
		}
		audit(-1, '', 'User Login');

		#Perform the login_post callback
		foreach($gCms->modules as $key=>$value)
		{
			if ($gCms->modules[$key]['installed'] == true &&
				$gCms->modules[$key]['active'] == true)
			{
				$gCms->modules[$key]['object']->LoginPost($oneuser);
			}
		}

		if (isset($_SESSION["redirect_url"]))
		{
			if (isset($gCms->config) and $gCms->config['debug'] == true)
			{
				echo "Debug is on.  Redirecting disabled...  Please click this link to continue.<br />";
				echo "<a href=\"".$_SESSION["redirect_url"]."\">".$_SESSION["redirect_url"]."</a><br />";
				global $sql_queries;
				if (isset($sql_queries))
				{
					echo $sql_queries;
				}
			}
			else
			{
				echo ('<html><head><title>Logging in... please wait</title><meta http-equiv="refresh" content="1; url='.$_SESSION["redirect_url"].'"></head><body>Logging in and redirecting to <a href="'.$_SESSION["redirect_url"].'">'.$_SESSION["redirect_url"].'</a>, one moment please...</body></html>');
			}
			unset($_SESSION["redirect_url"]);
		}
		else
		{
			if (isset($config) and $config['debug'] == true)
			{
				echo "Debug is on.  Redirecting disabled...  Please click this link to continue.<br />";
				echo "<a href=\"index.php\">index.php</a><br />";
				global $sql_queries;
				if (isset($sql_queries))
				{
					echo $sql_queries;
				}
			}
			else
			{
				echo ('<html><head><title>Logging in... please wait</title><meta http-equiv="refresh" content="1; url=./index.php"></head><body>Logging in and redirecting to <a href="./index.php">index.php</a>, one moment please...</body></html>');
			}
		}
		return;
		#redirect("index.php");
	}
	else if (isset($_POST["loginsubmit"])) { //No error if changing languages
		$error .= "<p>".lang('usernameincorrect')."</p>";
	}

}

// Language shizzle
//header("Content-Encoding: " . get_encoding());
header("Content-Language: " . $current_language);
header("Content-Type: text/html; charset=" . get_encoding());

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>

<title>CMS Admin Login</title>

<link rel="stylesheet" type="text/css" href="style.css" />

</head>

<body>

<div class="login">

<?php

	if ($error != "") {
		echo "<div class=\"formError\">".$error."</div>";
	}

?>

<form method="post" action="login.php" id="login" name="login" >
<img src="../images/cms/cmsloginbanner.gif" alt="CMS Made Simple" width="411" height="114" border="0" align="right" />
<div id="ctr" align="center">
<img src="../images/cms/login.gif" alt="CMS Made Simple" width="64" height="64" border="0" id="loginbox" />
</div>
<table border="0" id="logintable" align="center">
	<tr>
		<td align="right"><?php echo lang('username')?>:</td>
		<td><input type="text" id="userdata" name="username" value="<?php echo (isset($_POST["username"])?$_POST["username"]:"")?>" size="15" /></td>
	</tr>
	<tr>
		<td align="right"><?php echo lang('password')?>:</td>
		<td><input type="password" id="userdata_p" name="password" value="<?php echo (isset($_POST["password"])?$_POST["password"]:"")?>" size="15" /></td>
	</tr>
	<tr>
		<td align="right"><?php echo lang('language')?>:</td>
		<td>
			<select class="smallselect"  name="change_cms_lang" onchange="document.login.submit()" style="vertical-align: middle;">
			<?php
				asort($nls["language"]);
				foreach ($nls["language"] as $key=>$val) {
					echo "<option value=\"$key\"";
					if (isset($_POST["change_cms_lang"])) {
						if ($_POST["change_cms_lang"] == $key) {
							echo " selected=\"selected\"";
						}
					}else if(isset($_COOKIE["cms_language"])) {
						if ($_COOKIE["cms_language"] == $key) {
							echo " selected=\"selected\"";
						}
					}else if($key == 'en_US'){//no language is set defaults to english.
						echo ' selected="selected"';
					}
					echo ">$val";
					/*
					if (isset($nls["englishlang"][$key]))
					{
						echo " (".$nls["englishlang"][$key].")";
					}
					*/
					echo "</option>\n";
				}
			?>
			</select>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>
			<input type="submit" name="loginsubmit" value="<?php echo lang('submit')?>" class="button" onmouseover="this.className='buttonHover'" onmouseout="this.className='button'" />&nbsp;
			<input type="submit" name="logincancel" value="<?php echo lang('cancel')?>" class="button" onmouseover="this.className='buttonHover'" onmouseout="this.className='button'" />
		</td>
	</tr>
</table>
</form>

<script type="text/javascript">
<!--
	document.login.username.focus();
//-->
</script>
</div>
</body>
</html>
<?php
	if (isset($gCms->config) and $gCms->config['debug'] == true)
	{
		global $sql_queries;
		if (isset($sql_queries))
		{
			echo $sql_queries;
		}
	}
# vim:ts=4 sw=4 noet
?>
