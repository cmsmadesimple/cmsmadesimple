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
	</div>

	<div id="page_tabs">

		<ul class="anchors">
			<li><a href="#general"><span>{tr}General{/tr}</span></a></li>
			<li><a href="#languages"><span>{tr}Languages{/tr}</span></a></li>
		</ul>

		<div id="general" class="fragment">

			<form method="post" name="generalform" id="generalform" action="siteprefs.php">
				{admin_input type='submit' label='Clear Cache' id='clearcache' name='clearcache' value='Clear'}
				{admin_input type='input' label='Site Name' name='sitename' value=$sitename}
				{admin_input type='input' label='File Creation Mask (umask)' name='global_umask' value=$global_umask size='4'}
				{admin_input type='textarea' label='Global Metadata' name='metadata' value=$metadata}
				{admin_input type='checkbox' label='Enable Custom 404 Message' name='enablecustom404' id='enablecustom404' value=$enablecustom404}				
				{admin_input type='textarea' label='Custom 404 Error Message' name='custom404' id='custom404' value=$custom404}
				{admin_input type='select' label='Template' name='custom404template' id='custom404template' options=$templates selected=$custom404template}				
				{admin_input type='checkbox' label='Enable Site Down Message' name='enablesitedownmessage' id='enablesitedownmessage' value=$enablesitedownmessage}				
				{admin_input type='textarea' label='Site Down Message' name='sitedownmessage' id='sitedownmessage' value=$sitedownmessage}
	
				<div class="pageoverflow">
					<p class="pagetext">&nbsp;</p>
					<p class="pageinput">
						<input type="hidden" name="editsiteprefs" value="true" />
						{html_submit name='submit' value='Submit' class='pagebutton' onmouseover="this.className='pagebuttonhover'" onmouseout="this.className='pagebutton'" tr=true}
						{html_submit name='cancel' value='Cancel' class='pagebutton' onmouseover="this.className='pagebuttonhover'" onmouseout="this.className='pagebutton'" tr=true}
					</p>
				</div>

			</form>

		</div>

		<div id="languages" class="fragment">

			<form method="post" name="languagesform" id="languagesform" action="siteprefs.php">
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
								<td><img src="../{$v.flag_image}" /></td>
								<td>{$k}</td>
								<td>{$v.name}</td>
								<td>{html_checkbox name=$v.checkbox_name selected=$v.enabled full_toggle=false}</td>
								<td>{$v.default}</td>
							</tr>
						{/foreach}
					</tbody>
				</table>
				<button type="submit" name="lang_form_submit" class="positive">
					<span class="text">{tr}Submit{/tr}</span>
				</button>    
			</form>

		</div>
	</div>
</div>
{literal}<script>$('#page_tabs').tabs({fxAutoHeight: false});</script>{/literal}