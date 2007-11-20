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
			<li><a href="#general">{tr}General{/tr}</a></li>
			<li><a href="#languages">{tr}Languages{/tr}</a></li>
		</ul>

		<div id="general" class="fragment">

			<form method="post" name="generalform" id="generalform" action="siteprefs.php">

				<div class="pageoverflow">
					<p class="pagetext">{tr}Clear Cache{/tr}:</p>
					<p class="pageinput">
						{html_submit name='clearcache' value='Clear' onmouseover="this.className='pagebuttonhover'" onmouseout="this.className='pagebutton'" tr=true}
					</p>
				</div>
				<div class="pageoverflow">
					<p class="pagetext">{tr}Site Name{/tr}:</p>
					<p class="pageinput">{html_input name='sitename' class='pagesmalltextarea' size='30' value=$sitename}</p>
				</div>
				<div class="pageoverflow">
					<p class="pagetext">{tr}File Creation Mask (umask){/tr}:</p>
					<p class="pageinput">{html_input name='global_umask' class='pagesmalltextarea' size='4' value=$global_umask}</p>
				</div>
				<div class="pageoverflow">
					<p class="pagetext">{tr}Global Metadata{/tr}:</p>
					<p class="pageinput">{html_textarea text=$metadata name='metadata' class='pagesmalltextarea'}</p>
				</div>
				<div class="pageoverflow">
					<p class="pagetext">{tr}Enable Custom 404 Message{/tr}:</p>
					<p class="pageinput">{html_checkbox class='pagenb' name='enablecustom404' value=$enablecustom404}</p>
				</div>
				<div class="pageoverflow">
					<p class="pagetext">{tr}Custom 404 Error Message{/tr}:</p>
					<p class="pageinput">{html_textarea text=$custom404 name='custom404' class='pagesmalltextarea'}</p>
				</div>
				<div class="pageoverflow">
					<p class="pagetext">{tr}Template{/tr}:</p>
					<p class="pageinput">
				        <select name="custom404template">
				          {html_options options=$templates selected=$custom404template}
				        </select>
					</p>
				</div>
				<div class="pageoverflow">
					<p class="pagetext">{tr}Enable Site Down Message{/tr}:</p>
					<p class="pageinput">{html_checkbox class='pagenb' name='enablesitedownmessage' value=$enablesitedownmessage}</p>
				</div>
				<div class="pageoverflow">
					<p class="pagetext">{tr}Site Down Message{/tr}:</p>
					<p class="pageinput">{html_textarea text=$sitedownmessage name='sitedownmessage' class='pagesmalltextarea'}</p>
				</div>
				<div class="pageoverflow">
					<p class="pagetext">&nbsp;</p>
					<p class="pageinput">
						<input type="hidden" name="editsiteprefs" value="true" />
						{html_submit name='submit' value='Submit' class='pagebutton' onmouseover='this.className='pagebuttonhover'' onmouseout='this.className='pagebutton'' tr=true}
						{html_submit name='cancel' value='Cancel' class='pagebutton' onmouseover='this.className='pagebuttonhover'' onmouseout='this.className='pagebutton'' tr=true}
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