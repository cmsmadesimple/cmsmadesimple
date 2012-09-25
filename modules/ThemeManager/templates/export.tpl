{$startform}
{if $itemcount > 0}
<table cellspacing="0" class="pagetable">
	<thead>
		<tr>
			<th>{$idtext}</th>
			<th>{$nametext}</th>
			<th>{$activetext}</th>
			<th>{$defaulttext}</th>
			<th>{$exporttext}</th>
		</tr>
	</thead>
	<tbody>
	{foreach from=$items item=entry}
		<tr class="{$entry->rowclass}">
			<td>{$entry->id}</td>
			<td>{$entry->name}</td>
			<td>{if isset($entry->active)}{$entry->active}{/if}</td>
			<td>{$entry->default}</td>
			<td>{if isset($entry->select)}{$entry->select}{/if}</td>
		</tr>
	{/foreach}
	</tbody>
</table>
<div class="pageoverflow">
  <p class="pagetext">{$prompt_themename}</p>
  <p class="pageinput">{$input_themename}&nbsp;{$info_themename}</p>
</div>
<div class="pageoverflow">
  <p class="pagetext">&nbsp;</p>
  <p class="pageinput">{$submit}</p>
</div>
{/if}
{$endform}
