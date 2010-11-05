<?php echo '<?xml version="1.0" encoding="' . get_encoding() . '"?>'; ?>
<!DOCTYPE html 
     PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>CMS Login</title>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo get_encoding() ?>" />
<script src="themes/default/includes/standard.js" type="text/javascript"></script> 

<link rel="stylesheet" type="text/css" media="screen, projection" href="loginstyle.php" />
<base href="<?php global $gCms; $config = $gCms->GetConfig(); echo $config['root_url'] . '/' . $config['admin_dir'] . '/'; ?>" />
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
						
					    else if (isset($warningLogin) && $warningLogin != '')
						{
							echo '<span class="warningLogin">'.$warningLogin.'</span>';
						}
						else if (isset($acceptLogin) && $acceptLogin != '')
						{
							echo '<span class="acceptLogin">'.$acceptLogin.'</span>';
						}
					?>
				</p>
			</div>
			
			<div class="lbfields">
			<?php if ($changepwhash != '') {
							echo '<div class="warningLogin">'.lang('passwordchange').'</div>';
					?>
						<div class="lbfieldstext">
							<p class="lbpass"><?php echo lang('password')?>:</p>
							<p class="lbpass"><?php echo lang('passwordagain')?>:</p>
						</div>
						<div class="login-fields">
							<form method="post" action="login.php">
								<p class="lbpass"><input id="lbpassword"  name="password" type="password" size="15" /></p>
								<p class="lbpass"><input id="lbpasswordagain"  name="passwordagain" type="password" size="15" /></p>
								<input type="hidden" name="changepwhash" value="<?php echo $changepwhash ?>" />
								<input type="hidden" name="forgotpwchangeform" value="1" />
								<input class="lbsubmit" name="loginsubmit" type="submit" value="<?php echo lang('submit')?>" /> 
								<input class="lbsubmit" name="logincancel" type="submit" value="<?php echo lang('cancel')?>" />
							</form>
						</div>
					<?php } else if (isset($_REQUEST['forgotpw']) && $_REQUEST['forgotpw']) { ?>
						<p class="lbfieldstart"><?php echo lang('forgotpwprompt')?></p>
						<div class="lbfieldstext">
							<p class="lbuser"><?php echo lang('username')?>:</p>
						</div>
						<div class="login-fields">
							<form method="post" action="login.php">
								<input id="lbusername" name="forgottenusername" <?php if(!isset($_POST['username'])) echo 'class="defaultfocus"' ?> type="text" size="15" value="" /><br />
								<input type="hidden" name="forgotpwform" value="1" />
								<input class="lbsubmit" name="loginsubmit" type="submit" value="<?php echo lang('submit')?>" /> 
								<input class="lbsubmit" name="logincancel" type="submit" value="<?php echo lang('cancel')?>" />
							</form>
						</div>
					<?php } else { ?>
				<div class="lbfieldstext">
					<p class="lbuser"><?php echo lang('username')?>:</p>
					<p class="lbpass"><?php echo lang('password')?>:</p>
				</div>
				<div class="lbinput">
					<form method="post" action="login.php">
						<p>
							<input name="username" <?php if(!isset($_POST['username'])) echo 'class="defaultfocus"' ?> type="text" size="15" value="<?php echo htmlentities(isset($_POST['username'])?$_POST['username']:'')?>" /><br />
						<?php if(isset($error) && $error!='') {
						  echo '<input class="lbpassword defaultfocus" name="password" type="password" size="15" /><br />';
						} else {
						  echo '<input class="lbpassword" name="password" type="password" size="15" /><br />';
						} ?>
							<input class="lbsubmit" name="loginsubmit" type="submit" value="<?php echo lang('submit')?>" /> 
							<input class="lbsubmit" name="logincancel" type="submit" value="<?php echo lang('cancel')?>" />
						</p>
					</form>
						<div class="forgot-pw">
						<a href="login.php?forgotpw=1"><?php echo lang('lostpw')?></a>
					</div>
				</div>
				<?php } ?>
			</div>	
	</div>
	<div class="lblayout footer"><a class="footer" href="http://www.cmsmadesimple.org">CMS Made Simple</a> is free software released under the General Public Licence.</div>
</div>	
</body>
</html>
