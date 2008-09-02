<!DOCTYPE html 
     PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>CMS Login</title>
<script src="themes/NCleanGrey2/includes/standard.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" media="screen, projection" href="themes/NCleanGrey2/css/style.css" />

<!--<link rel="stylesheet" type="text/css" media="screen, projection" href="loginstyle.php" />-->
<base href="{$base_url}" />
</head>

<body>
<div class="login-all clear">
<div class="info">
  <h1>{tr}login_info_title{/tr}</h1>
  <div class="centerLogin">
  <p id="img">{tr}login_info{/tr}:</p>
{tr}login_info_params{/tr} 
<span>( host  )</span>
</div>
</div>
<div class="login">
<div class="top"><?php echo lang('logintitle')?></div>
		<div id="centerLogin" class="formcontainer">
				{$debug_buffer}
					{if !empty($error)}
						<div  class="loginerror">{$error}</div >
					{/if}
                    <div class="lbfieldstext">
					<p class="lbuser">{tr}username{/tr}:</p>
					<p class="lbpass">{tr}password{/tr}:</p>
                    <p class="lbopenid">{tr}openid{/tr}:</p>
					</div>
		            <div class="login-fields">
					<form method="post" action="login.php">
						<p>
							<input name="username" class="defaultfocus" type="text" size="15" value="{$username}" /><br />
							{if !empty($error)}
							  <input class="lbpassword defaultfocus" name="password" type="password" size="15" /><br />
							{else}
							  <input class="lbpassword" name="password" type="password" size="15" /><br />
							{/if}
							<input class="lbopenid" name="openid" id="openid" type="text" size="15" value="{$openid}" /><br /><br />
							{html_submit name="loginsubmit" name="loginsubmit" value=$submit_text} 
							{html_submit name="logincancel" name="logincancel" value=$cancel_text}
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


<!--/*//////////////////////

<div class="lball">
	<div class="lblayout lbtopmargin"><p><img src="themes/default/images/logo.gif" alt="" /><span class="logotext">{tr}logintitle{/tr}</span></p></div>
	<div id="loginbox" class="lblayout lbcontainer">
			<div class="lbinfo">
				<p>{tr}loginprompt{/tr} <br /><br />
					{$debug_buffer}
					{if !empty($error)}
						<span class="loginerror">{$error}</span>
					{/if}
				</p>
			</div>
			<div class="lbfields">
				<div class="lbfieldstext">
					<p class="lbuser">{tr}username{/tr}:</p>
					<p class="lbpass">{tr}password{/tr}:</p>
					<p class="lbopenid">{tr}openid{/tr}:</p>
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
							<input class="lbopenid" name="openid" id="openid" type="text" size="15" value="{$openid}" /><br /><br />
							{html_submit name="loginsubmit" name="loginsubmit" value=$submit_text} 
							{html_submit name="logincancel" name="logincancel" value=$cancel_text}
						</p>
					</form>
				</div>
			</div>
	</div>
	<div class="lblayout footer"><a class="footer" href="http://www.cmsmadesimple.org">CMS Made Simple</a> is free software released under the General Public Licence.</div>*/-->
</div>	
</body>
</html>
