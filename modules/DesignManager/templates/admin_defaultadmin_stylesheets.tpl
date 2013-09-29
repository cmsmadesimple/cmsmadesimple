<script type="text/javascript">
$(document).ready(function(){
    cms_checkAll('#css_selall');

    $('#css_bulk_action,#css_bulk_submit').attr('disabled','disabled');
    $('#css_bulk_submit').button({ 'disabled' : true });
    $('#css_selall,.css_select').on('click',function(){
      if( !$(this).is(':checked') ) {
        $('#css_bulk_action').attr('disabled','disabled');
        $('#css_bulk_submit').attr('disabled','disabled');
        $('#css_bulk_submit').button({ 'disabled' : true });
      } else {
        $('#css_bulk_action').removeAttr('disabled');
        $('#css_bulk_submit').removeAttr('disabled');
        $('#css_bulk_submit').button({ 'disabled' : false });
      }
    });

    $('#editcssfilter').on('click',function(){
      $('#filtercssdlg').dialog({
        buttons: {
          '{$mod->Lang('submit')}': function () {
            $(this).dialog('close');
            $('#filtercssdlg_form').submit();
          },
          '{$mod->Lang('cancel')}': function () {
            $(this).dialog('close');
          },
        }
      });
    });
});
</script>

<div id="filtercssdlg" style="display: none;" title="{$mod->Lang('css_filter')}">
  {form_start id='filtercssdlg_form'}{*strip*}
    <input type="hidden" name="{$actionid}submit_filter_css" value="1"/>
    <div class="pageoverflow">
      <p class="pagetext"><label for="filter_css_design">{$mod->Lang('prompt_design')}:</label></p>
      <p class="pageinput">
        <select id="filter_css_design" name="{$actionid}filter_css_design" title="{$mod->Lang('title_sortby_design')}">
          <option value="">{$mod->Lang('any')}</option>
	  {html_options options=$design_names selected=$css_filter.design}
        </select>
      </p>
    </div>
    <div class="pageoverflow">
      <p class="pagetext"><label for="filter_css_sortby">{$mod->Lang('prompt_sortby')}:</label></p>
      <p class="pageinput">
        <select id="filter_css_sortby" name="{$actionid}filter_css_sortby" title="{$mod->Lang('title_sortby')}">
          <option value="name"{if $css_filter.sortby == 'name'} selected="selected"{/if}>{$mod->Lang('name')}</option>
          <option value="created"{if $css_filter.sortby == 'created'} selected="selected"{/if}>{$mod->Lang('created')}</option>
          <option value="modified"{if $css_filter.sortby == 'modified'} selected="selected"{/if}>{$mod->Lang('modified')}</option>
        </select>
      </p>
    </div>
    <div class="pageoverflow">
      <p class="pagetext"><label for="filter_css_sortorder">{$mod->Lang('prompt_sortorder')}:</label></p>
      <p class="pageinput">
        <select id="filter_css_sortorder" name="{$actionid}filter_css_sortorder" title="{$mod->Lang('title_sortorder')}">
          <option value="asc"{if $css_filter.sortorder == 'asc'} selected="selected"{/if}>{$mod->Lang('asc')}</option>
          <option value="desc"{if $css_filter.sortorder == 'desc'} selected="selected"{/if}>{$mod->Lang('desc')}</option>
        </select>
      </p>
    </div>
    <div class="pageoverflow">
      <p class="pagetext">
        <label for="filter_limit_css">{$mod->Lang('prompt_limit')}:</label>
      </p>
      <p class="pageinput">
        <select id="filter_limit_css" name="{$actionid}filter_limit_css">
          <option value="10"{if (isset($filter_limit_css) && ($filter_limit_css == 10)) } selected="selected"{/if}>10</option>
	  <option value="25"{if (isset($filter_limit_css) && ($filter_limit_css == 25)) } selected="selected"{/if}>25</option>
	  <option value="50"{if (isset($filter_limit_css) && ($filter_limit_css == 50)) } selected="selected"{/if}>50</option>
	  <option value="100"{if (isset($filter_limit_css) && ($filter_limit_css == 100)) } selected="selected"{/if}>100</option>
        </select>
      </p>
    </div>
  {form_end}
</div>

<div class="row">
  <div class="pageoptions options-menu half">
    <ul class="options-menu">
      <li class="parent">{admin_icon icon='run.gif' alt=$mod->Lang('prompt_options')}&nbsp;{$mod->lang('prompt_options')}
        <ul id="popupcsscontents">
           <li><a id="addcss" accesskey="a" href="{cms_action_url action='admin_edit_css'}" title="{$mod->Lang('create_stylesheet')}">{admin_icon icon='newobject.gif'} {$mod->Lang('create_stylesheet')}</a></li>
           <li><a id="editcssfilter" accesskey="f" title="{$mod->Lang('prompt_editfilter')}">{admin_icon icon='edit.gif' alt=$mod->Lang('prompt_editfilter')} {$mod->Lang('filter')}</a></li>
        </ul>
      </li>
      {if $css_filter != '' && $css_filter.design != ''}
      <li><span style="color: green;" title="{$mod->Lang('title_filterapplied')}">{$mod->Lang('filterapplied')}</span></li>
      {/if}
    </ul>
  </div>

  {if isset($css_nav) && $css_nav.numpages > 1}
    <div class="pageoptions" style="text-align: right;">
      {form_start}
        <label for="css_page">{$mod->Lang('prompt_page')}:</label>&nbsp;
        <select id="css_page" name="{$actionid}css_page">
        {cms_pageoptions numpages=$css_nav.numpages curpage=$css_nav.curpage}
        </select>
        &nbsp;<input type="submit" value="{$mod->Lang('go')}"/>
      {form_end}
    </div>
  {/if}
