<style>
.alias {
   color: maroon;
}
</style>

<div class="c_full cf">
   <a href="{cms_action_url action=admin_edit_category}">{admin_icon icon='newobject'} {$mod->Lang('add_category')}</a>
   {if !empty($categories) && count($categories) > 1}
       <a href="{cms_action_url action=admin_order_categories}">{admin_icon icon='reorder'} {$mod->Lang('reorder_categories')}</a>
   {/if}
</div>

{if empty($categories)}
   <div class="information">{$mod->Lang('warn_nocategories')}</div>
{else}
   <table class="pagetable">
      <thead>
         <tr>
	    <th>{$mod->Lang('category')}</th>
	    <th class="pageicon">{* edit *}</th>
	    <th class="pageicon">{* delete *}</th>
	 </tr>
      </thead>
      <tbody>
         {foreach $categories as $cat}
	    {cms_action_url action=admin_edit_category catid=$cat->id assign=edit_url}
	    <tr class="{cycle values='row1,row2'}">
	       <td>{repeat string='&nbsp;&gt;&nbsp;' times=$cat->depth}
	           <a href="{$edit_url}" title="{$mod->Lang('t_edit_category')}">
                     {$cat->name}
		   </a>
	           {if $cat->alias}<em class="alias" title="{$mod->Lang('t_cat_alias')}">({$cat->alias})</em>{/if}
	       </td>
	       <td>
	          <a href="{$edit_url}" title="{$mod->Lang('t_edit_category')}">{admin_icon icon='edit'}</a>
	       </td>
	       <td>
  	          <a href="{cms_action_url action=admin_del_category catid=$cat->id}" title="{$mod->Lang('t_del_category')}">{admin_icon icon='delete'}</a>
	       </td>
	    </tr>
	 {/foreach}
      </tbody>
   </table>
{/if}
