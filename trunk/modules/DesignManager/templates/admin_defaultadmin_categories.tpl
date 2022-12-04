{if isset($list_categories)}
<script type="text/javascript">
$(document).ready(function () {
    $('#categorylist tbody').cmsms_sortable_table({
        actionurl: '{cms_action_url action='ajax_order_cats' forjs=1}&showtemplate=false',
        callback: function(data) {

            var $response = $('<aside/>').addClass('message');

            if (data.status === 'success') {

                $response.addClass('pagemcontainer')
                    .append($('<span>').text('Close').addClass('close-warning'))
                    .append($('<p/>').text(data.message));
            } else if (data.status === 'error') {

                $response.addClass('pageerrorcontainer')
                    .append($('<span>').text('Close').addClass('close-warning'))
                    .append($('<p/>').text(data.message));
            }

            $('body').append($response).slideDown(1000, function() {
                window.setTimeout(function() {
                    $response.slideUp();
                    $response.remove();
                }, 10000);
            });
    	}
    });
    $('#categorylist a.del_cat').click(function(ev){
       var self = $(this);
       ev.preventDefault();
       cms_confirm('{$mod->Lang('confirm_delete_category')|escape:'javascript'}').done(function(){
          window.location = self.attr('href');
       });
    });
});
</script>

{if count($list_categories) > 1}
  <div class="pagewarning" style="display: block;">{$mod->Lang('warning_category_dragdrop')}</div>
{/if}

{/if}

<div class="information">{$mod->lang('info_about_categories')}</div>
<div class="pageoptions">
	{cms_action_url action='admin_edit_category' assign='url'}
	<a id="addcategory" href="{$url}" title="{$mod->Lang('create_category')}">{admin_icon icon='newobject.gif'} {$mod->Lang('create_category')}</a>
</div>

{if isset($list_categories)}
<table id="categorylist" class="pagetable">
	<thead>
		<tr>
			<th width="5%" title="{$mod->Lang('title_cat_id')}">{$mod->Lang('prompt_id')}</th>
			<th title="{$mod->Lang('title_cat_name')}">{$mod->Lang('prompt_name')}</th>
			<th class="pageicon"></th>
			<th class="pageicon"></th>
		</tr>
	</thead>
	<tbody>
	{foreach from=$list_categories item='category'}
		{cycle values="row1,row2" assign='rowclass'}
		{cms_action_url action='admin_edit_category' cat=$category->get_id() assign='edit_url'}
		<tr class="{$rowclass} sortable-table" id="cat_{$category->get_id()}">
			<td><a href="{$edit_url}" title="{$mod->Lang('prompt_edit')}">{$category->get_id()}</a></td>
			<td><a href="{$edit_url}" title="{$mod->Lang('prompt_edit')}">{$category->get_name()}</a></td>
			<td><a href="{$edit_url}" title="{$mod->Lang('prompt_edit')}">{admin_icon icon='edit.gif'}</a></td>
			<td>{cms_action_url action='admin_delete_category' cat=$category->get_id() assign='delete_url'}<a href="{$delete_url}" class="del_cat" title="{$mod->Lang('prompt_delete')}">{admin_icon icon='delete.gif'}</a></td>
		</tr>
	{/foreach}
	</tbody>
</table>
{/if}