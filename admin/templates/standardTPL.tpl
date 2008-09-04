{*
standard "skeleton" TPL for new pages stuff in admin side
*}

{validation_errors for=$foo_object}
<div class="pagecontainer">
	<div class="pageoverflow">
		{$header_name}
	</div><!-- pageoverflow -->

	
		<table class="pagetable">
		<thead>
		<tr>
		<th class="pagew60">{tr}name{/tr}</th>
		<th class="pagepos">{tr}url{/tr}</th>
		<th class="pageicon">&nbsp;</th>
		<th class="pageicon">&nbsp;</th>
		</tr>
		</thead>
		<tbody>
     {foreach from=$foo item=keyfoo name=keyfoo }
     {cycle values='row1,row2' assign='currow'} 
  <tr class="{$currow}">
				<td>&nbsp;</td>
				<td>{$onemark->url}</td>
				<td><a href="foo">
                {adminicon icon='edit.gif' alt_lang='edit'}
                 </a></td>
				<td>
                <a href="foo" onclick="return confirm('{tr}deleteconfirm{/tr} - {$foo} - ?');">
                {adminicon icon='delete.gif' alt_lang='delete'}
                </a></td>
				</tr>
 {/foreach}

		</tbody>
		</table>

</div><!-- pagecontainer -->

<p class="pageback"><a class="pageback" href="{$back_url}">&#171; {lang string='back'}</a></p>
