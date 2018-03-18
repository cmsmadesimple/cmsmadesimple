
                        {* login form starts here *}
			{admin_headtext}
			    <link rel="stylesheet" href="{$mod->GetModuleURLPath()}/css/admin_login.css"/>
			{/admin_headtext}

			<div class="login-container">
				<div class="login-box cf"{if !empty($error)} id="error"{/if}>
					<div class="logo">
						<img src="{theme_root}/images/layout/cmsms_login_logo.png" width="180" height="36" alt="CMS Made Simple&trade;" />
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
					{form_start module=CoreAdminLogin action=admin_login csrf=$csrf}
						<fieldset>
							<label for="lbusername">{'username'|lang}</label>
							<input id="lbusername" class="focus" placeholder="{'username'|lang}" name="{$actionid}username" type="text" size="15" value="" autofocus="autofocus" />

							{if empty($smarty.get.forgotpw) }
							    <label for="lbpassword">{'password'|lang}</label>
							    <input id="lbpassword" class="focus" placeholder="{'password'|lang}" name="{$actionid}password" type="password" size="15" maxlength="100"/>
							{/if}
							{if !empty($changepwhash)}
							    <label for="lbpasswordagain">{'passwordagain'|lang}</label>
							    <input id="lbpasswordagain" name="{$actionid}passwordagain" type="password" size="15" placeholder="{'passwordagain'|lang}" maxlength="100" />
							    <input type="hidden" name="{$actionid}forgotpwchangeform" value="1" />
							    <input type="hidden" name="{$actionid}changepwhash" value="{$changepwhash}" />
							{elseif !empty($smarty.get.forgotpw)}
							    <input type="hidden" name="{$actionid}forgotpwform" value="1" />
							{/if}
							<input class="loginsubmit" name="{$actionid}submit" type="submit" value="{'submit'|lang}" />
							<input class="loginsubmit" name="{$actionid}cancel" type="submit" value="{'cancel'|lang}" />
						</fieldset>
					{form_end}
					{if isset($smarty.get.forgotpw) && !empty($smarty.get.forgotpw)}
						<div class="message warning">
							{'forgotpwprompt'|lang}
						</div>
					{/if}
					{if !empty($error)}
						<div class="message error">
							{$error}
						</div>
					{else if !empty($warning)}
						<div class="message warning">
							{$warning}
						</div>
					{elseif !empty($message)}
						<div class="message success">
							{$message}
						</div>
					{/if}
					{if isset($changepwhash) && !empty($changepwhash)}
						<div class="warning message">
							{$mod->Lang('warn_passwordchange')}
						</div>
					{/if} <a href="{root_url}" title="{'goto'|lang} {sitename}"> <img class="goback" width="16" height="16" src="{theme_root}/images/layout/goback.png" alt="{'goto'|lang} {sitename}" /> </a>
					<p class="forgotpw">
						<a href="login.php?forgotpw=1">{'lostpw'|lang}</a>
					</p>
				</div>
			</div>{* .login-container *}
			{* login form ends here *}
