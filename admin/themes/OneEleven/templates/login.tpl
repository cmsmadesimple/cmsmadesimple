<!doctype html>
<html>
	<head>
		<meta charset="{$encoding}" />
		<title>{'logintitle'|lang} - {sitename}</title>
		<base href="{$config.admin_url}/" />
		<meta name="generator" content="CMS Made Simple - Copyright (C) 2004-12 Ted Kulp. All rights reserved." />
		<meta name="robots" content="noindex, nofollow" />
		<meta name="viewport" content="initial-scale=1.0 maximum-scale=1.0 user-scalable=no" />
		<meta name="HandheldFriendly" content="True"/>
		<link rel="shortcut icon" href="{$config.admin_url}/themes/OneEleven/images/favicon/cmsms-favicon.ico"/>
		<link rel="stylesheet" href="loginstyle.php" />
		<!-- learn IE html5 -->
		<!--[if lt IE 9]>
		<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
		{cms_jquery exclude="jquery.ui.nestedSortable-1.3.4.js,jquery.json-2.2.js" append="`$config.admin_url`/themes/OneEleven/includes/login.js"}
	</head>
	<body id="login">
		<div id="wrapper">
			<div class="login-container">
				<div class="login-box cf"{if isset($error)} id="error"{/if}>
					<div class="logo">
						<img src="{$config.admin_url}/themes/OneEleven/images/layout/cmsms_login_logo.png" width="180" height="36" alt="CMS Made Simple&trade;" />
					</div>
					<div class="info-wrapper open">
					<aside class="info">
					<h2>{'login_info_title'|lang}</h2>
						<p>{'login_info'|lang}</p>
							{'login_info_params'|lang}
							<p><strong>({$smarty.server.HTTP_HOST})</strong></p>					
						<p class="warning">{'warn_admin_ipandcookies'|lang}</p>
					</aside>
					<a href="#" title="{'open'|lang}/{'close'|lang}" class="toggle-info">{'open'|lang}/{'close'|lang}</a>
					</div>					
					<header>
						<h1>{'logintitle'|lang}</h1>
					</header>
					<form method="post" action="login.php">
						<fieldset>
                                                        {assign var='usernamefld' value='username'}
							{if isset($smarty.get.forgotpw)}{assign var='usernamefld' value='forgottenusername'}{/if}
							<label for="lbusername">{'username'|lang}</label>
							<input id="lbusername"{if !isset($smarty.post.lbusername)} class="focus"{/if} placeholder="{'username'|lang}" name="{$usernamefld}" type="text" size="15" value="" />
						{if isset($smarty.get.forgotpw) && !empty($smarty.get.forgotpw)}
							<input type="hidden" name="forgotpwform" value="1" />
						{/if}
						{if !isset($smarty.get.forgotpw) && empty($smarty.get.forgotpw)}
							<label for="lbpassword">{'password'|lang}</label>
							<input id="lbpassword"{if !isset($smarty.post.lbpassword) or isset($error)} class="focus"{/if} placeholder="{'password'|lang}" name="password" type="password" size="15" />
						{/if}
						{if isset($changepwhash) && !empty($changepwhash)} 
							<label for="lbpasswordagain">{'passwordagain'|lang}</label>
							<input id="lbpasswordagain"  name="passwordagain" type="password" size="15" placeholder="{'passwordagain'|lang}" />
							<input type="hidden" name="forgotpwchangeform" value="1" />
							<input type="hidden" name="changepwhash" value="{$changepwhash}" />
						{/if}
							<input class="loginsubmit" name="loginsubmit" type="submit" value="{'submit'|lang}" />
							<input class="loginsubmit" name="logincancel" type="submit" value="{'cancel'|lang}" />
						</fieldset>
					</form>
					{if isset($smarty.get.forgotpw) && !empty($smarty.get.forgotpw)}
						<div class="message warning">
							{'forgotpwprompt'|lang}
						</div>
					{/if}
					{if isset($error)}
						<div class="message error">
							{$error}
						</div>
					{/if}
					{if isset($warninglogin)}
						<div class="message warning">
							{$warninglogin}
						</div>
					{/if}
					{if isset($acceptlogin)}
						<div class="message success">
							{$acceptlogin}
						</div>
					{/if}
					{if isset($changepwhash) && !empty($changepwhash)}
						<div class="warning message">
							{'passwordchange'|lang}
						</div>
					{/if} <a href="{root_url}" title="{'goto'|lang} {sitename}"> <img class="goback" width="16" height="16" src="{$config.admin_url}/themes/OneEleven/images/layout/goback.png" alt="{'goto'|lang} {sitename}" /> </a>
					<p class="forgotpw">
						<a href="login.php?forgotpw=1">{'lostpw'|lang}</a>
					</p>
				</div>			
				<footer>
					<small class="copyright">Copyright &copy; <a rel="external" href="http://www.cmsmadesimple.org">CMS Made Simple&trade;</a></small>
				</footer>
			</div>
		</div>
	</body>
</html>