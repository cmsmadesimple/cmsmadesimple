{if $itemcount > 0}
<div class="pageoptions">
	<p class="pageoptions">{$addlink}</p>
</div>

<table class="pagetable">
	<thead>
		<tr>
			<th>{$templatetext}</th>
			<th>{$usagetext}</th>
			<th align="center" class="pageicon">{$defaulttext}</th>
			<th class="pageicon">{$cachabletext}</th>
			<th class="pageicon">{$importtext}</th>
			<th class="pageicon">{$edittext}</th>
			<th class="pageicon">{$deletetext}</th>
		</tr>
	</thead>
	<tbody>
	{foreach from=$items item=entry}
                {cycle values="row1,row2" assign="rowclass"}
		<tr class="{$rowclass}">
			<td>{if isset($entry->templatelink)}{$entry->templatelink}{else}{$entry->templatename}&nbsp;<em>({$readonlytext})</em>{/if}</td>
			<td>{if $entry->templatename == $default_template}{literal}{menu}{/literal}{else}{literal}{menu template="{/literal}{$entry->templatename}{literal}"}{/literal}{/if}</td>
			<td align="center">
                          {if $entry->templatename == $default_template}
			    {$yesimg}
                          {else if isset($entry->setdefault_link)}
                            {$entry->setdefault_link}
 			  {/if}
            </td>
            <td>{if isset($entry->setcachable_link)}{$entry->setcachable_link}{/if}</td>
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