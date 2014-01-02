<div class="pagecontainer">

{if !$mail_is_set}
	<div class="warning">{lang('info_mail_notset')}</div>
{/if}

<script type="text/javascript">
$(document).ready(function(){
  $(document).on('click','[name=submit]',function(){
    return confirm('{lang('siteprefs_confirm')}');
  });
});
</script>

{tab_header name='general' label=lang('general_settings') active=$tab}
{tab_header name='editcontent' label=lang('editcontent_settings') active=$tab}
{tab_header name='sitedown' label=lang('sitedown_settings') active=$tab}
{tab_header name='mail' label=lang('mail_settings') active=$tab}
{tab_header name='setup' label=lang('setup') active=$tab}
{tab_header name='smarty' label=lang('smarty_settings') active=$tab}

{* +++++++++++++++++++++++++++++++++++++++++++ *}
{tab_start name='general'}
	<form id="siteprefform_general" method="post" action="{$formurl}">
		<div>
			<input type="hidden" name="{$SECURE_PARAM_NAME}" value="{$CMS_USER_KEY}"/>
			<input type="hidden" name="active_tab" value="general" />
			<input type="hidden" name="editsiteprefs" value="true" />
		</div>
		<div class="pageoverflow">
			<p class="pageinput">
				<input type="submit" name="submit" value="{lang('submit')}" class="pagebutton" />
				<input type="submit" name="cancel" value="{lang('cancel')}" class="pagebutton" />
			</p>
			<br />
		</div>
		<div class="pageoverflow">
			<p class="pagetext"><label for="sitename">{'sitename'|lang}:</label></p>
			<p class="pageinput"><input type="text" id="sitename" class="pagesmalltextarea" name="sitename" size="30" value="{$sitename}" />&nbsp;{cms_help key2='siteprefs_sitename'}</p>
		</div>
		<div class="pageoverflow">
			<p class="pagetext"><label for="frontendlang">{lang('frontendlang')}:</label></p>
			<p class="pageinput">
				<select id="frontendlang" name="frontendlang" style="vertical-align: middle;">
					{html_options options=$languages selected=$frontendlang}
				</select>&nbsp;{cms_help key2='siteprefs_frontendlang'}
			</p>
		</div>
		<div class="pageoverflow">
			<p class="pagetext"><label for="frontendwysiwyg">{lang('frontendwysiwygtouse')}:</label></p>
			<p class="pageinput">
				<select id="frontendwysiwyg" name="frontendwysiwyg">
					{html_options options=$wysiwyg selected=$frontendwysiwyg}
				</select>&nbsp;{cms_help key2='siteprefs_frontendwysiwyg'}
			</p>
		</div>
		<div class="pageoverflow">
			<p class="pagetext"><label for="nogcbwysiwyg">{lang('nogcbwysiwyg')}:</label></p>
			<p class="pageinput"><input type="hidden" name="nogcbwysiwyg" value="0"/><input id="nogcbwysiwyg" class="pagenb" type="checkbox" value="1" name="nogcbwysiwyg" {if $nogcbwysiwyg == "1"}checked="checked"{/if} />&nbsp;{cms_help key2='siteprefs_nogcbwysiwyg'}</p>
		</div>
		<div class="pageoverflow">
			<p class="pagetext"><label for="globalmetadata">{lang('globalmetadata')}:</label>&nbsp;{cms_help key2='siteprefs_globalmetadata'}</p>
			<p class="pageinput"><textarea id="globalmetadata" class="pagesmalltextarea" name="metadata" cols="80" rows="20">{$metadata}</textarea></p>
		</div>
		{if isset($themes)}
			<div class="pageoverflow">
				<p class="pagetext"><label for="logintheme">{lang('master_admintheme')}:</label></p>
				<p class="pageinput">
					<select id="logintheme" name="logintheme">
						{html_options options=$themes selected=$logintheme}
					</select>&nbsp;{cms_help key2='siteprefs_logintheme'}
				</p>
			</div>
		{/if}
		<div class="pageoverflow">
			<p class="pagetext"><label for="backendwysiwyg">{lang('backendwysiwygtouse')}:</label></p>
			<p class="pageinput">
				<select id="backendwysiwyg" name="backendwysiwyg">
					{html_options options=$wysiwyg selected=$backendwysiwyg}
				</select>&nbsp;{cms_help key2='siteprefs_backendwysiwyg'}
			</p>
		</div>
		<div class="pageoverflow">
			<p class="pagetext"><label for="defaultdateformat">{lang('date_format_string')}:</label></p>
			<p class="pageinput">
				<input class="pagenb" id="defaultdateformat" type="text" name="defaultdateformat" size="20" maxlength="255" value="{$defaultdateformat}"/>&nbsp;{cms_help key2='siteprefs_dateformat'}
			</p>
		</div>
		<div class="pageoverflow">
			<p class="pagetext"><label for="thumbnail_width">{lang('thumbnail_width')}:</label></p>
			<p class="pageinput">
				<input class="pagenb" id="thumbnail_width" type="text" name="thumbnail_width" size="3" maxlength="3" value="{$thumbnail_width}"/>
				&nbsp;{cms_help key2='siteprefs_thumbwidth'}
			</p>
		</div>
		<div class="pageoverflow">
			<p class="pagetext"><label for="thumbnail_height">{lang('thumbnail_height')}:</label></p>
			<p class="pageinput">
				<input id="thumbnail_height" class="pagenb" type="text" name="thumbnail_height" size="3" maxlength="3" value="{$thumbnail_height}"/>
				&nbsp;{cms_help key2='siteprefs_thumbheight'}
			</p>
		</div>
		{if isset($search_modules)}
			<p class="pagetext"><label for="search_module">{lang('search_module')}:</label></p>
			<p class="pageinput">
				<select id="search_module" name="search_module">
					{html_options options=$search_modules selected=$search_module}
				</select>&nbsp;{cms_help key2='settings_searchmodule'}
			</p>
		{/if}
		<div class="pageoverflow">
			<br />
			<p class="pageinput">
				<input type="submit" name="submit" value="{lang('submit')}" class="pagebutton" />
				<input type="submit" name="cancel" value="{lang('cancel')}" class="pagebutton" />
			</p>
		</div>
	</form>

