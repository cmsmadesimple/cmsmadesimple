<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>

<title>CMS Admin Login</title>

<link rel="stylesheet" type="text/css" href="loginstyle.php" />

</head>

<body>

<div class="login">

<?php

	if ($error != "") {
		echo "<div class=\"formError\">".$error."</div>";
	}

?>

<form method="post" action="login.php" id="login" name="login" >
<img src="themes/default/cmsloginbanner.gif" alt="CMS Made Simple" width="411" height="114" border="0" align="right" />
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