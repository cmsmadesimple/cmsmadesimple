{if isset($reason_for_not_showing)}
    {if $reason_for_not_showing eq 'multiple'}
        Multiple Items Selected
    {elseif $reason_for_not_showing eq 'none'}
        Nothing Selected
    {/if}
{elseif 1 == 1} {* Has edit permissions *}
    <form method="post" action="listcontent.php" id="content_form" onsubmit="cms_ajax_call('cms_ajax_save_page', $(this).serializeArray()); return false;">

    	{$theme_object->start_tab_headers()}
        {$theme_object->set_tab_header('info', 'Information')}
        {$theme_object->set_tab_header('edit', 'Content')}
        {$theme_object->set_tab_header('attributes', 'Attributes')}
        {$theme_object->set_tab_header('metadata', 'Metadata')}
        {$theme_object->set_tab_header('preview', 'Preview')}
    	{$theme_object->end_tab_headers()}
    	{$theme_object->start_tab_content()}
    	{$theme_object->start_tab('info')}
    		{include file="editcontent-info.tpl"}
    	{$theme_object->end_tab()}
    	{$theme_object->start_tab('edit')}
    		{include file="editcontent-content.tpl"}
    	{$theme_object->end_tab()}
    	{$theme_object->start_tab('attributes')}
    		{include file="editcontent-attributes.tpl"}
    	{$theme_object->end_tab()}
    	{$theme_object->start_tab('metadata')}
    		{include file="editcontent-metadata.tpl"}
    	{$theme_object->end_tab()}
    	{$theme_object->start_tab('preview')}
    	{$theme_object->end_tab()}
    	{$theme_object->end_tab_content()}
    	<br />
        
        {if $page.id > -1}
    	    {html_hidden name='page[id]' value=$page.id}
    	{/if}
    	{html_hidden name='serialized_page' id='serialized_page' value=$serialized_page}

    	{html_submit name="save" value="Save" remote="save_page"} {html_submit name="cancel" value="Cancel" remote="save_page"} {html_submit name="apply" value="Apply" remote="save_page"}

    </form>
{else}
    <div class="pageoverflow">
    	<p class="pagetext">Id:</p>
    	<p class="pageinput">{$content->id}</p>
    </div>
    <div class="pageoverflow">
    	<p class="pagetext">Name:</p>
    	<p class="pageinput">{$content->name}</p>
    </div>
    <div class="pageoverflow">
    	<p class="pagetext">Menu Text:</p>
    	<p class="pageinput">{$content->menu_text}</p>
    </div>
    <div class="pageoverflow">
    	<p class="pagetext">Page Alias:</p>
    	<p class="pageinput">{$content->alias}</p>
    </div>
    <div class="pageoverflow">
    	<p class="pagetext">Type:</p>
    	<p class="pageinput">{$content->type}</p>
    </div>
    <div class="pageoverflow">
    	<p class="pagetext">Path:</p>
    	<p class="pageinput">/{$content->hierarchy_path}</p>
    </div>
    <div class="pageoverflow">
        <p class="pagetext"></p>
        <p class="pageinput">
          <a href="editcontent.php{$urlext}&amp;page_id={$content->id}">Edit</a> 
          <a href="listcontent.php{$urlext}&amp;deletecontent={$content->id}">{$delete_image} Delete</a>
        </p>
    </div>
{/if}