{* +++++++++++++++++++++++++++++++++++++++++++ *}
{tab_start name='editcontent'}
	<form id="siteprefform_editcontent" method="post" action="{$formurl}">
		<div>
			<input type="hidden" name="{$SECURE_PARAM_NAME}" value="{$CMS_USER_KEY}"/>
			<input type="hidden" name="active_tab" value="editcontent" />
			<input type="hidden" name="editsiteprefs" value="true" />
		</div>
		{if !$pretty_urls}
			<div class="warning">{lang('warn_nosefurl')}&nbsp;&nbsp;{cms_help key2='settings_nosefurl'}</div>
		{/if}
		<div class="pageoverflow">
			<p class="pageinput">
				<input type="submit" name="submit" value="{lang('submit')}" class="pagebutton"  />
				<input type="submit" name="cancel" value="{lang('cancel')}" class="pagebutton"  />
			</p>
			<br />
		</div>
		{if $pretty_urls}
			<div class="pageoverflow">
				<p class="pagetext"><label for="content_autocreate_urls">{lang('content_autocreate_urls')}:</label></p>
				<p class="pageinput">
					<select id="content_autocreate_urls" name="content_autocreate_urls">
						{html_options options=$yesno selected=$content_autocreate_urls}
					</select>&nbsp;{cms_help key2='settings_autocreate_url'}
				</p>
			</div>
		{/if}
		{if $pretty_urls}
			<div class="pageoverflow">
				<p class="pagetext"><label for="content_autocreate_flaturls">{lang('content_autocreate_flaturls')}:</label></p>
				<p class="pageinput">
					<select id="content_autocreate_flaturls" name="content_autocreate_flaturls">
						{html_options options=$yesno selected=$content_autocreate_flaturls}
					</select>&nbsp;{cms_help key2='settings_autocreate_flaturls'}
				</p>
			</div>
		{/if}
		{if $pretty_urls}
			<div class="pageoverflow">
				<p class="pagetext"><label for="content_mandatory_urls">{lang('content_mandatory_urls')}:</label></p>
				<p class="pageinput">
					<select id="content_mandatory_urls" name="content_mandatory_urls">
						{html_options options=$yesno selected=$content_mandatory_urls}
					</select>&nbsp;{cms_help key2='settings_mandatory_urls'}
				</p>
			</div>
		{/if}
		<div class="pageoverflow">
			<p class="pagetext"><label for="disallowed_contenttypes">{lang('disallowed_contenttypes')}:</label>&nbsp;{cms_help key2='settings_badtypes'}</p>
			<p class="pageinput">
				<select id="disallowed_contenttypes" name="disallowed_contenttypes[]" multiple="multiple" size="5">
					{html_options options=$all_contenttypes selected=$disallowed_contenttypes}
				</select>
			</p>
		</div>
		<div class="pageoverflow">
			<p class="pagetext"><label for="basic_attributes">{lang('basic_attributes')}:</label>&nbsp;{cms_help key2='settings_basicattribs'}</p>
			<p class="pageinput">
				<select id="basic_attributes" class="multicolumn" name="basic_attributes[]" multiple="multiple" size="5">
					{cms_html_options options=$all_attributes selected=$basic_attributes}
				</select>
			</p>
		</div>
		<div class="pageoverflow">
			<p class="pagetext"><label for="imagefield_path">{lang('content_imagefield_path')}:</label></p>
			<p class="pageinput">
				<input id="imagefield_path" type="text" name="content_imagefield_path" size="50" maxlength="255" value="{$content_imagefield_path}"/>
				&nbsp;{cms_help key2='settings_imagefield_path'}
			</p>
		</div>
		<div class="pageoverflow">
			<p class="pagetext"><label for="thumbfield_path">{lang('content_thumbnailfield_path')}:</label></p>
			<p class="pageinput">
				<input id="thumbfield_path" type="text" name="content_thumbnailfield_path" size="50" maxlength="255" value="{$content_thumbnailfield_path}"/>&nbsp;{cms_help key2='settings_thumbfield_path'}
			</p>
		</div>
		<div class="pageoverflow">
			<p class="pagetext"><label for="contentimage_path">{lang('contentimage_path')}:</label></p>
			<p class="pageinput">
				<input type="text" id="contentimage_path" name="contentimage_path" size="50" maxlength="255" value="{$contentimage_path}"/>
				&nbsp;{cms_help key2='settings_contentimage_path'}
			</p>
		</div>
		<div class="pageoverflow">
			<p class="pagetext"><label for="cssnameisblockname">{lang('cssnameisblockname')}:</label></p>
			<p class="pageinput">
                                <select id="cssnameisblockname" name="content_cssnameisblockname">
                                {cms_yesno selected=$content_cssnameisblockname}
				</select>
				&nbsp;{cms_help key2='settings_cssnameisblockname'}
			</p>
		</div>
		<div class="pageoverflow">
			<br />
			<p class="pageinput">
				<input type="submit" name="submit" value="{lang('submit')}" class="pagebutton" />
				<input type="submit" name="cancel" value="{lang('cancel')}" class="pagebutton" />
			</p>
		</div>
	</form>
	
