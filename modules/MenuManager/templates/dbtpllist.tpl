{if $itemcount > 0}
<div class="pageoptions">
	<p class="pageoptions">{$addlink}</p>
</div>

<table cellspacing="0" class="pagetable">
	<thead>
		<tr>
			<th>{$templatetext}</th>
			<th align="center" class="pageicon">{$defaulttext}</th>
			<th class="pageicon">&nbsp;</th>
			<th class="pageicon">&nbsp;</th>
			<th class="pageicon">&nbsp;</th>
		</tr>
	</thead>
	<tbody>
	{foreach from=$items item=entry}
                {cycle values="row1,row2" assign="rowclass"}
		<tr class="{$rowclass}" onmouseover="this.className='{$rowclass}hover';" onmouseout="this.className='{$rowclass}';">
			<td>{if isset($entry->templatelink)}{$entry->templatelink}{else}{$entry->templatename}&nbsp;<em>({$readonlytext})</em>{/if}</td>
			<td align="center">
                          {if $entry->templatename == $default_template}
			    {$yesimg}
                          {else if isset($entry->setdefault_link)}
                            {$entry->setdefault_link}
 			  {/if}
                        </td>
			<td>{if isset($entry->importlink)}{$entry->importlink}{/if}</td>
			<td>{if isset($entry->editlink)}{$entry->editlink}{/if}</td>
			<td>{if isset($entry->deletelink)}{$entry->deletelink}{/if}</td>
		</tr>
	{/foreach}
	</tbody>
</table>
{/if}
<div class="pageoptions">
	<p class="pageoptions">{$addlink}</p>
{*TODO check what happen with this div's stuff'*}
{*</div>*}
