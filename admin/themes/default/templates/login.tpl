<!DOCTYPE html 
     PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>CMS Login</title>
<script src="themes/default/includes/standard.js" type="text/javascript"></script>

<link rel="stylesheet" type="text/css" media="screen, projection" href="loginstyle.php" />
<base href="{$base_url}" />
</head>

<body>
<div class="lball">
	<div class="lblayout lbtopmargin"><p><img src="themes/default/images/logo.gif" alt="" /><span class="logotext">{$logintitle_text}</span></p></div>
	<div id="loginbox" class="lblayout lbcontainer">
			<div class="lbinfo">
				<p>{$loginprompt_text} <br /><br />
					{$debug_buffer}
					{if !empty($error)}
						<span class="loginerror">{$error}</span>
					{/if}
				</p>
			</div>
			<div class="lbfields">
				<div class="lbfieldstext">
					<p class="lbuser">{$username_text}:</p>
					<p class="lbpass">{$password_text}:</p>
				</div>
				<div class="lbinput">
					<form method="post" action="login.php">
						<p>
							<input name="username" class="defaultfocus" type="text" size="15" value="{$username}" /><br />
							{if !empty($error)}
							  <input class="lbpassword defaultfocus" name="password" type="password" size="15" /><br />
							{else}
							  <input class="lbpassword" name="password" type="password" size="15" /><br />
							{/if}
							<input class="lbsubmit" name="loginsubmit" type="submit" value="{$submit_text}" /> 
							<input class="lbsubmit" name="logincancel" type="submit" value="{$cancel_text}" />
						</p>
					</form>
				</div>
			</div>
	</div>
	<div class="lblayout footer"><a class="footer" href="http://www.cmsmadesimple.org">CMS Made Simple</a> is free software released under the General Public Licence.</div>
</div>	
</body>
</html>