{* +++++++++++++++++++++++++++++++++++++++++++ *}
{tab_start name='sitedown'}
	<form id="siteprefform_sitedown" method="post" action="{$formurl}">
		<div>
			<input type="hidden" name="{$SECURE_PARAM_NAME}" value="{$CMS_USER_KEY}"/>
			<input type="hidden" name="active_tab" value="sitedown" />
			<input type="hidden" name="editsiteprefs" value="true" />
		</div>
		<div class="information">{lang('info_settings_sitedown')}</div>
		<div class="pageoverflow">
			<p class="pageinput">
				<input type="submit" name="submit" value="{lang('submit')}" class="pagebutton"  />
				<input type="submit" name="cancel" value="{lang('cancel')}" class="pagebutton"  />
			</p>
			<br />
		</div>

		<div class="pageoverflow">
			<p class="pagetext"><label for="enablesitedown">{lang('enablesitedown')}:</label></p>
			<p class="pageinput">
				<select id="enablesitedown" name="enablesitedownmessage">
					{cms_yesno selected=$enablesitedownmessage}
				</select>
		</div>
		<div class="pageoverflow">
			<p class="pagetext"><label for="enablewysiwyg">{lang('enablewysiwyg')}:</label></p>
			<p class="pageinput">
				<select id="enablewysiwyg">
					{cms_yesno selected=$enablewysiwyg}
				</select>
		</div>
		<div class="pageoverflow">
			<p class="pagetext">{lang('sitedownmessage')}:</p>
			<p class="pageinput">{$textarea_sitedownmessage}</p>
		</div>
		<div class="pageoverflow">
			<p class="pagetext">{'sitedownexcludeadmins'|lang}:</p>
			<p class="pageinput">
				<select id="sitedownexcludeadmins" name="sitedownexcludeadmins">
					{cms_yesno selected=$sitedownexcludeadmins}
				</select>
			</p>
		</div>
		<div class="pageoverflow">
			<p class="pagetext"><label for="sitedownexcludes">{lang('sitedownexcludes')}:</label></p>
			<p class="pageinput">
				<input id="sitedownexcludes" type="text" name="sitedownexcludes" size="50" maxlength="255" value="{$sitedownexcludes}" />
				<br /><strong>{lang('your_ipaddress')}:</strong>&nbsp;<span style="color: red;">{cms_utils::get_real_ip()}</span><br/>{$lang_info_sitedownexcludes}
			</p>
		</div>
		<div class="pageoverflow">
			<br />
			<p class="pageinput">
				<input type="submit" name="submit" value="{lang('submit')}" class="pagebutton" />
				<input type="submit" name="cancel" value="{lang('cancel')}" class="pagebutton" />
			</p>
		</div>
	</form>

