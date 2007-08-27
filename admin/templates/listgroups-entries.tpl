{foreach from=$groups item='current'}
  {cycle values='row1,row2' assign='currow'}
  <tr class="{$currow}" onouseover="this.className='{$currow}hover';" onmouseout="this.className='{$currow}';">
    <td>
      {if $modify_groups eq true}
        <a href="editgroup.php?group_id={$current->id}">
      {/if}
      {$current->name}
      {if $modify_groups eq true}
        </a>
      {/if}
    </td>

    <td class="pagepos">
      {if $current->active eq 1}
        {adminicon icon='true.gif' alt_lang='yes'}
      {else}
	{adminicon icon='false.gif' alt_lang='no'}
      {/if}
    </td>

    {if $modify_permissions eq true}
      <td class="pagepos icons_wide">
        <a href="changegroupperm.php?group_id={$current->id}">{adminicon icon='permissions.gif' alt_lang='permissions'}</a>
      </td>
    {/if}

    {if $modify_group_assignments eq true}
      <td class="pagepos icons_wide">
        <a href="changegroupassign.php?group_id={$current->id}">{adminicon icon='groupassign.gif' alt_lang='assignments'}</a>
      </td>
    {/if}

    {if $modify_groups eq true}
      <td class="icons_wide">
      {if $current->id != 1 && $current->name != 'Anonymous'}
          <a href="editgroup.php?group_id={$current->id}">{adminicon icon='edit.gif' alt_lang='edit'}</a>
      {else}
           &nbsp;
      {/if}
      </td>
    {/if}
  
    {if $remove_groups eq true}
      <td class="icons_wide">
      {if $current->id != 1 && $current->name != 'Anonymous'}
        <a href="deletegroup.php?group_id={$current->id}" onclick="return confirm('{tr}deleteconfirm{/tr} - {$current->name} - ?');">{adminicon icon='delete.gif' alt_lang='delete'}</a>
      {else}
        &nbsp;
      {/if}
      </td>
    {/if}  
  </tr>
{/foreach}