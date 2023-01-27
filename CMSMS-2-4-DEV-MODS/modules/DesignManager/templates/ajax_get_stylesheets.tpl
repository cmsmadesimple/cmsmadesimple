<script>
$('#css_selall').cmsms_checkall();
</script>

<div class="row">
  <div class="pageoptions options-menu half">
      <a id="addcss" accesskey="a" href="{cms_action_url action='admin_edit_css'}" title="{$mod->Lang('create_stylesheet')}">{admin_icon icon='newobject.gif'} {$mod->Lang('create_stylesheet')}</a>&nbsp;&nbsp;
      <a id="editcssfilter" accesskey="f" title="{$mod->Lang('prompt_editfilter')}">{admin_icon icon='view.gif' alt=$mod->Lang('prompt_editfilter')} {$mod->Lang('filter')}</a>&nbsp;&nbsp;
      {if $have_css_locks}
         <a id="cssclearlocks" accesskey="l" title="{$mod->Lang('title_clearlocks')}" href="{cms_action_url action=admin_clearlocks type=stylesheet}">{admin_icon icon='run.gif' alt=''}&nbsp;{$mod->Lang('prompt_clearlocks')}</a>&nbsp;&nbsp;
      {/if}
      {if $css_filter != '' && $css_filter.design != ''}
      <span style="color: green;" title="{$mod->Lang('title_filterapplied')}">{$mod->Lang('filterapplied')}</span>
      {/if}
    </ul>
  </div>

  {if isset($css_nav) && $css_nav.numpages > 1}
    <div class="pageoptions" style="text-align: right;">
      {form_start action=defaultadmin}
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
  {form_start action=admin_bulk_css}
  <table class="pagetable">
    <thead>
      <tr>
    <th title="{$mod->Lang('title_css_id')}">{$mod->Lang('prompt_id')}</th>
    <th class="pageicon"></th>
    <th title="{$mod->Lang('title_css_name')}">{$mod->Lang('prompt_name')}</th>
    <th title="{$mod->Lang('title_css_designs')}">{$mod->Lang('prompt_design')}</th>
    <th title="{$mod->Lang('title_css_filename')}">{$mod->Lang('prompt_filename')}</th>
    <th title="{$mod->Lang('title_css_modified')}">{$mod->Lang('prompt_modified')}</th>
    <th class="pageicon"></th>{* edit *}
    <th class="pageicon"></th>{* copy *}
    <th class="pageicon"></th>{* delete *}
    <th class="pageicon"><label for="css_selall" style="display: none;">{$mod->Lang('title_css_selectall')}</label><input type="checkbox" value="1" id="css_selall" title="{$mod->Lang('title_css_selectall')}"/></th>{* multiple *}
      </tr>
    </thead>
    <tbody>
      {foreach $stylesheets as $css}
        {cycle values="row1,row2" assign='rowclass'}
        {include file='module_file_tpl:DesignManager;admin_defaultadmin_csstooltip.tpl' assign='css_tooltip'}
        {cms_action_url action='admin_edit_css' css=$css->get_id() assign='edit_css'}
        {cms_action_url action='admin_copy_css' css=$css->get_id() assign='copy_css'}
        {cms_action_url action='admin_delete_css' css=$css->get_id() assign='delete_css'}

    <tr class="{$rowclass}">
    {if !$css->locked()}
          <td><a href="{$edit_css}" data-css-id="{$css->get_id()}" class="edit_css tooltip" title="{$mod->Lang('edit_stylesheet')}" data-cms-description='{$css_tooltip}'>{$css->get_id()}</a></td>
          <td></td>
          <td><a href="{$edit_css}" data-css-id="{$css->get_id()}" class="edit_css tooltip" title="{$mod->Lang('edit_stylesheet')}" data-cms-description='{$css_tooltip}'>{$css->get_name()}</a></td>
        {else}
          <td>{$css->get_id()}</td>
          <td>{admin_icon icon='warning.gif' title=$mod->Lang('title_locked')}</td>
          <td><span class="tooltip" data-cms-description='{$css_tooltip}'>{$css->get_name()}</span></td>
        {/if}

        <td>
      {assign var='t1' value=$css->get_designs()}
      {if $t1 && count($t1) == 1}
        {assign var='t1' value=$t1[0]}
        {assign var='hn' value=$design_names.$t1}
        {if $manage_designs}
          {cms_action_url action=admin_edit_design design=$t1 assign='edit_design_url'}
          <a href="{$edit_design_url}" title="{$mod->Lang('edit_design')}">{$hn}</a>
        {else}
          {$hn}
        {/if}
      {elseif empty($t1)}
        <span title="{$mod->Lang('help_stylesheet_no_designs')}">{$mod->Lang('prompt_none')}</span>
      {else}
        {capture assign='tooltip_designs'}{strip}
            <u>{$mod->Lang('prompt_attached_designs')}</u>:<br />
        {foreach $t1 as $dsn_id}
          {$design_names.$dsn_id}<br />
        {/foreach}
        {/strip}{/capture}
        <a class="tooltip text-red" data-cms-description='{$tooltip_designs|htmlentities}' title="{$mod->Lang('help_stylesheet_multiple_designs')}">{$mod->Lang('prompt_multiple')} ({count($t1)})
      {/if}
    </td>

    <td>
       {if $css->has_content_file()}
         {basename($css->get_content_filename())}
       {/if}
    </td>

    <td>{$css->get_modified()|localedate_format:'%x %X'}</td>

    {if !$css->locked()}
          <td><a href="{$edit_css}" data-css-id="{$css->get_id()}" class="edit_css" title="{$mod->Lang('edit_stylesheet')}">{admin_icon icon='edit.gif' title=$mod->Lang('edit_stylesheet')}</a></td>
      <td><a href="{$copy_css}" title="{$mod->Lang('copy_stylesheet')}">{admin_icon icon='copy.gif' title=$mod->Lang('copy_stylesheet')}</a></td>
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
          <td></td>
        {/if}
        </tr>
      {/foreach}
    </tbody>
  </table>
  {/strip}

  {capture assign='stylesheet_dropdown_options'}
    <div class="pageoptions" id="bulkoptions" style="text-align: right;">
      <label for="css_bulk_action">{$mod->Lang('prompt_with_selected')}:</label>&nbsp;
      <select name="{$actionid}css_bulk_action" id="css_bulk_action" class="cssx_bulk_action">
        <option value="delete" title="{$mod->Lang('title_delete')}">{$mod->lang('prompt_delete')}</option>
        <option value="export">{$mod->lang('export')}</option>
        <option value="import">{$mod->lang('import')}</option>
      </select>
      <input id="css_bulk_submit" class="css_bulk_action" type="submit" name="{$actionid}submit_bulk_css" value="{$mod->Lang('submit')}"/>&nbsp;{cms_help key2='help_css_bulk' title=$mod->lang('prompt_delete')}
    </div>
  {/capture}

  <div class="clearb"></div>
  <div class="row">
    <div class="half"></div>
    {if isset($stylesheet_dropdown_options)}{$stylesheet_dropdown_options}{/if}
  </div>
  {form_end}
{else}
  <div class="warning">{$mod->Lang('warning_no_stylesheets')}</div>
{/if}