{* +++++++++++++++++++++++++++++++++++++++++++ *}
{tab_start name='mail'}
<script type="text/javascript">
function on_mailer()
{
  var v = $('#mailer').val();
  if( v == 'mail' ) {
    $('#set_smtp').find('input,select').attr('disabled','disabled');
    $('#set_sendmail').find('input,select').attr('disabled','disabled');
  }
  else if( v == 'smtp' ) {
    $('#set_smtp').find('input,select').removeAttr('disabled');
    $('#set_sendmail').find('input,select').attr('disabled','disabled');
  }
  else if( v == 'sendmail' ) {
    $('#set_smtp').find('input,select').attr('disabled','disabled');
    $('#set_sendmail').find('input,select').removeAttr('disabled');
  }
}
$(document).ready(function(){
{if !$mail_is_set}$('#mailertest').hide();{/if}
  $(document).on('click', '#mailertest', function(e){
    $('#testpopup').dialog({
      modal: true
    });
    return false;
  });

  $(document).on('click', '#testcancel', function(e){
    $('#testpopup').dialog('close');
  });
  $(document).on('click','#testsend', function(e){
    $('#testpopup').dialog('close');
    $(this).closest('form').submit();
  });

  $('#mailer').change(function(){
    on_mailer();
  });
  on_mailer();
});
</script>

	<div id="testpopup" title="{lang('title_mailtest')}" style="display: none;">
		<form id="siteprefform_mail" method="post" action="{$formurl}">
			<div>
				<input type="hidden" name="{$SECURE_PARAM_NAME}" value="{$CMS_USER_KEY}"/>
				<input type="hidden" name="active_tab" value="mail" />
				<input type="hidden" name="testmail" value="1"/>
			</div>
			<div class="information">{lang('info_mailtest')}</div>
			<div class="pageoverflow">
				<p class="pagetext"><label for="testaddress">{lang('settings_testaddress')}:</label>&nbsp;</p>
				<p class="pageinput">
					<input type="text" id="testaddress" name="mailtest_testaddress" size="50" maxlength="255"/>
					&nbsp;{cms_help key2='settings_mailtest_testaddress'}
				</p>
			</div>
			<div class="pageoverflow">
				<p class="pagetext"></p>
				<p class="pageinput">
					<input id="testsend" type="submit" name="sendtest" value="{lang('sendtest')}"/>
					<input id="testcancel" type="submit" value="{lang('cancel')}"/>
				</p>
			</div>
		</form>
	</div>

	<form id="siteprefform_mail" method="post" action="{$formurl}">
		<div>
			<input type="hidden" name="{$SECURE_PARAM_NAME}" value="{$CMS_USER_KEY}"/>
			<input type="hidden" name="active_tab" value="mail" />
			<input type="hidden" name="editsiteprefs" value="true" />
		</div>
		<div class="pageoverflow">
			<p class="pageinput">
				<input type="submit" name="submit" value="{lang('submit')}" class="pagebutton"  />
				<input id="mailertest" type="submit" name="testemail" value="{lang('test')}" class="pagebutton"  />
				<input type="submit" name="cancel" value="{lang('cancel')}" class="pagebutton"  />
			</p>
			<br />
		</div>

		<fieldset id="set_general">
			<legend>{lang('general_settings')}:</legend>
				<div class="pageoverflow">
					<p class="pagetext"><label for="mailer">{lang('settings_mailer')}:</labei></p>
					<p class="pageinput">
						<select id="mailer" name="mailprefs_mailer">
							{html_options options=$maileritems selected=$mailprefs.mailer}
						</select>&nbsp;{cms_help key2='settings_mailprefs_mailer'}
					</p>
				</div>
				<div class="pageoverflow">
					<p class="pagetext"><label for="from">{lang('settings_mailfrom')}:</label></p>
					<p class="pageinput">
						<input type="text" id="from" name="mailprefs_from" value="{$mailprefs.from}" size="50" maxlength="255"/>
						&nbsp;{cms_help key2='settings_mailprefs_from'}
					</p>
				</div>
				<div class="pageoverflow">
					<p class="pagetext"><label for="fromuser">{lang('settings_mailfromuser')}:</label></p>
					<p class="pageinput">
						<input type="text" id="fromuser" name="mailprefs_fromuser" value="{$mailprefs.fromuser}" size="50" maxlength="255"/>
						&nbsp;{cms_help key2='settings_mailprefs_fromuser'}
					</p>
				</div>
		</fieldset>

		<fieldset id="set_smtp">
			<legend>{lang('smtp_settings')}:</legend>
				<div class="pageoverflow">
					<p class="pagetext"><label for="host">{lang('settings_smtphost')}:</label></p>
					<p class="pageinput">
						<input type="text" id="host" name="mailprefs_host" value="{$mailprefs.host}" size="50" maxlength="255"/>
						&nbsp;{cms_help key2='settings_mailprefs_smtphost'}
					</p>
				</div>

				<div class="pageoverflow">
					<p class="pagetext"><label for="port">{lang('settings_smtpport')}:</label></p>
					<p class="pageinput">
						<input type="text" id="port" name="mailprefs_port" value="{$mailprefs.port}" size="6" maxlength="8"/>
						&nbsp;{cms_help key2='settings_mailprefs_smtpport'}
					</p>
				</div>

				<div class="pageoverflow">
					<p class="pagetext"><label for="timeout">{lang('settings_smtptimeout')}:</label></p>
					<p class="pageinput">
						<input type="text" id="timeout" name="mailprefs_timeout" value="{$mailprefs.timeout}" size="6" maxlength="8"/>
						&nbsp;{cms_help key2='settings_mailprefs_smtptimeout'}
					</p>
				</div>

				<fieldset>
					<legend>{lang('settings_authentication')}:</legend>
					<div class="pageoverflow">
						<p class="pagetext"><label for="smtpauth">{lang('settings_smtpauth')}:</label></p>
						<p class="pageinput">
							<select id="smtpauth" name="mailprefs_smtpauth">
								{cms_yesno selected=$prefs.smtpauth}
							</select>&nbsp;{cms_help key2='settings_mailprefs_smtpauth'}
						</p>
					</div>

					<div class="pageoverflow">
						<p class="pagetext"><label for="secure">{lang('settings_authsecure')}:</label></p>
						<p class="pageinput">
							<select id="secure" name="mailprefs_secure">
								{html_options options=$secure_opts selected=$prefs.secure}
							</select>&nbsp;{cms_help key2='settings_mailprefs_smtpsecure'}
						</p>
					</div>

					<div class="pageoverflow">
						<p class="pagetext"><label for="username">{lang('settings_authusername')}:</label></p>
						<p class="pageinput">
							<input type="text" id="username" name="mailprefs_username" value="{$mailprefs.username}" size="50" maxlength="255"/>
							&nbsp;{cms_help key2='settings_mailprefs_smtpusername'}
						</p>
					</div>

					<div class="pageoverflow">
						<p class="pagetext"><label for="password">{lang('settings_authpassword')}:</label></p>
						<p class="pageinput">
							<input type="password" id="password" name="mailprefs_password" value="{$mailprefs.password}" size="30" maxlength="30"/>
							&nbsp;{cms_help key2='settings_mailprefs_smtppassword'}
						</p>
					</div>
				</fieldset>
		</fieldset>

		<fieldset id="set_sendmail">
			<legend>{lang('sendmail_settings')}:</legend>
				<div class="pageoverflow">
					<p class="pagetext"><label for="sendmail">{lang('settings_sendmailpath')}:</label></p>
					<p class="pageinput">
						<input type="text" id="sendmail" name="mailprefs_sendmail" value="{$mailprefs.sendmail}" size="50" maxlength="255"/>
						&nbsp;{cms_help key2='settings_mailprefs_sendmail'}
					</p>
				</div>
		</fieldset>
		<div class="pageoverflow">
			<br />
			<p class="pageinput">
				<input type="submit" name="submit" value="{lang('submit')}" class="pagebutton" />
				<input type="submit" name="cancel" value="{lang('cancel')}" class="pagebutton" />
			</p>
		</div>
	</form>

