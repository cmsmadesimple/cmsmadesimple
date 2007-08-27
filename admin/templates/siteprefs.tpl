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
			<li><a href="#general">{translate}General{/translate}</a></li>
			<li><a href="#languages">{translate}Languages{/translate}</a></li>
		</ul>

		<div id="general" class="fragment">

			<form method="post" name="generalform" id="generalform">

				<div class="pageoverflow">
					<p class="pagetext">{translate}Clear Cache{/translate}:</p>
					<p class="pageinput">
						{html_submit name='clearcache' value='Clear' onmouseover="this.className='pagebuttonhover'" onmouseout="this.className='pagebutton'" translate=true}
					</p>
				</div>
				<div class="pageoverflow">
					<p class="pagetext">{translate}Site Name{/translate}:</p>
					<p class="pageinput">{html_input name='sitename' class='pagesmalltextarea' size='30' value=$sitename}</p>
				</div>
				<div class="pageoverflow">
					<p class="pagetext">{translate}File Creation Mask (umask){/translate}:</p>
					<p class="pageinput">{html_input name='global_umask' class='pagesmalltextarea' size='4' value=$global_umask}</p>
				</div>
				<div class="pageoverflow">
					<p class="pagetext">{translate}Global Metadata{/translate}:</p>
					<p class="pageinput">{html_textarea text=$metadata name='metadata' class='pagesmalltextarea'}</p>
				</div>
				<div class="pageoverflow">
					<p class="pagetext">{translate}Enable Custom 404 Message{/translate}:</p>
					<p class="pageinput">{html_checkbox class='pagenb' name='enablecustom404' value=$enablecustom404}</p>
				</div>
				<div class="pageoverflow">
					<p class="pagetext">{translate}Custom 404 Error Message{/translate}:</p>
					<p class="pageinput">{html_textarea text=$custom404 name='custom404' class='pagesmalltextarea'}</p>
				</div>
				<div class="pageoverflow">
					<p class="pagetext">{translate}Template{/translate}:</p>
					<p class="pageinput">
				        <select name="custom404template">
				          {html_options options=$templates selected=$custom404template}
				        </select>
					</p>
				</div>
				<div class="pageoverflow">
					<p class="pagetext">{translate}Enable Site Down Message{/translate}:</p>
					<p class="pageinput">{html_checkbox class='pagenb' name='enablesitedownmessage' value=$enablesitedownmessage}</p>
				</div>
				<div class="pageoverflow">
					<p class="pagetext">{translate}Site Down Message{/translate}:</p>
					<p class="pageinput">{html_textarea text=$sitedownmessage name='sitedownmessage' class='pagesmalltextarea'}</p>
				</div>
				<div class="pageoverflow">
					<p class="pagetext">&nbsp;</p>
					<p class="pageinput">
						<input type="hidden" name="editsiteprefs" value="true" />
						{html_submit name='submit' value='Submit' class='pagebutton' onmouseover='this.className='pagebuttonhover'' onmouseout='this.className='pagebutton'' translate=true}
						{html_submit name='cancel' value='Cancel' class='pagebutton' onmouseover='this.className='pagebuttonhover'' onmouseout='this.className='pagebutton'' translate=true}
					</p>
				</div>

			</form>

		</div>

		<div id="languages" class="fragment">

			<form method="post" name="languagesform" id="languagesform">
HI
			</form>

		</div>
	</div>
</div>
{literal}<script>$('#page_tabs').tabs({fxAutoHeight: false});</script>{/literal}