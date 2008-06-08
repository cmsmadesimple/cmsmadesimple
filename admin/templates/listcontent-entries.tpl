{foreach from=$content item=current}

  {* Some Custom Plugins *}
  {get_content item=$current to=content_item}
  {get_template id=$content_item->template_id to=template}
  {get_edit_permission userid=$content_item->owner_id id=$content_item->id mypages=$mypages modify_any_page=$check_modify_all to=has_edit}
  
		<li class="sortable-element-class">

			{if $current->has_children()}
				{if in_array($content_item->id, $opened_items)}
					<a href="listcontent.php?content_id={$content_item->id}&amp;col=1" onclick="cms_ajax_content_toggleexpand({$content_item->id}, 'true'); return false;">{$contract_image}</a>
				{else}
					<a href="listcontent.php?content_id={$content_item->id}&amp;col=0" onclick="cms_ajax_content_toggleexpand({$content_item->id}, 'false'); return false;">{$expand_image}</a>
				{/if}
			{/if}
			
			<span class="content_name" id="content_span_{$content_item->id}">
				{$content_item->hierarchy()} - 
				{if $has_edit}
					<a href="editcontent.php?content_id={$content_item->id}">{$content_item->get_property_value('name', $language)}</a>
				{else}
					{$content_item->get_property_value('name', $language)}
				{/if}
			</span>
			
			{if $content_item->get_url() ne '' and $content_item->get_url() ne '#'}
				<a href="{$content_item->get_url()}" rel="external">{$view_image}</a>
			{/if}
			
			{if $content_item->default_content eq false and $current->get_children_count() eq 0 and ($modify_page_structure eq true or $remove_pages eq true)}
				<a href="listcontent.php?deletecontent={$content_item->id}" onclick="if (confirm('{lang string='deleteconfirm'}')) cms_ajax_content_delete({$content_item->id}); return false;">{$delete_image}</a>
			{/if}
			
			<br />
			
			Template: &apos;{$template->name}&apos; Content Type: &apos;{$content_item->friendly_name()}&apos;

			{* Handle Any Children of this node *}
			{if count($current->get_children()) gt 0 and in_array($content_item->id, $opened_items)}
				<ul>
					{include file='listcontent-entries.tpl' content=$current->get_children() siblingcount=$current->get_children_count()}
				</ul>
			{/if}
		</li>
  
{/foreach}
