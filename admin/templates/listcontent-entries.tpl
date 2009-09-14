{foreach from=$content item=current}

    {* Some Custom Plugins *}
    {get_content item=$current to=content_item}
    {get_template id=$content_item->template_id to=template}
    {get_user id=$content_item->owner_id to=user}
    {get_edit_permission userid=$content_item->owner_id id=$content_item->id mypages=$mypages modify_any_page=$check_modify_all to=has_edit}
    
    <li id="phtml_{$content_item->id}"><a href="#">{$content_item->menu_text}</a>

        {if $current->has_children()}
            <ul>
                {include file='listcontent-entries.tpl' content=$current->get_children()}
            </ul>
        {/if}
    
    </li>
  
  {* Do a row *}
  {*
  <tr id="tr_{$content_item->id}" class="reorderable, {cycle name="1" values="row1,row2"}" onmouseover="this.className='{cycle name="2" values="row1,row2"}hover';" onmouseout="this.className='{cycle name="3" values="row1,row2"}';">
  *}
    
    {* Collapse/Expand Links *}
    {*
    <td width="12">
      {if $current->has_children()}
        {if in_array($content_item->id, $opened_items)}
          <a href="{$thisurl}&content_id={$content_item->id}&amp;col=1" onclick="cms_ajax_content_toggleexpand({$content_item->id}, 'true'); return false;">{$contract_image}</a>
        {else}
          <a href="{$thisurl}&content_id={$content_item->id}&amp;col=0" onclick="cms_ajax_content_toggleexpand({$content_item->id}, 'false'); return false;">{$expand_image}</a>
        {/if}
      {/if}
    </td>
    *}
    
    
    {* Basic Data Fields *}
    {*
    <td class="hierarchy">{$content_item->hierarchy()}</td>
    <td>
      {if $has_edit}
        <a href="editcontent.php{$urlext}&content_id={$content_item->id}">{$content_item->name}</a>
      {else}
        {$content_item->name}
      {/if}
    </td>
    <td>{$template->name}</td>
    <td>{$content_item->friendly_name()}</td>
    <td>{$user->username}</td>
    *}

    
    {* Active *}
    {*
    {if $modify_page_structure eq true}
      {if $content_item->active eq true}
        {if $content_item->default_content eq true}
          <td class="pagepos">{$true_image}</td>
        {else}
          <td class="pagepos"><a href="listcontent.php{$urlext}&setinactive={$content_item->id}" onclick="cms_ajax_content_setinactive({$content_item->id}); return false;">{$setfalse_image}</a></td>
        {/if}
      {else}
        <td class="pagepos"><a href="listcontent.php{$urlext}&setactive={$content_item->id}" onclick="cms_ajax_content_setactive({$content_item->id}); return false;">{$settrue_image}</a></td>
      {/if}
    {/if}
    *}

    
    {* Default *}
    {*
    {if $content_item->is_default_possible() and ($has_edit or $modify_page_structure)}
      {if $content_item->default_content eq true}
        <td class="pagepos">{$true_image}</td>
      {else}
        <td class="pagepos"><a href="listcontent.php{$urlext}&makedefault={$content_item->id}" onclick="if(confirm('{lang string="confirmdefault"}')) cms_ajax_content_setdefault({$content_item->id}); return false;">{$settrue_image}</a></td>
      {/if}
    {else}
      <td>&nbsp;</td>
    {/if}
    *}
    
    
    {* Move *}
    {*
    {if $modify_page_structure eq true}
      <td class="move">
        {if $siblingcount gt 1}
          {if $content_item->item_order - 1 lt $siblingcount - 1}
            <a onclick="cms_ajax_content_move({$content_item->id}, {$content_item->parent_id}, 'down'); return false;" href="listcontent.php{$urlext}&direction=down&amp;content_id={$content_item->id}&amp;parent_id={$content_item->parent_id}">{$down_image}</a>
          {/if}
          {if $content_item->item_order - 1 gt 0}
            <a onclick="cms_ajax_content_move({$content_item->id}, {$content_item->parent_id}, 'up'); return false;" href="listcontent.php{$urlext}&direction=up&amp;content_id={$content_item->id}&amp;parent_id={$content_item->parent_id}">{$up_image}</a>
          {/if}
        {/if}
      </td>
      <td class="invisibleme" style="text-align: center;">
        <input type="text" name="order-{$content_item->id}" value="{$content_item->item_order}" class="order" />
      </td>
    {/if}
    *}
    

    {* URL *}
    {*
    <td class="pagepos">
      {if $content_item->get_url() ne '' and $content_item->get_url() ne '#'}
        <a href="{$content_item->get_url()}" rel="external">{$view_image}</a>
      {/if}
    </td>
    *}
    
    
    {* Edit *}
    {*
    <td class="pagepos">
      {if $has_edit eq true}
        <a href="editcontent.php{$urlext}&content_id={$content_item->id}">{$edit_image}</a>
      {/if}
    </td>
    *}
    
    
    {* Delete *}
    {*
    <td class="pagepos">
      {if $content_item->default_content eq false and $current->get_children_count() eq 0 and ($modify_page_structure eq true or $remove_pages eq true)}
        <a href="listcontent.php{$urlext}&deletecontent={$content_item->id}" onclick="if (confirm('{lang string='deleteconfirm'}')) cms_ajax_content_delete({$content_item->id}); return false;">{$delete_image}</a>
      {/if}
    </td>
    *}


    {* Checkbox *}
    {*
    <td class="checkbox">
      {if $content_item->default_content eq false}
        <input type="checkbox" name="multicontent-{$content_item->id}" />
      {/if}
    </td>
  </tr>
  *}
  

  {* Handle Any Children of this node *}
  {*
  {if count($current->get_children()) gt 0 and in_array($content_item->id, $opened_items)}
    {include file='listcontent-entries.tpl' content=$current->get_children() siblingcount=$current->get_children_count()}
  {/if}
  *}
{/foreach}
