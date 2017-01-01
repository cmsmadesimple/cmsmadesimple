<div class="pageoptions">
  <a href="{cms_action_url action=edit_profile}">{admin_icon alt="{$mod->Lang('add_profile')}" title="{$mod->Lang('add_profile')}" icon='newobject.gif'} {$mod->Lang('add_profile')}</a>
</div>
{$mod->Lang('')}
{if !empty($profiles)}
<table class="pagetable">
    <thead>
      <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Tag</th>
        <th>Created</th>
        <th>Last Edited</th>
        <th class="pageicon">&nbsp;</th>
        <th class="pageicon">&nbsp;</th>
      </tr>
    </thead>
    <tbody>
    {foreach $profiles as $profile}
      <tr class="{cycle values='row1,row2'}">
        {cms_action_url action=edit_profile pid=$profile->id assign='edit_url'}
        <td>{$profile->id}</td>
        <td><a href="{$edit_url}" title="{$mod->Lang('edit_profile')}">{$profile->name}</a></td>
        <td>{ldelim}cms_filepicker profile='{$profile->name}'{rdelim}</td>
        <td>{$profile->create_date|cms_date_format}</td>
        <td>{$profile->modified_date|cms_date_format}</td>
        <td><a href="{$edit_url}" class="pageoptions">{admin_icon alt="{$mod->Lang('edit_profile')}" title="{$mod->Lang('edit_profile')}" icon='edit.gif'}</a></td>
        <td><a href="{cms_action_url action=delete_profile pid=$profile->id}" class="pageoptions">{admin_icon alt="{$mod->Lang('delete_profile')}" title="{$mod->Lang('delete_profile')}" icon='delete.gif'}</a></td>
      </tr>
    {/foreach}
    </tbody>
</table>
{/if}