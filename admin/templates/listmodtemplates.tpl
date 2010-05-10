<div class="pagecontainer">
  <div class="pageoverflow">
    {$header_name}
  </div><!-- pageoverflow -->

  <div id="modulelist">
	{foreach from=$templates key=module_name item=template_types}
		
		<h3>{$module_name}</h3>
		
		{foreach from=$template_types key=template_name item=templates}
		
			<h4>{$template_name} (<a href="addmodtemplate.php?module_name={$module_name}&amp;template_type={$template_name}">{tr}add_template{/tr}</a>)</h4>
			<table>
				<tbody>
				{foreach from=$templates item=template}
					<tr>
						<td>{$template->name}</td>
						<td>
						{if $template->default eq 1}
							{adminicon icon='true.gif' alt_lang='true'}
						{else}
							<a href="listmodtemplates.php?make_default={$template->id}">{adminicon icon='false.gif' alt_lang='false'}</a>
						{/if}
						</td>
						<td><a href="editmodtemplate.php?template_id={$template->id}">{tr}edit{/tr}</a></td>
						{if $template->default eq 1}
							<td>&nbsp;</td>
						{else}
							<td><a href="listmodtemplates.php?delete_template={$template->id}">{tr}delete{/tr}</a></td>
						{/if}
					</tr>
				{/foreach}
				</tbody>
			</table>

			<br />

		{/foreach}
		
		<hr />
		
	{/foreach}
  </div><!-- modulelist -->

</div><!-- pagecontainer -->

<p class="pageback"><a class="pageback" href="{$back_url}">&#171; {lang string='back'}</a></p>
