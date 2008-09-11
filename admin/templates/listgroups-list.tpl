<div class="pageoverflow">
  <p class="pageoptions">
    {if $modify_groups eq true}
      <a href="addgroup.php" class="pageoptions">{adminicon icon='newobject.gif' alt_lang='addgroup'}</a>
      <a href="addgroup.php" class="pageoptions">{tr}addgroup{/tr}</a>
    {/if}
  </p>
</div><!-- pageoverflow -->

<table class="pagetable">
  <thead>
    <tr>
      <th class="pagew60">{tr}name{/tr}</th>
      <th class="pagepos">{tr}active{/tr}</th>
      {if $modify_permissions eq true}
        <th class="pageicon">&nbsp;</th>
      {/if}
      {if $modify_group_assignments eq true}
        <th class="pageicon">&nbsp;</th>
      {/if}
      {if $modify_groups eq true}
        <th class="pageicon">&nbsp;</th>
      {/if}
      {if $remove_groups eq true}
        <th class="pageicon">&nbsp;</th>
      {/if}
    </tr>
  </thead>
  <tbody>
    {if count($groups) gt 0}
      {include file='listgroups-entries.tpl' groups=$groups}
    {/if}
  </tbody>
</table>

<div class="pageoverflow">
  <p class="pageoptions">
    {if $modify_groups eq true}
      <a href="addgroup.php" class="pageoptions">{adminicon icon='newobject.gif' alt_lang='addgroup'}</a>
      <a href="addgroup.php" class="pageoptions">{tr}addgroup{/tr}</a>
    {/if}
  </p>
</div><!-- pageoverflow -->
