<?php
#NCleanGrey Theme - Theme for CMS Made Simple admin side.
#(c)2008/10 by Author: Nuno Costa - nuno.mfcosta@sapo.pt  /  criacaoweb.net / http://dev.cmsmadesimple.org/users/nuno/
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
#$Id: login.php 5517 2009-04-02 16:42:15Z nuno $
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title><?php echo lang('logintitle')?></title>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo get_encoding() ?>" />
<meta name="robots" content="noindex, nofollow" />
<link rel="shortcut icon" href="themes/NCleanGrey/images/layout/ncleangrey-favicon.ico" />
<link rel="Bookmark" href="themes/NCleanGrey/images/layout/ncleangrey-favicon.ico" />
<script src="themes/NCleanGrey/includes/standard.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" media="screen, projection" href="loginstyle.php" />
<base href="<?php $config = cmsms()->GetConfig(); echo $config['admin_url'].'/'; ?>" />
</head>
<body class="body-login">
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
						else if (isset($warningLogin) && $warningLogin != '')
						{
							echo '<div class="warningLogin">'.$warningLogin.'</div>';
						}
						else if (isset($acceptLogin) && $acceptLogin != '')
						{
							echo '<div class="acceptLogin">'.$acceptLogin.'</div>';
						}
					?>
					<?php if ($changepwhash != '') {
							echo '<div class="warningLogin">'.lang('passwordchange').'</div>';
					?>
						<div class="lbfieldstext">
							<p class="lbpass"><?php echo lang('password')?>:</p>
							<p class="lbpass"><?php echo lang('passwordagain')?>:</p>
						</div>
						<div class="login-fields">
							<form method="post" action="login.php">
								<input id="lbpassword"  name="password" type="password" size="15" /><br />
								<input id="lbpasswordagain"  name="passwordagain" type="password" size="15" /><br />
								<input type="hidden" name="changepwhash" value="<?php echo $changepwhash ?>" />
								<input type="hidden" name="forgotpwchangeform" value="1" />
								<input class="loginsubmit" name="loginsubmit" type="submit" value="<?php echo lang('submit')?>" /> 
								<input class="loginsubmit" name="logincancel" type="submit" value="<?php echo lang('cancel')?>" />
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
								<input class="loginsubmit" name="loginsubmit" type="submit" value="<?php echo lang('submit')?>" /> 
								<input class="loginsubmit" name="logincancel" type="submit" value="<?php echo lang('cancel')?>" />
							</form>
						</div>
					<?php } else { ?>
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
					<div class="forgot-pw">
						<a href="login.php?forgotpw=1"><?php echo lang('lostpw')?></a>
					</div>
				</div>
				<?php } ?>
    </div>
  </div>

</div>
<div class="login-footer"></div>
<div id="copy"> &copy; <a rel="external" href="http://www.cmsmadesimple.org" >CMS Made Simple</a>&trade;</div>
</body>
</html>
