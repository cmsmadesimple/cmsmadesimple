<script type="text/javascript">
//<![CDATA[
$(document).ready(function(){
  cms_checkAll('#selall');
  $('#filter').hide();
  $('#toggle_filter').click(function(){
    $('#filter').toggle('slow');
  });
  $('#bulkactions').hide();
  $('#articlelist').on('cms_checkall_toggle','[type=checkbox]',function(){
    var l = $('#articlelist :checked').length;
    if( l == 0 ) {
      $('#bulkactions').hide('slow');
    } else {
      $('#bulkactions').show('slow');
    }
  });
  $('#bulk_category').hide();
  $('#bulk_action').on('change',function(){
    var v = $(this).val();
    if( v == 'setcategory' ) {
      $('#bulk_category').show('slow');
    }
    else {
      $('#bulk_category').hide('slow');
    }
  });
  $('#bulkactions').on('click','#submit_bulkaction',function(){
    return confirm('{$mod->Lang('areyousure_multiple')}');
  });
});
//]]>
</script>

{if isset($formstart) }
<fieldset id="filter">
  <legend>{$filtertext}</legend>
  {$formstart}
  <div class="pageoverflow">
    <p class="pagetext"><label for="filter_category">{$prompt_category}:</label></p>
    <p class="pageinput">
      <select id="filter_category" name="{$actionid}category">
      {html_options options=$categorylist selected=$curcategory}
      </select>
      <label for="filter_allcategories">{$prompt_showchildcategories}:</label>
      <input id="filter_allcategories" type="checkbox" name="{$actionid}allcategories" value="yes" {if $allcategories=="yes"}checked="checked"{/if}>
    </p>
  </div>
  <div class="pageoverflow">
    <p class="pagetext"><label for="filter_sortby">{$prompt_sorting}:</label></p>
    <p class="pageinput">
      <select id="filter_sorting" name="{$actionid}sortby">
      {html_options options=$sortlist selected=$sortby}
      </select>
    </p>
  </div>
  <div class="pageoverflow">
    <p class="pagetext"><label for="filter_pagelimit">{$prompt_pagelimit}:</label></p>
    <p class="pageinput">
      <select id="filter_pagelimit" name="{$actionid}pagelimit">
      {html_options options=$pagelimits selected=$pagelimit}
      </select>
    </p>
  </div>
  <div class="pageoverflow">
    <p class="pagetext">&nbsp;</p>
    <p class="pageinput">
      <input type="submit" name="{$actionid}submitfilter" value="{$mod->Lang('submit')}"/>
      <input type="submit" name="{$actionid}resetfilter" value="{$mod->Lang('reset')}"/>
    </p>
  </div>
  {$formend}
</fieldset>
{/if}

{if $itemcount > 0}
<div class=row">
  <div class="pageoptions half" style="margin-top: 8px;">
    <input type="checkbox" id="toggle_filter" value="1"/>
    <label for="toggle_filter">{$mod->Lang('viewfilter')}</label>
    {if isset($addlink)}&nbsp;{$addlink}{/if}
  </div>

  {if $pagecount > 1}
    <div class="pageoptions" style="text-align: right;">
      {form_start}
      {$mod->Lang('prompt_page')}&nbsp;
      <select name="{$actionid}pagenumber">
        {cms_pageoptions numpages=$pagecount curpage=$pagenumber}
      </select>&nbsp;
      <input type="submit" name-"{$actionid}paginate" value="{$mod->Lang('prompt_go')}"/>
      {form_end}
    </div>
  {/if}
</div>{* .row *}

{$form2start}
<table cellspacing="0" class="pagetable" id="articlelist">
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
			<th class="pageicon"><input type="checkbox" id="selall" value="1" title="{$mod->Lang('selectall')}"/></th>
		</tr>
	</thead>
	<tbody>
	{foreach from=$items item=entry}
		<tr class="{$entry->rowclass}">
			<td>
                        {if isset($entry->edit_url)}
                          <a href="{$entry->edit_url}" title="{$mod->Lang('editarticle')}">{$entry->news_title|cms_escape}</a>
                        {else}
                          {$entry->news_title|cms_escape}
                        {/if}
                        </td>
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
			<td>
                          {if isset($entry->edit_url)}
                          <a href="{$entry->edit_url}" title="{$mod->Lang('editarticle')}">{admin_icon icon='edit.gif'}</a>
                          {/if}
                        </td>
			<td>
                          {if isset($entry->delete_url)}
                          <a href="{$entry->delete_url}" title="{$mod->Lang('delete_article')}">{admin_icon icon='delete.gif'}</a>
                          {/if}
                        </td>
			<td><input type="checkbox" name="{$actionid}sel[]" value="$entry->id" title="{$mod->Lang('toggle_bulk')}"/></td>
		</tr>
	{/foreach}
	</tbody>
</table>
{/if}

<div style="width: 99%;">
{if isset($addlink)}
  <div class="pageoptions" style="float: left;">
    <p class="pageoptions">{$addlink}</p>
  </div>
{/if}
{if $itemcount > 0}
  <div class="pageoptions" style="float: right; text-align: right;" id="bulkactions">
    <label for="bulk_action">{$mod->Lang('with_selected')}:</label>
    <select id="bulk_action" name="{$actionid}bulk_action">
    {if isset($submit_massdelete)}
    <option value="delete">{$mod->Lang('bulk_delete')}</option>
    {/if}
    <option value="setdraft">{$mod->Lang('bulk_setdraft')}</option>
    <option value="setpublished">{$mod->Lang('bulk_setpublished')}</option>
    <option value="setcategory">{$mod->Lang('bulk_setcategory')}</option>
    </select>
    <div id="bulk_category" style="display: inline-block;">
      {$mod->Lang('category')}: {$categoryinput}
    </div>
    <input type="submit" id="submit_bulkaction" name="{$actionid}submit_bulkaction" value="{$mod->Lang('submit')}"/>
  </div>
{/if}
<div class="clearb"></div>
</div>
{$form2end}
