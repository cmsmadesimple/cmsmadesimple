{if isset($list_themes)}
<table class="pagetable" cellspacing="0">
  <thead>
    <tr>
      <th width="5%">{$mod->Lang('prompt_id')}</th>
      <th>{$mod->Lang('prompt_name')}</th>
      <th class="pageicon"></th>
      <th class="pageicon"></th>
      <th class="pageicon"></th>
    </tr>
  </thead>
  <tbody>
  {foreach from=$list_themes item='theme'}
    {cycle values="row1,row2" assign='rowclass'}
    {cms_action_url action=admin_edit_theme theme=$theme->get_id() assign='edit_url'}
    {cms_action_url action=admin_delete_theme theme=$theme->get_id() assign='delete_url'}
    <tr class="{$rowclass}" onmouseover="this.className='{$rowclass}hover';" onmouseout="this.className='{$rowclass}';">
      <td><a href="{$edit_url}" title="{$mod->Lang('edit_theme')}">{$theme->get_id()}</a></td>
      <td><a href="{$edit_url}" title="{$mod->Lang('edit_theme')}">{$theme->get_name()}</a></td>
      <td><a href="{$edit_url}" title="{$mod->Lang('edit_theme')}">{admin_icon icon='edit.gif'}</a></td>
      <td>EXPORT</td>
      <td><a href="{$delete_url}" title="{$mod->Lang('delete_theme')}">{admin_icon icon='delete.gif'}</a></td>
    </tr>
  {/foreach}
  </tbody>
</table>
{/if}

<div class="pagecontainer">
{cms_action_url action='admin_edit_theme' assign='url'}
<a href="{$url}" title="{$mod->Lang('create_theme')}">{admin_icon icon='newobject.gif'}</a>&nbsp;
<a href="{$url}" title="{$mod->Lang('create_theme')}">{$mod->Lang('create_theme')}</a>
&nbsp;&nbsp;
{cms_action_url action='admin_import_theme' assign='url'}
<a href="{$url}" title="{$mod->Lang('import_theme')}">{admin_icon icon='import.gif'}</a>&nbsp;
<a href="{$url}" title="{$mod->Lang('import_theme')}">{$mod->Lang('import_theme')}</a>
</div>