<?php echo '<?xml version="1.0" encoding="UTF-8"?>'."\n" ?>
<!DOCTYPE html 
     PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>CMS Login</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel="stylesheet" type="text/css" media="screen, projection" href="themes/modern/css/style.css" />
</head>

<body>
<div class="ta-c">
	<div class="lblayout lbtopmargin"><p><img src="themes/modern/images/logo.gif" alt="" /><span class="logotext">CMS Admin Login</span></p></div>
	<div class="lblayout lbcontainer">
			<div class="lbinfo">
				<p>
					Enter a valid user credential to get access to the administration console. <br /><br />
					<?php
						if ($error != "") {
							echo '<span class="loginerror">'.$error.'</span>';
						}
					?>
				</p>
			</div>
			<div class="lbfields">
				<div class="floatl">
					<p class="lbuser"><?php echo lang('username')?>:</p>
					<p class="lbpass"><?php echo lang('password')?>:</p>
				</div>
				<div class="lbinput">
					<form method="post" action="login.php">
						<p>
							<input name="username" type="text" size="15" value="<?php echo (isset($_POST['username'])?$_POST['username']:'')?>" /><br />
							<input class="mt5" name="password" type="password" size="15" value="<?php echo (isset($_POST['password'])?$_POST['password']:'')?>" /><br />
							<input class="lbsubmit" name="loginsubmit" type="submit" value="<?php echo lang('submit')?>" /> 
							<input class="lbsubmit" name="logincancel" type="submit" value="<?php echo lang('cancel')?>" />
						</p>
					</form>
				</div>
			</div>
	</div>
	<div class="lblayout footer"><a class="footer" href="http://www.cmsmadesimple.org">CMS Made Simple</a> is free software released under the General Public Licence.</div>
</div>	
</body>
</html>