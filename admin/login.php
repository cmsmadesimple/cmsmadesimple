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

	#$query = "SELECT * FROM ".cms_db_prefix()."users WHERE username = ".$db->qstr($username)." and password = ".$db->qstr(md5($password)) . " and active = 1";
	#$result = $db->Execute($query);

	#$line = $result->FetchRow();

	#if ($username != "" && $password != "" && isset($line["user_id"])) {
	if ($username != "" && $password != "" && $oneuser)
	{
		generate_user_object($oneuser->id);
		setcookie("cms_admin_user_id", $oneuser->id);
		audit(-1, '', 'User Login');
		echo ('<html><head><title>Logging in... please wait</title><meta http-equiv="refresh" content="1; url=./index.php"></head><body>Logging in, one moment please...</body></html>');
		return;
		#redirect("index.php");
	}
	else {
		$error .= "<p>".lang('usernameincorrect')."</p>";
	}

}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html40/loose.dtd">
<HEAD>

<TITLE></TITLE>

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
		<TD><INPUT TYPE="password" ID="userdata" NAME="password" SIZE="15" ></TD>
	</TR>
	<TR>
		<TD>&nbsp;</TD>
		<TD><INPUT TYPE="submit" VALUE="<?php echo lang('submit')?>" CLASS="button" onMouseOver="this.className='buttonHover'" onMouseOut="this.className='button'"></TD>
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
