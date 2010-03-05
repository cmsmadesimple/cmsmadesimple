<!DOCTYPE html 
     PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>CMS Login</title>
<link rel="stylesheet" type="text/css" media="screen, projection" href="themes/default/css/style.css" />
<base href="{$base_url}" />
</head>

<body>
<div class="login-all clear">
<div class="info">
  <h1>{tr}Information{/tr}</h1>
  <div class="centerLogin">
  <p id="img">{tr}From this point should take into consideration the following parameters:{/tr}:</p>
{tr}<ol> 
  <li>Cookies enabled in your browser</li> 
  <li>Javascript enabled in your browser </li> 
  <li>Windows popup active to the following address:</li> 
</ol>{/tr} 
<span>( {$smarty.server.SERVER_NAME}
  )</span>
</div>
</div>
<div class="login">
<div class="top">{tr}logintitle{/tr}</div>
		<div id="centerLogin" class="formcontainer">
				{$debug_buffer}
					{if !empty($error)}
						<div  class="erroLogin">{$error}</div >
					{/if}
                    <div class="lbfieldstext">
					<p class="lbuser">{tr}username{/tr}:</p>
					<p class="lbpass">{tr}password{/tr}:</p>
					</div>
		            <div class="login-fields">
					<form method="post" action="login.php">
						<p>
							<input  id="lbusername" name="username" class="defaultfocus" type="text" size="15" value="{$username}" /><br />
							{if !empty($error)}
							  <input id="lbpassword"  class="defaultfocus" name="password" type="password" size="15" /><br />
							{else}
							  <input id="lbpassword"  name="password" type="password" size="15" /><br />
							{/if}
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
Is free software released under the General Public Licence.</div>

</body>
</html>