{* +++++++++++++++++++++++++++++++++++++++++++ *}
{tab_start name='setup'}
	<form id="siteprefform_setup" method="post" action="{$formurl}">
		<div>
			<input type="hidden" name="{$SECURE_PARAM_NAME}" value="{$CMS_USER_KEY}"/>
			<input type="hidden" name="active_tab" value="setup" />
			<input type="hidden" name="editsiteprefs" value="true" />
		</div>
		<div class="pageoverflow">
			<p class="pageinput">
				<input type="submit" name="submit" value="{lang('submit')}" class="pagebutton"  />
				<input type="submit" name="cancel" value="{lang('cancel')}" class="pagebutton"  />
			</p>
			<br />
		</div>

		<fieldset>
			<legend>{'browser_cache_settings'|lang}:&nbsp;</legend>
				<div class="pageoverflow">
					<p class="pagetext"><label for="allow_browser_cache">{lang('allow_browser_cache')}:</label></p>
					<p class="pageinput">
						<select name="allow_browser_cache">
							{cms_yesno selected=$allow_browser_cache}
						</select>&nbsp;{cms_help key2='settings_browsercache'}
					</p>
				</div>  
				<div class="pageoverflow">
					<p class="pagetext"><label for="browser_expiry">{lang('browser_cache_expiry')}:</label></p>
					<p class="pageinput">
						<input type="text" id="browser_expiry" name="browser_cache_expiry" value="{$browser_cache_expiry}" size="6" maxlength="10"/>
						&nbsp;{cms_help key2='settings_browsercache_expiry'}
					</p>
				</div>  
		</fieldset>

		<fieldset>
			<legend>{'server_cache_settings'|lang}:&nbsp;</legend>
				<div class="pageoverflow">
					<p class="pagetext"><label for="autoclearcache2">{'autoclearcache2'|lang}:</label></p>
					<p class="pageinput">
						<input id="autoclearcache2" type="text" name="auto_clear_cache_age" size="4" value="{$auto_clear_cache_age}" maxlength="4"/>&nbsp;{cms_help key2='settings_autoclearcache'}
					</p>
				</div>
		</fieldset>
		<fieldset>
			<legend>{'general_operation_settings'|lang}:&nbsp;</legend>
				<div class="pageoverflow">
					<p class="pagetext"><label for="umask">{lang('global_umask')}:</label></p>
					<p class="pageinput">
						<input id="umask" type="text" class="pagesmalltextarea" name="global_umask" size="4" value="{$global_umask}" />
						&nbsp;{cms_help key2='settings_umask'}
					</p>
				</div>
				{if isset($testresults)}
					<div class="pageoverflow">
						<p class="pagetext">{lang('results')}</p>
						<p class="pageinput"><strong>{$testresults}</strong></p>
					</div>
				{/if}
				<div class="pageoverflow">
					<p class="pagetext">&nbsp;</p>
					<p class="pageinput"><input type="submit" name="testumask" value="{lang('test')}" class="pagebutton"  /></p>
				</div>
				<div class="pageoverflow">
					<p class="pagetext"><label for="safemodewarn">{lang('disablesafemodewarning')}:</label></p>
					<p class="pageinput">
						<select id="safemodewarn" name="disablesafemodewarning">
							{cms_yesno selected=$disablesafemodewarning}
						</select>&nbsp;{cms_help key2='settings_disablesafemodewarn'}
					</p>
				</div>
				<div class="pageoverflow">
					<p class="pagetext"><label for="notifications">{lang('admin_enablenotifications')}:</label></p>
					<p class="pageinput">
						<select id="notifications" name="enablenotifications">
							{cms_yesno selected=$enablenotifications}
						</select>&nbsp;{cms_help key2='settings_enablenotifications'}
				</div>
				<div class="pageoverflow">
					<p class="pagetext"><label for="pseudocron">{lang('pseudocron_granularity')}:</label></p>
					<p class="pageinput">
						<select id="pseudocron" name="pseudocron_granularity">
							{html_options options=$pseudocron_options selected=$pseudocron_granularity}
						</select>&nbsp;{cms_help key2='settings_pseudocron_granularity'}
					</p>
				</div>
				<div class="pageoverflow">
					<p class="pagetext"><label for="adminlog">{lang('adminlog_lifetime')}:</label></p>
					<p class="pageinput">
						<select id="adminlog" name="adminlog_lifetime">
							{html_options options=$adminlog_options selected=$adminlog_lifetime}
						</select>&nbsp;{cms_help key2='settings_adminlog_lifetime'}
					</p>
				</div>
				<div class="pageoverflow">
					<p class="pagetext"><label for="checkversion">{lang('checkversion')}:</label></p>
					<p class="pageinput">
						<select id="checkversion" name="checkversion">
							{cms_yesno options=$checkversion}
						<select>&nbsp;{cms_help key2='settings_checkversion'}	  
					</p>
				</div>
		</fieldset>
		<div class="pageoverflow">
			<br />
			<p class="pageinput">
				<input type="submit" name="submit" value="{lang('submit')}" class="pagebutton" />
				<input type="submit" name="cancel" value="{lang('cancel')}" class="pagebutton" />
			</p>
		</div>
	</form>

