{literal}
<script type="text/javascript">
//<![CDATA[
function selectAll()
{
  checkboxes = document.getElementsByTagName("input");
  for (i=0; i<checkboxes.length ; i++)
    {
      if (checkboxes[i].type == "checkbox") checkboxes[i].checked=!checkboxes[i].checked;
    }
}
//]]>
</script>
{/literal}

{if isset($formstart) }
<fieldset>
  <legend>{$filtertext}</legend>
  {$formstart}
  <div class="pageoverflow">
    <p class="pagetext">{$prompt_category}:</p>
    <p class="pageinput">{$input_category} {$prompt_showchildcategories}: {$input_allcategories}</p>
  </div>
  <div class="pageoverflow">
    <p class="pagetext">{$prompt_sorting}:</p>
    <p class="pageinput">{$input_sorting}</p>
  </div>
  <div class="pageoverflow">
    <p class="pagetext">{$prompt_pagelimit}:</p>
    <p class="pageinput">{$input_pagelimit}</p>
  </div>
  <div class="pageoverflow">
    <p class="pagetext">&nbsp;</p>
    <p class="pageinput">{$submitfilter}{$hidden|default:''}</p>
  </div>
  {$formend}
</fieldset>
{/if}

{if $itemcount > 0}
<div class="pageoptions">
{if isset($addlink)}<p class="pageoptions">{$addlink}</p>{/if}
</div>

{if $pagecount > 1}
  <p>
{if $pagenumber > 1}
{$firstpage}&nbsp;{$prevpage}&nbsp;
{/if}
{$pagenumber}&nbsp;{$oftext}&nbsp;{$pagecount}
{if $pagenumber < $pagecount}
&nbsp;{$nextpage}&nbsp;{$lastpage}
{/if}
</p>
{/if}

{$form2start}
<table cellspacing="0" class="pagetable">
	<thead>
		<tr>
			<th>{$titletext}</th>
			<th>{$postdatetext}</th>
                        <th>{$startdatetext}</th>
                        <th>{$enddatetext}</th>
			<th>{$categorytext}</th>
			<th class="pageicon">{$statustext}</th>
			<th class="pageicon">&nbsp;</th>
			<th class="pageicon">&nbsp;</th>
			<th class="pageicon"><a href="javascript:selectAll();">{$selecttext}</a></th>
		</tr>
	</thead>
	<tbody>
	{foreach from=$items item=entry}
		<tr class="{$entry->rowclass}">
			<td>{$entry->title}</td>
			<td>{$entry->u_postdate|cms_date_format}</td>
                        <td>{if !empty($entry->u_enddate)}{$entry->u_startdate|cms_date_format}{/if}</td>
                        <td>{if $entry->expired == 1}
                              <div class="important">
                              {$entry->u_enddate|cms_date_format}
	                      </div>
                            {else}
                              {$entry->u_enddate|cms_date_format}
                            {/if}
                        </td>
			<td>{$entry->category}</td>
			<td>{if isset($entry->approve_link)}{$entry->approve_link}{/if}</td>
			<td>{$entry->editlink|default:''}</td>
			<td>{if isset($entry->deletelink)}{$entry->deletelink}{/if}</td>
			<td>{$entry->select}</td>
		</tr>
	{/foreach}
	</tbody>
</table>
{/if}

<div style="width: 97%;">
{if isset($addlink)}
  <div class="pageoptions" style="float: left;">
    <p class="pageoptions">{$addlink}</p>
  </div>
{/if}
{if $itemcount > 0}
  <div class="pageoptions" style="float: right;">
    {$reassigntext}:&nbsp;{$categoryinput}{$submit_reassign}{if isset($submit_massdelete)}<br/>{$submit_massdelete}{/if}
  </div>
{/if}
<div class="clearb"></div>
</div>
{$form2end}