</div>

{if isset($stylesheets)}
  {strip}
  <table class="pagetable">
    <thead>
      <tr>
 	<th title="{$mod->Lang('title_css_id')}">{$mod->Lang('prompt_id')}</th>
 	<th title="{$mod->Lang('title_css_name')}">{$mod->Lang('prompt_name')}</th>
  	<th title="{$mod->Lang('title_css_designs')}">{$mod->Lang('prompt_design')}</th>
	<th title="{$mod->Lang('title_css_modified')}">{$mod->Lang('prompt_modified')}</th>
	<th class="pageicon"></th>{* edit *}
	<th class="pageicon"></th>{* delete *}
	<th class="pageicon"><label for="css_selall" style="display: none;">{$mod->Lang('title_css_selectall')}</label><input type="checkbox" value="1" id="css_selall" title="{$mod->Lang('title_css_selectall')}"/></th>{* multiple *}
      </tr>
    </thead>
    <tbody>
      {foreach $stylesheets as $css}
        {cycle values="row1,row2" assign='rowclass'}
        {include file='module_file_tpl:DesignManager;admin_defaultadmin_csstooltip.tpl' assign='css_tooltip'}
        {cms_action_url action='admin_edit_css' css=$css->get_id() assign='edit_css'}
        {cms_action_url action='admin_delete_css' css=$css->get_id() assign='delete_css'}

	<tr class="{$rowclass}">
	{if !$css->locked()}
          <td><a href="{$edit_css}" data-css-id="{$css->get_id()}" class="edit_css tooltip" title="{$mod->Lang('edit_stylesheet')}" data-cms-description='{$css_tooltip}'>{$css->get_id()}</a></td>
          <td><a href="{$edit_css}" data-css-id="{$css->get_id()}" class="edit_css tooltip" title="{$mod->Lang('edit_stylesheet')}" data-cms-description='{$css_tooltip}'>{$css->get_name()}</a></td>
        {else}
          <td>{$css->get_id()}</td>
          <td><span class="tooltip" data-cms-description='{$css_tooltip}'>{$css->get_name()}</span></td>
        {/if}

        <td>
	  {assign var='t1' value=$css->get_designs()}
	  {if count($t1) == 1}
	    {assign var='t1' value=$t1[0]}
	    {assign var='hn' value=$design_names.$t1}
	    {if $manage_designs}
	      {cms_action_url action=admin_edit_design design=$t1 assign='edit_design_url'}
	      <a href="{$edit_design_url}" title="{$mod->Lang('edit_design')}">{$hn}</a>
	    {else}
	      {$hn}
	    {/if}
	  {elseif count($t1) == 0}
	    <span title="{$mod->Lang('help_stylesheet_no_designs')}">{$mod->Lang('prompt_none')}</span>
	  {else}
	    {capture assign='tooltip_designs'}{strip}
	    {foreach $t1 as $dsn_id}
	      {$mod->Lang('prompt_name')}: {$design_names.$dsn_id}<br />
	    {/foreach}
	    {/strip}{/capture}
	    <a class="tooltip text-red" data-cms-description='{$tooltip_designs|htmlentities}' title="{$mod->Lang('help_stylesheet_multiple_designs')}">{$mod->Lang('prompt_multiple')} ({count($t1)})
	  {/if}
	</td>
	<td>{$css->get_modified()|date_format:'%x %X'}</td>

	{if !$css->locked()}
          <td><a href="{$edit_css}" data-css-id="{$css->get_id()}" class="edit_css" title="{$mod->Lang('edit_stylesheet')}">{admin_icon icon='edit.gif' title=$mod->Lang('edit_stylesheet')}</a></td>
  	  <td><a href="{$delete_css}" title="{$mod->Lang('delete_stylesheet')}">{admin_icon icon='delete.gif' title=$mod->Lang('delete_stylesheet')}</a></td>
  	  <td>
	    <label for="css_select{$css@index}" style="display: none;">{$mod->Lang('prompt_select')}:</label>
	    <input id="{$css@index}" type="checkbox" class="css_select" name="{$actionid}css_select[]" value="{$css->get_id()}"/>
	  </td>
        {else}
          <td>
            {$lock=$css->get_lock()}
            {if $lock.expires < $smarty.now}
	      <a href="{$edit_css}" data-css-id="{$css->get_id()}" accesskey="e" class="steal_css_lock">{admin_icon icon='permissions.gif' class='edit_css steal_css_lock' title=$mod->Lang('prompt_steal_lock')}</a>
            {/if}
          </td>
          <td></td>
          <td></td>
        {/if}
        </tr>
      {/foreach}
    </tbody>
  </table>
  {/strip}

  {capture assign='stylesheet_dropdown_options'}
  {form_start}
    <div class="pageoptions" id="bulkoptions" style="text-align: right;">
      <label for="css_bulk_action">{$mod->Lang('prompt_with_selected')}:</label>&nbsp;
      <select name="{$actionid}css_bulk_action" id="css_bulk_action" class="cssx_bulk_action">
        <option value="delete" title="{$mod->Lang('title_delete')}">{$mod->lang('prompt_delete')}</option>
      </select>
      <input id="css_bulk_submit" class="css_bulk_action" type="submit" name="{$actionid}submit_bulk_css" value="{$mod->Lang('submit')}"/>&nbsp;{cms_help key2='help_css_bulk'}
    </div>
  {form_end}
  {/capture}

  <div class="clearb"></div>
{else}
  <div class="warning">{$mod->Lang('warning_no_stylesheets')}</div>
{/if}

<div class="row">
  <div class="half"></div>
  {if isset($stylesheet_dropdown_options)}{$stylesheet_dropdown_options}{/if}
</div>
