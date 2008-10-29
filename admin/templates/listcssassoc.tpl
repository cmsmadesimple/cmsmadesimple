<div class="pageoverflow">
  <p class="pagetext">{$text_template}</p>
  <p class="pageinput">{$edittemplate_link}</p>
</div>

{if isset($cssassoc)}
<table cellspacing="0" class="pagetable">
  <thead>
  <tr>
    <th>{$text_title}</th>
    <th class="pageicon" colspan="2">{$text_move}</th>
    <th class="pageicon">&nbsp;{* edit *}</th>
    <th class="pageicon">&nbsp;{* delete *}</th>
 </tr>
  </thead>
  <tbody>
  {foreach from=$cssassoc item='one'}
    {cycle values='row1,row2' assign='currow'}
    <tr class="{$currow}" onmouseover="this.className='{$currow}.hover';" onmouseout="this.className='{$currow}';">
      <td>{$one.editlink}</td>
      <td>{if isset($one.downlink)}{$one.downlink}{/if}</td>
      <td>{if isset($one.uplink)}{$one.uplink}{/if}</td>
      <td>{$one.editimg}</td>
      <td>{if isset($one.deletelink)}{$one.deletelink}{/if}</td>
    </tr>
  {/foreach}
  </tbody>
</table>
{/if}

{if isset($formstart)}
{$formstart}
<div class="pageoverflow">
  <p class="pageoptions">
    {$dropdown}
    <div>{$hidden}</div>
    {$submit}
  </p>
</div>
</form>
{/if}