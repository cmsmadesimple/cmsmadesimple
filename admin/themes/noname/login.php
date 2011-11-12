<?php
#-------------------------------------------------------------------------
# Noname set Theme - An admin theme for CMS Made Simple
# (c)2011 by Author: Goran Ilic - ja@ich-mach-das.at  /  www.ich-mach-das.at / http://dev.cmsmadesimple.org/users/uniqu3/
#
#-------------------------------------------------------------------------
# CMS - CMS Made Simple is (c) 2005 by Ted Kulp (wishy@cmsmadesimple.org)
# This project's homepage is: http://www.cmsmadesimple.org
#
#-------------------------------------------------------------------------
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# However, as a special exception to the GPL, this software is distributed
# as an addon module to CMS Made Simple.  You may not use this software
# in any Non GPL version of CMS Made simple, or in any version of CMS
# Made simple that does not indicate clearly and obviously in its admin
# section that the site was built with CMS Made simple.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
# Or read it online: http://www.gnu.org/licenses/licenses.html#GPL
#
#-------------------------------------------------------------------------
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
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
<base href="<?php $config = cmsms() -> GetConfig();
	echo $config['admin_url'] . '/';
 ?>" />
</head>
<body class="login">
	<div class="top cf">
		<a class="logo" href="http://www.cmsmadesimple.org" title="CMS Made Simple" rel="external">
			<img src="themes/noname/images/layout/cmsms-login-logo.png" width="146" height="45" alt="CMS Made Simple" />
		</a>
		<h1><?php echo lang('logintitle')?> </h1>
		<a class="home" href="<?php $config = cmsms() -> GetConfig(); echo $config['root_url'];?>" title="<?php echo lang('goto'); $sitename = get_site_preference('sitename'); echo "&nbsp;". $sitename;?>">
			<?php $config = cmsms() -> GetConfig();	echo $config['root_url'];?></a>
	</div>
	<div class="bottom">
		<div class="container">
			<div class="info">				
				<h2><?php echo lang('login_info_title')?></h2> 
					<p class="login-info"><?php echo lang('login_info')?>:</p>
						<?php echo lang('login_info_params')?>
						<strong>( <?php echo $_SERVER['HTTP_HOST'];?> )</strong>
			</div>
			
		<?php
		debug_buffer('Debug in the page is: ' . $error);
		if (isset($error) && $error != '') {
			echo '<div class="error message"><p>' . $error . '</p></div>';
		} else if (isset($warningLogin) && $warningLogin != '') {
			echo '<div class="warning message"><p>' . $warningLogin . '</p></div>';
		} else if (isset($acceptLogin) && $acceptLogin != '') {
			echo '<div class="success message"><p>' . $acceptLogin . '</p></div>';
		}
		?>	
				
		<?php if ($changepwhash != '') {?>
				<div class="warning message">
					<p><?php echo lang('passwordchange')?></p>
				</div>

				<form method="post" action="login.php">
					<fieldset>
						<p class="left"><label for="lbpassword"><?php echo lang('password')?></label>
						<input id="lbpassword"  name="password" type="password" size="15" placeholder="<?php echo lang('password')?>" /></p>
						<p class="right"><label for="lbpasswordagain"><?php echo lang('passwordagain')?></label>
						<input id="lbpasswordagain"  name="passwordagain" type="password" size="15" placeholder="<?php echo lang('passwordagain')?>" /></p>
						<input type="hidden" name="changepwhash" value="<?php echo $changepwhash ?>" />
						<input type="hidden" name="forgotpwchangeform" value="1" />
						<input class="loginsubmit" name="loginsubmit" type="submit" value="<?php echo lang('submit')?>" /> 
						<input class="loginsubmit" name="logincancel" type="submit" value="<?php echo lang('cancel')?>" />
					</fieldset>
				</form>
			
		<?php } else if (isset($_REQUEST['forgotpw']) && $_REQUEST['forgotpw']) {?>
				<div class="message information">
					<p><?php echo lang('forgotpwprompt')?></p>
				</div>									
				
				<form method="post" action="login.php">
					<fieldset>
						<p class="form"><label for="lbusername"><?php echo lang('username')?></label>
						<input id="lbusername" name="forgottenusername" <?php if(!isset($_POST['username'])) echo 'class="focus"' ?> type="text" size="15" value="" /></p>
						<input type="hidden" name="forgotpwform" value="1" />
						<input class="loginsubmit" name="loginsubmit" type="submit" value="<?php echo lang('submit')?>" /> 
						<input class="loginsubmit" name="logincancel" type="submit" value="<?php echo lang('cancel')?>" />
					</fieldset>
				</form>			
		<?php } else {?>

				<form method="post" action="login.php">
					<fieldset>
						<p class="left"><label for="lbusername"><?php echo lang('username')?></label>
						<input id="lbusername" placeholder="<?php echo lang('username')?>" name="username" <?php if(!isset($_POST['username'])) echo 'class="focus"' ?> type="text" size="15" value="<?php echo htmlentities(isset($_POST['username'])?$_POST['username']:'')?>" /></p>
						<p class="right"><label for="lbpassword"><?php echo lang('password')?></label>
					<?php if (isset($error) && $error != '') {?>
						<input id="lbpassword" placeholder="<?php echo lang('password')?>" class="focus" name="password" type="password" size="15" />
					<?php } else {?>
						<input id="lbpassword" placeholder="<?php echo lang('password')?>" name="password" type="password" size="15" />
					<?php }?></p>
						<input class="loginsubmit" name="loginsubmit" type="submit" value="<?php echo lang('submit')?>" /> 
						<input class="loginsubmit" name="logincancel" type="submit" value="<?php echo lang('cancel')?>" />
					</fieldset>			
				</form>					
				
				<p class="forgotpw"><a href="login.php?forgotpw=1"><?php echo lang('lostpw')?></a></p>
				
		<?php }?>
		</div>
	</div>
	<div class="copyright">		
		<p class="small"><?php echo lang('warn_admin_ipandcookies');?></p>
		<small id="copy"> &copy; <a rel="external" href="http://www.cmsmadesimple.org" >CMS Made Simple</a>&trade;</small>
	</div>
</body>
</html> 