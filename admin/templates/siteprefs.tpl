<div class="pagecontainer">
	
	{if $error_msg}
	<div class="pageerrorcontainer">
		<p>{$error_msg}</p>
	</div>
	{/if}

	{if $message}
	<div class="pagemcontainer">
		<p>{$message}</p>
	</div>
	{/if}

	<div class="pageoverflow">
		{$header_name} 
     </div><!-- pageoverflow -->
	

	<div id="page_tabs">

		{* tab headers *}
		<ul class="anchors">
			<li><a href="#general"><span>{tr}General{/tr}</span></a></li>
			<li><a href="#languages"><span>{tr}Languages{/tr}</span></a></li>
			<li><a href="#mail"><span>{tr}Mail Settings{/tr}</span></a></li>
		</ul>
        


		<div id="general" class="fragment">
<br />
			<form id="generalform" method="post" action="siteprefs.php">
				{admin_input type='submit' label='Clear Cache' id='clearcache' name='clearcache' value='Clear'}
				{admin_input type='input' label='Site Name' name='sitename' id='sitename' value=$sitename}
				{admin_input type='input' label='File Creation Mask (umask)' name='global_umask' id='global_umask' value=$global_umask size='4'}
				{admin_input type='textarea' class='smalltext' label='Global Metadata' name='metadata' id='metadata' value=$metadata}
				{admin_input type='checkbox' label='Enable Custom 404 Message' name='enablecustom404' id='enablecustom404' value=$enablecustom404 selected=$enablecustom404}				
				{admin_input type='textarea' class='smalltext' label='Custom 404 Error Message' name='custom404' id='custom404' value=$custom404}
				{admin_input type='select' label='Template' name='custom404template' id='custom404template' options=$templates selected=$custom404template}				
				{admin_input type='checkbox' label='Enable Site Down Message' name='enablesitedownmessage' id='enablesitedownmessage' value=$enablesitedownmessage	selected=$enablesitedownmessage}			
				{admin_input type='textarea' class='smalltext' label='Site Down Message' name='sitedownmessage' id='sitedownmessage' value=$sitedownmessage}
                
                {admin_input type='select' label='Login Theme' name='logintheme' id='logintheme' options=$admintheme_options selected=$logintheme}	
                
	
			        
       
        
        <div class="input-hidden"> 
        {admin_input type='hidden' label='noneLabel' name='editsiteprefs' id='editsiteprefs' value='true'}
        <!--<input type="hidden" name="editsiteprefs" value="true" />-->
        </div> <!--input-hidden-->
				{include file='elements/buttons.tpl'}
               
			</form>

		</div>

		<div id="languages" class="fragment">

			<form id="languagesform" method="post"  action="siteprefs.php">
				<table border="0" cellspacing="0" cellpadding="3">
					<thead>
						<tr>
							<th></th>
							<th>{tr}Locale{/tr}</th>
							<th>{tr}Language{/tr}</th>
							<th>{tr}Enabled{/tr}</th>
							<th>{tr}Default Language{/tr}</th>
						</tr>
					</thead>
					<tbody>
						{foreach from=$lang_list item='v' key='k'}
							<tr>
								<td><img alt="{$k}" title="{$k}" src="../{$v.flag_image}" /></td>
								<td>{$k}</td>
								<td>{$v.name}</td>
								<td>{html_checkbox name=$v.checkbox_name selected=$v.enabled full_toggle=false}</td>
								<td>{$v.default}</td>
							</tr>
						{/foreach}
					</tbody>
				</table>
				{include file='elements/buttons.tpl'}
			</form>

		</div>

		<div id="mail" class="fragment">
        <form id="mailform" method="post"   action="siteprefs.php">
				<p>{lang string='mail_settings_short_help'}</p>
				{assign var='tmp1' value=','|explode:"mail,sendmail,smtp"}
				{assign var='tmp2' value=','|explode:"Mail,Sendmail,Smtp"}
				{admin_input type='select' label='Mailer' name='mail_mailer' id='mail_mailer' options=$tmp2 values=$tmp1 selected=$mail_mailer}
				<fieldset>
                  <legend>&nbsp;{lang string='smtp_mail_settings'}&nbsp;</legend>
				  <p>{lang string='smtp_short_help'}</p>
				  {admin_input type='input' label='SMTP Host' name='mail_host' id='mail_host' value=$mail_host}
				  {admin_input type='input' label='SMTP Port' name='mail_port' id='mail_port' value=$mail_port}
				  {admin_input type='checkbox' label='Use SMTP Auth' name='mail_smtpauth' id='mail_smtpauth' value=$mail_smtpauth selected=$mail_smtpauth}				
				  {admin_input type='input' label='SMTP Auth Username' name='mail_smtpauthuser' id='mail_smtpauthuser' value=$mail_smtpauthuser}
				  {admin_input type='input' label='SMTP Auth Password' name='mail_smtpauthpw' id='mail_smtpauthpw' value=$mail_smtpauthpw}
                </fieldset>
				<fieldset>
				  <legend>&nbsp;{lang string='sendmail_settings'}&nbsp;</legend>
				  <p>{lang string='sendmail_short_help'}</p>
				  {admin_input type='input' label='Sendmail Path' name='mail_sendmail' id='mail_sendmail' value=$mail_sendmail}
				</fieldset>
			    {admin_input type='input' label='Default From Address' name='mail_from' id='mail_from' value=$mail_from}
			    {admin_input type='input' label='Default From Name' name='mail_fromname' id='mail_fromname' value=$mail_fromname}
		
				<div class="input-hidden">
       <input type="hidden" name="mailsettings" value="true" />
        </div> <!--input-hidden-->
        
              
        
				{include file='elements/buttons.tpl'}

				<fieldset>
				  <p>{lang string='mail_test_shorthelp'}</p>
                                  <legend>&nbsp;{lang string='mail_test'}&nbsp;</legend>
				  {admin_input type='input' label='Test Address' name='test_address' id='test_address'}
				  {admin_input type='submit' label='Test' id='test_mail' name='test_mail' value='Start'}

                                </fieldset>
			</form>
		</div>
	</div>
</div>
<p class="pageback"><a class="pageback" href="{$back_url}">&#171; {lang string='back'}</a></p>
{literal}<script type="text/javascript">$('#page_tabs').tabs({fxAutoHeight: false});</script>{/literal}