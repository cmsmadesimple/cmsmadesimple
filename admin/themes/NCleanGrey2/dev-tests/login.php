<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title><?php echo lang('logintitle')?></title>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo get_encoding() ?>" />
<meta name="robots" content="noindex, nofollow" />
<script src="themes/NCleanGrey/includes/standard.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" media="screen, projection" href="loginstyle.php" />
<base href="<?php global $gCms; $config =& $gCms->GetConfig(); echo $config['root_url'] . '/' . $config['admin_dir'] . '/'; ?>" />
</head>
<body>
<div class="login-all clear">
<div class="info">
  <h1><?php echo lang('login_info_title')?></h1>
  <div class="centerLogin">
  <p id="img"><?php echo lang('login_info')?>:</p>
 <?php echo lang('login_info_params')?>
<span>( <?php echo $_SERVER['HTTP_HOST'];?> )</span>
</div>
</div>
<div class="login">
<div class="top"><?php echo lang('logintitle')?></div>
		<div id="centerLogin" class="formcontainer">
				<?php
						debug_buffer('Debug in the page is: ' . $error);
						if (isset($error) && $error != '')
						{
							echo '<div class="erroLogin">'.$error.'</div>';
						}
					?>
					<div class="lbfieldstext">
					<p class="lbuser"><?php echo lang('username')?>:</p>
					<p class="lbpass"><?php echo lang('password')?>:</p>
					</div>
		            <div class="login-fields">
					<form method="post" action="login.php">
						<p>
							<input id="lbusername" name="username" <?php if(!isset($_POST['username'])) echo 'class="defaultfocus"' ?> type="text" size="15" value="<?php echo htmlentities(isset($_POST['username'])?$_POST['username']:'')?>" /><br />
						<?php if(isset($error) && $error!='') {
						  echo '<input id="lbpassword" class="defaultfocus" name="password" type="password" size="15" /><br />';
						} else {
						  echo '<input id="lbpassword"  name="password" type="password" size="15" /><br />';
						} ?>
							<input class="loginsubmit" name="loginsubmit" type="submit" value="<?php echo lang('submit')?>" /> 
							<input class="loginsubmit" name="logincancel" type="submit" value="<?php echo lang('cancel')?>" />
						</p>
					</form>
				</div>
    </div>
  </div>

</div>
<div class="login-footer"></div>
<div id="copy"> &copy; <a rel="external" href="http://www.cmsmadesimple.org" >CMS Made Simple</a>
<br /> 
Is free software released under the General Public Licence.<br />Theme designed by <a rel="external" href="http://www.criacaoweb.net"><b>Nuno Costa</b></a></div>
</body>
</html>