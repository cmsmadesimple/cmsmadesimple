{if isset($message)}<p>{$message}</p>{/if}
<p>{$mod->Lang('info_cmsmailer')}</p>
{$startform}
<fieldset>
	<legend>{$mod->Lang('friendlyname')} {$mod->Lang('settings_title')}</legend>
	<div class="pageoverflow">
		<p class="pagetext">{$prompt_charset}:</p>
		<p class="pageinput">{$input_charset}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">{$prompt_mailer}:</p>
		<p class="pageinput">{$input_mailer}<br/>{$info_mailer}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">{$prompt_host}:</p>
		<p class="pageinput">{$input_host}<br/>{$info_host}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">{$prompt_secure}:</p>
		<p class="pageinput">{$input_secure}<br/>{$info_secure}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">{$prompt_port}:</p>
		<p class="pageinput">{$input_port}<br/>{$info_port}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">{$prompt_from}:</p>
		<p class="pageinput">{$input_from}<br/>{$info_from}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">{$prompt_fromuser}:</p>
		<p class="pageinput">{$input_fromuser}<br/>{$info_fromuser}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">{$prompt_sendmail}:</p>
		<p class="pageinput">{$input_sendmail}<br/>{$info_sendmail}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">{$prompt_timeout}:</p>
		<p class="pageinput">{$input_timeout}<br/>{$info_timeout}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">{$prompt_smtpauth}:</p>
		<p class="pageinput">{$input_smtpauth}<br/>{$info_smtpauth}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">{$prompt_username}:</p>
		<p class="pageinput">{$input_username}<br/>{$info_username}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">{$prompt_password}:</p>
		<p class="pageinput">{$input_password}<br/>{$info_password}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">&nbsp;</p>
		<p class="pageinput">{if isset($hidden)}{$hidden}{/if}{$submit}</p>
	</div>
</fieldset>
<fieldset>
	<legend>{$mod->Lang('friendlyname')} {$mod->Lang('test_title')}</legend>
	<div class="pageoverflow">
		<p class="pagetext">{$prompt_testaddress}:</p>
		<p class="pageinput">{$input_testaddress}&nbsp;{$sendtest}</p>
	</div>
</fieldset>
{$endform}
