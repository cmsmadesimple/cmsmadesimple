{if isset($list_categories)}
<script type="text/javascript">
$(document).ready(function () {
    cms_initSortable('#categorylist', '{cms_action_url action='ajax_order_cats' forjs=1}&showtemplate=false')
});
</script>

<div class="pagewarning">{$mod->Lang('warning_category_dragdrop')}</div>

<table id="categorylist" class="pagetable">
	<thead>
		<tr>
			<th width="5%">{$mod->Lang('prompt_id')}</th>
			<th>{$mod->Lang('prompt_name')}</th>
			<th class="pageicon"></th>
			<th class="pageicon"></th>
		</tr>
	</thead>
	<tbody>
	{foreach from=$list_categories item='category'}
		{cycle values="row1,row2" assign='rowclass'}
		{cms_action_url action='admin_edit_category' cat=$category->get_id() assign='edit_url'}
		<tr class="{$rowclass}"id="cat_{$category->get_id()}">
			<td><a href="{$edit_url}" title="{$mod->Lang('prompt_edit')}">{$category->get_id()}</a></td>
			<td><a href="{$edit_url}" title="{$mod->Lang('prompt_edit')}">{$category->get_name()}</a></td>
			<td><a href="{$edit_url}" title="{$mod->Lang('prompt_edit')}">{admin_icon icon='edit.gif'}</a></td>
			<td>{cms_action_url action='admin_delete_category' cat=$category->get_id() assign='delete_url'}<a href="{$delete_url}" title="{$mod->Lang('prompt_delete')}" onclick="return confirm('{$mod->Lang('confirm_delete_category')}');">{admin_icon icon='delete.gif'}</a></td>
		</tr>
	{/foreach}
	</tbody>
</table>
{/if}

<div class="pagecontainer">
	{cms_action_url action='admin_edit_category' assign='url'}
	<a href="{$url}" title="{$mod->Lang('create_category')}">{admin_icon icon='newobject.gif'}</a>&nbsp;
	<a href="{$url}" title="{$mod->Lang('create_category')}">{$mod->Lang('create_category')}</a>
</div>