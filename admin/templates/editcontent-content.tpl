{if $page->template}
	{foreach from=$page->template->get_page_blocks() key='name' item='block' name='foo'}
		{assign var=block_obj value=$page->get_content_block($name, true)}
		{if $block_obj}
			<h3>{$name}</h3>
			<label>Content Type:</label> {html_options class='content_type_picker' id="block_select_`$smarty.foreach.foo.index`" name="block_type[$name]" options=$content_types selected=$block_obj.content_type}<br />
			<div id="block_{$smarty.foreach.foo.index}">
				{$block_obj->get_edit_form($name)}
			</div>
			{if $block_obj->id}
			    <input type="hidden" name="block[{$name}][id]" value="{$block_obj.id}" />
			{/if}
		{/if}
	{/foreach}
{/if}
