{* missing module dependencies *}
<div class="pageheader">
{'depsformodule'|lang:$module}
</div>

<table class="pagetable" cellspacing="0">
  <thead>
    <tr>
      <th>{'name'|lang}</th>
      <th>{'minimumversion'|lang}</th>
      <th>{'installed'|lang}</th>
    </tr>
  </thead>
  <tbody>
  {foreach from=$deps item='one'}
    {cycle assign='rowclass' values='row1,row2'}
    <tr class="{$rowclass}" onmouseover="this.className='{$rowclass}hover';" onmouseout="this.className='{$rowclass}';">
      <td>{$one.name}</td>
      <td>{$one.version}</td>
      <td>{if $one.installed}{'yes'|lang}{else}{'no'|lang}{/if}</td>
    </tr>
  {/foreach}
  </tbody>
</table>