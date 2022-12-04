<div class="pageoverflow">
<table class="pagetable">
  <thead>
    <tr>
      <th width="75%">{$nameprompt}</th>
      <th>{$defaultprompt}</th>
      <th class="pageicon">&nbsp;</th>
      <th class="pageicon">&nbsp;</th>
    </tr>
  </thead>
{foreach from=$items item=entry}
   <tr class="{$entry->rowclass}">
     <td>{$entry->name}</td>
     <td>{$entry->default}</td>
     <td>{$entry->editlink}</td>
     <td>{$entry->deletelink}</td>
   </tr>
{/foreach}
</table>
</div>
<div class="pageoverflow">
  <p class="pageoptions">{$newtemplatelink}</p>
</div>