{* +++++++++++++++++++++++++++++++++++++++++++ *}
{tab_start name='smarty'}
	<form id="siteprefform_setup" method="post" action="{$formurl}">
		<div>
			<input type="hidden" name="{$SECURE_PARAM_NAME}" value="{$CMS_USER_KEY}"/>
			<input type="hidden" name="active_tab" value="smarty" />
			<input type="hidden" name="editsiteprefs" value="true" />
		</div>
		<div class="pageoverflow">
			<p class="pageinput">
				<input type="submit" name="submit" value="{lang('submit')}" class="pagebutton"  />
				<input type="submit" name="cancel" value="{lang('cancel')}" class="pagebutton"  />
			</p>
			<br />
		</div>

		<div class="pageoverflow">
			<p class="pagetext"><label for="smartycache">{lang('prompt_use_smartycaching')}:</label></p>
			<p class="pageinput">
				<select id="smartycache" name="use_smartycache">
					{html_options options=$yesno selected=$use_smartycache}
				</select>&nbsp;{cms_help key2='settings_smartycaching'}
			</p>
		</div>
		<div class="pageoverflow">
			<p class="pagetext"><label for="compilecheck">{lang('prompt_smarty_compilecheck')}:</label></p>
			<p class="pageinput">
				<select for="compilecheck" name="use_smartycompilecheck">
					{html_options options=$yesno selected=$use_smartycompilecheck}
				</select>&nbsp;{cms_help key2='settings_smartycompilecheck'}
			</p>
		</div>
	</form>
{tab_end}

<p class="pageback">
	<a class="pageback" href="{$backurl}">&#171; {lang('back')}</a>
</p>

</div>