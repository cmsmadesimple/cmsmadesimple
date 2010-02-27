{if isset($reason_for_not_showing)}
    {if $reason_for_not_showing eq 'multiple'}
        Multiple Items Selected
    {elseif $reason_for_not_showing eq 'none'}
        Nothing Selected
    {/if}
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