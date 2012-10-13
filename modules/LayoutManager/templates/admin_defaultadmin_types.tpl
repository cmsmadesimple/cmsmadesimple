{if isset($list_all_types)}
<table class="pagetable" cellspacing="0">
  <thead>
    <tr>
      <th width="5%">{$mod->Lang('prompt_id')}</th>
      <th>{$mod->Lang('prompt_name')}</th>
      <th class="pageicon"></th>
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
   {cms_action_url action='admin_view_type' type=$type->get_id() assign='view_url'}
   <tr class="{$rowclass}" onmouseover="this.className='{$rowclass}hover';" onmouseout="this.className='{$rowclass}';">
      <td>{$type->get_id()}</td>
      <td>
        <a href="{$view_url}" title="{$mod->Lang('prompt_view')}">{$type->get_langified_display_value()}</a>
      </td>
      <td><a href="{$view_url}"" title="{$mod->Lang('prompt_view')}">{admin_icon icon='view.gif'}</a></td>
      <td>
      {if $reset_url}
        <a href="{$reset_url}" title="{$mod->Lang('prompt_reset')}" onclick="return confirm('{$mod->Lang('confirm_reset_type')}')">{admin_icon icon='import.gif'}</a>
      {/if}
      </td>
    </tr>
  {/foreach}
  </tbody>
</table>
{/if}