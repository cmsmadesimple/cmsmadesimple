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

$CMS_ADMIN_PAGE=1;

require_once("../include.php");
require_once("../lib/classes/class.user.inc.php");

$error = "";

if (isset($_POST["username"]) && isset($_POST["password"])) {

	$username = "";
	if (isset($_POST["username"])) $username = $_POST["username"];

	$password = "";
	if (isset($_POST["password"])) $password = $_POST["password"];

	$oneuser = UserOperations::LoadUserByUsername($username, $password, true, true);

	if ($username != "" && $password != "" && $oneuser && isset($_POST["loginsubmit"]))
	{
		generate_user_object($oneuser->id);
		setcookie("cms_admin_user_id", $oneuser->id);
		$default_cms_lang = get_preference($oneuser->id, 'default_cms_language');
		if ($default_cms_lang != '')
		{
			setcookie('cms_language', $default_cms_lang);
		}
		audit(-1, '', 'User Login');
		echo ('<html><head><title>Logging in... please wait</title><meta http-equiv="refresh" content="1; url=./index.php"></head><body>Logging in and redirecting to <a href="./index.php">index.php</a>, one moment please...</body></html>');
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
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html40/loose.dtd">
<HEAD>

<TITLE>CMS Admin Login</TITLE>

<LINK REL="stylesheet" TYPE="text/css" HREF="style.css" >

</HEAD>

<BODY>

<DIV CLASS="login">

<?php

	if ($error != "") {
		echo "<div class=\"loginerror\">".$error."</div>";
	}

?>

<FORM METHOD="post" ACTION="login.php" ID="login" NAME="login" >
<IMG SRC="../images/cms/cmsloginbanner.png" ALT="CMS Made Simple" WIDTH="411" HEIGHT="114" BORDER="0" ALIGN="right" >
<DIV ID=ctr ALIGN=center>
<IMG SRC="../images/cms/login.png" ALT="CMS Made Simple" WIDTH="64" HEIGHT="64" BORDER="0"  ID="loginbox">
</DIV>
<TABLE BORDER="0" ID="logintable" ALIGN="center">
	<TR>
		<TD ALIGN="right"><?php echo lang('username')?>:</TD>
		<TD><INPUT TYPE="text" ID="userdata" NAME="username" VALUE="<?php echo (isset($_POST["username"])?$_POST["username"]:"")?>" SIZE="15"></TD>
	</TR>
	<TR>
		<TD ALIGN="right"><?php echo lang('password')?>:</TD>
		<TD><INPUT TYPE="password" ID="userdata" NAME="password" VALUE="<?php echo (isset($_POST["password"])?$_POST["password"]:"")?>" SIZE="15" ></TD>
	</TR>
	<TR>
		<TD ALIGN="right"><?php echo lang('language')?>:</TD>
		<TD>
			<SELECT CLASS="smallselect"  NAME="change_cms_lang" onChange="document.login.submit()" STYLE="vertical-align: middle;">
			<?php
				asort($nls["language"]);
				foreach ($nls["language"] as $key=>$val) {
					echo "<option value=\"$key\"";
					if (isset($_POST["change_cms_lang"])) {
						if ($_POST["change_cms_lang"] == $key) {
							echo " selected";
						}
					} else if (isset($_COOKIE["cms_language"])) {
						if ($_COOKIE["cms_language"] == $key) {
							echo " selected";
						}
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
			</SELECT>
		</TD>
	</TR>
	<TR>
		<TD>&nbsp;</TD>
		<TD><INPUT TYPE="submit" NAME="loginsubmit" VALUE="<?php echo lang('submit')?>" CLASS="button" onMouseOver="this.className='buttonHover'" onMouseOut="this.className='button'"></TD>
	</TR>
</TABLE>
</FORM>

<SCRIPT LANGUAGE="javascript">
<!--
	document.login.username.focus();
//-->
</SCRIPT>

</BODY>
</HTML>
<?php
# vim:ts=4 sw=4 noet
?>
