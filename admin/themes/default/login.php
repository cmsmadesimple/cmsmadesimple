<?php echo '<?xml version="1.0" encoding="' . get_encoding() . '"?>'; ?>
<!DOCTYPE html 
     PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>CMS Login</title>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo get_encoding() ?>" />
<link rel="stylesheet" type="text/css" media="screen, projection" href="themes/default/css/style.css" />
<base href="<?php global $gCms; $config =& $gCms->GetConfig(); echo $config['root_url'] . '/' . $config['admin_dir'] . '/'; ?>" />
</head>

<body>
<div class="lball">
	<div class="lblayout lbtopmargin"><p><img src="themes/default/images/logo.gif" alt="" /><span class="logotext"><?php echo lang('logintitle')?></span></p></div>
	<div id="loginbox" class="lblayout lbcontainer">
			<div class="lbinfo">
				<p><?php echo lang('loginprompt')?> <br /><br />
					<?php
						debug_buffer('Debug in the page is: ' . $error);
						if (isset($error) && $error != '')
						{
							echo '<span class="loginerror">'.$error.'</span>';
                        }
					?>
				</p>
			</div>
			<div class="lbfields">
				<div class="lbfieldstext">
					<p class="lbuser"><?php echo lang('username')?>:</p>
					<p class="lbpass"><?php echo lang('password')?>:</p>
				</div>
				<div class="lbinput">
					<form method="post" action="login.php">
						<p>
							<input name="username" type="text" size="15" value="<?php echo (isset($_POST['username'])?$_POST['username']:'')?>" /><br />
							<input class="lbpassword" name="password" type="password" size="15" value="<?php echo (isset($_POST['password'])?$_POST['password']:'')?>" /><br />
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
