{mod_form action='defaultadmin'}
  <p>
    {tr}Category{/tr}: {mod_dropdown name='current_category' items=$cms_mapi_module->get_categories(true) selected_value=$current_category}
    {tr}Status{/tr}: {mod_dropdown name='current_status' items=$cms_mapi_module->get_statuses(true) selected_value=$current_status}
	{mod_hidden name='selected_tab' value='manageposts'}
    {mod_submit name='submit_filter' value='Filter' translate=true}
  </p>
{/mod_form}

{if count($posts) > 0}
  <table cellspacing="0" class="pagetable">
  	<thead>
  		<tr>
  			<th>{tr}Title{/tr}</th>
  			<th>{tr}Post Date{/tr}</th>
  			<th>{tr}Category{/tr}</th>
  			<th>{tr}Author{/tr}</th>
			<th>{tr}Status{/tr}</th>
  			<th class="pageicon">&nbsp;</th>
  			<th class="pageicon">&nbsp;</th>
  		</tr>
  	</thead>
  	<tbody>
    	{foreach from=$posts item=entry}
    		<tr class="{cycle values='row1,row2' advance=false name='post'}" onmouseover="this.className='{cycle values='row1,row2' advance=false name='article'}hover';" onmouseout="this.className='{cycle values='row1,row2' name='post'}';">
    			<td>{$entry->title}</td>
    			<td>{$entry->post_date}</td>
    			<td>Categories w/ Links</td>
				<td>Author</td>
				<td>{mod_lang string=$entry->status}</td>
    			<td>{mod_link action='editpost' value='Edit Post' blog_post_id=$entry->id theme_image='icons/system/edit.gif' translate=true}</td>
    			<td>{mod_link action='deletepost' value='Delete Post' blog_post_id=$entry->id theme_image='icons/system/delete.gif' translate=true}</td>
    		</tr>
    	{/foreach}
  	</tbody>
  </table>
{else}
	<p><strong>{tr}noposts{/tr}</strong></p>
{/if}
