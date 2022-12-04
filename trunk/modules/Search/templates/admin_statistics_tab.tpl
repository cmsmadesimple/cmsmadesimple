{* admin statistics tab *}

{if isset($topwords)}
{$formstart}
<div class="pageoverflow">
  <table class="pagetable">
    <thead>
      <tr>
        <th width="75%">{$wordtext}</th>
        <th width="25%">{$counttext}</th>
      </tr>
    </thead>
    <tbody>
    {foreach from=$topwords item='entry'}
      {cycle assign='rowclass' values='row1,row2'}
      <tr class="{$rowclass}">
        <td>{$entry.word}</td>
        <td>{$entry.count}</td>
      </tr>
    {/foreach}
    </tbody>
  </table>
</div>
<div class="pageoverflow">
  <p class="pagetext">&nbsp;</p>
  <p class="pageinput">{$clearwordcount}&nbsp;{$exportcsv}</p>
</div>
{$formend}
{else}
<div class="information">{lang_by_realm('Search','nostatistics')}</div>
{/if}