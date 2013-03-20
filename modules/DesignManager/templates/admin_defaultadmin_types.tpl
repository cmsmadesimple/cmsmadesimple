{if isset($list_all_types)}
<table class="pagetable">
  <thead>
    <tr>
      <th width="5%">{$mod->Lang('prompt_id')}</th>
      <th>{$mod->Lang('prompt_name')}</th>
      {if $has_add_right}
      <th class="pageicon"></th>
      {/if}
      <th class="pageicon"></th>
    </tr>
  </thead>
  <tbody>
  {foreach from=$list_all_types item='type'}
   {cycle values="row1,row2" assign='rowclass'}
   {assign var='reset_url' value=''}
   {if $type->get_dflt_flag()}
     {cms_action_url action='admin_reset_type' type=$type->get_id() assign='reset_url'}
   {/if}
   {cms_action_url action='admin_edit_type' type=$type->get_id() assign='edit_url'}
   <tr class="{$rowclass}" onmouseover="this.className='{$rowclass}hover';" onmouseout="this.className='{$rowclass}';">
      <td>{$type->get_id()}</td>
      <td>
        <a href="{$edit_url}" title="{$mod->Lang('prompt_edit')}">{$type->get_langified_display_value()}</a>
      </td>
      {if $has_add_right}
      <td>{cms_action_url action=admin_edit_template import_type=$type->get_id() assign='create_url'}
        <a href="{$create_url}" title="{$mod->Lang('prompt_import')}">{admin_icon icon='import.gif'}</a>
      </td>
      {/if}
      <td><a href="{$edit_url}" title="{$mod->Lang('prompt_edit')}">{admin_icon icon='edit.gif'}</a></td>
    </tr>
  {/foreach}
  </tbody>
</table>
{/if}