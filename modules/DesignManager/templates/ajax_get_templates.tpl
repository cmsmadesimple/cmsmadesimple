<script>
$('#tpl_selall').cmsms_checkall();
</script>

{form_start action=defaultadmin}{strip}

<div class="row">
  <div class="pageoptions options-menu half">
    {if $has_add_right}
      <a id="addtemplate" accesskey="a" title="{$mod->Lang('create_template')}">{admin_icon icon='newobject.gif' alt=$mod->Lang('create_template')}&nbsp;{$mod->Lang('create_template')}</a>&nbsp;&nbsp;
    {/if}
    <a id="edittplfilter" accesskey="f" title="{$mod->Lang('prompt_editfilter')}">{admin_icon icon='view.gif' alt=$mod->Lang('prompt_editfilter')}&nbsp;{$mod->Lang('filter')}</a>&nbsp;&nbsp;
    {if $have_locks}
      <a id="clearlocks" accesskey="l" title="{$mod->Lang('title_clearlocks')}" href="{cms_action_url action=admin_clearlocks type=template}">{admin_icon icon='run.gif' alt=''}&nbsp;{$mod->Lang('prompt_clearlocks')}</a>&nbsp;&nbsp;
    {/if}
    {if !empty($tpl_filter[0])}
      <span style="color: green;" title="{$mod->Lang('title_filterapplied')}">{$mod->Lang('filterapplied')}</span>
    {/if}
  </div>

  {if isset($tpl_nav) && $tpl_nav.numpages > 1}
    <div class="pageoptions" style="text-align: right;">
        <label for="tpl_page">{$mod->Lang('prompt_page')}:</label>&nbsp;
        <select id="tpl_page" name="{$actionid}tpl_page">
          {cms_pageoptions numpages=$tpl_nav.numpages curpage=$tpl_nav.curpage}
        </select>
        &nbsp;<input type="submit" value="{$mod->Lang('go')}"/>
    </div>
  {/if}
</div>

{if isset($templates)}
  <table class="pagetable">
    <thead>
      <tr>
        <th title="{$mod->Lang('title_tpl_id')}">{$mod->Lang('prompt_id')}</th>
		<th class="pageicon"></th>
		<th title="{$mod->Lang('title_tpl_name')}">{$mod->Lang('prompt_name')}</th>
		<th title="{$mod->Lang('title_tpl_type')}">{$mod->Lang('prompt_type')}</th>
		<th title="{$mod->Lang('title_tpl_filename')}">{$mod->Lang('prompt_filename')}</th>
		<th title="{$mod->Lang('title_tpl_design')}">{$mod->Lang('prompt_design')}</th>
		<th title="{$mod->Lang('title_tpl_dflt')}" class="pageicon">{$mod->Lang('prompt_dflt')}</th>{* dflt *}
		<th class="pageicon"></th>{* edit *}
		{if $has_add_right}
			<th class="pageicon"></th>{* copy *}
		{/if}
		<th class="pageicon"></th>{* delete *}
		<th class="pageicon"><input type="checkbox" value="1" id="tpl_selall" title="{$mod->Lang('prompt_select_all')}"/></th>{* checkbox *}
      </tr>
    </thead>
    <tbody>
      {foreach from=$templates item='template'}{strip}
        {cycle values="row1,row2" assign='rowclass'}
        {include file='module_file_tpl:DesignManager;admin_defaultadmin_tpltooltip.tpl' assign='tpl_tooltip'}
	<tr class="{$rowclass}">
	  {cms_action_url action='admin_edit_template' tpl=$template->get_id() assign='edit_tpl'}
	  {if $has_add_right}
	    {cms_action_url action='admin_copy_template' tpl=$template->get_id() assign='copy_tpl'}
	  {/if}
	  {cms_action_url action='admin_delete_template' tpl=$template->get_id() assign='delete_tpl'}

  	  {* template id, and template name columns *}
  	  {if !$template->locked()}
	    <td><a href="{$edit_tpl}" data-tpl-id="{$template->get_id()}" class="edit_tpl tooltip" title="{$mod->Lang('edit_template')}" data-cms-description='{$tpl_tooltip}'>{$template->get_id()}</a></td>
            <td></td>
	    <td><a href="{$edit_tpl}" data-tpl-id="{$template->get_type_id()}" class="edit_tpl tooltip" title="{$mod->Lang('edit_template')}" data-cms-description='{$tpl_tooltip}'>{$template->get_name()}</a></td>
	  {else}
	    <td>{$template->get_id()}</td>
	    <td>{admin_icon icon='warning.gif' title=$mod->Lang('title_locked')}</td>
	    <td><span class="tooltip" data-cms-description='{$tpl_tooltip}'>{$template->get_name()}</span></td>
	  {/if}

	  {* template type column *}
	  <td>
	    {$type_id=$template->get_type_id()}
	    {include file='module_file_tpl:DesignManager;admin_defaultadmin_tpltype_tooltip.tpl' assign='tpltype_tooltip'}
	    <span class="tooltip" data-cms-description='{$tpltype_tooltip}'>{$list_types.$type_id}</span>
	  </td>

	  {* filename column *}
	  <td>
	     {if $template->has_content_file()}
	       {basename($template->get_content_filename())}
	     {/if}
	  </td>

	  {* design column *}
	  <td>
	    {assign var='t1' value=$template->get_designs()}
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
	      <span title="{$mod->Lang('help_template_no_designs')}">{$mod->Lang('prompt_none')}</span>
   	    {else}
	      <span title="{$mod->Lang('help_template_multiple_designs')}">{$mod->Lang('prompt_multiple')} ({count($t1)})</span>
	    {/if}
	  </td>

	  {* default column *}
	  <td>
	    {assign var='the_type' value=$list_all_types.$type_id}
	    {if $the_type->get_dflt_flag()}
	      {if $template->get_type_dflt()}
		{admin_icon icon='true.gif' title=$mod->Lang('prompt_dflt_tpl')}
	      {else}
		{admin_icon icon='false.gif' title=$mod->Lang('prompt_notdflt_tpl')}
	      {/if}
	    {else}
	      <span title="{$mod->Lang('prompt_title_na')}">{$mod->Lang('prompt_na')}</span>
	    {/if}
	  </td>

	  {* edit/copy iconsm, or steal icons *}
	  {if !$lock_timeout || !$template->locked()}
	    <td><a href="{$edit_tpl}" data-tpl-id="{$template->get_id()}" class="edit_tpl" title="{$mod->Lang('edit_template')}">{admin_icon icon='edit.gif' title=$mod->Lang('prompt_edit')}</a></td>
	    {if $has_add_right}
	      <td><a href="{$copy_tpl}" title="{$mod->Lang('copy_template')}">{admin_icon icon='copy.gif' title=$mod->Lang('prompt_copy_template')}</a></td>
	    {/if}
	  {else}
  	    <td>
	      {$lock=$template->get_lock()}
	      {if $lock.expires < $smarty.now}
	        <a href="{$edit_tpl}" data-tpl-id="{$template->get_id()}" accesskey="e" class="steal_tpl_lock">{admin_icon icon='permissions.gif' class='edit_tpl steal_tpl_lock' title=$mod->Lang('prompt_steal_lock')}</a>
	      {/if}
	    </td>
	    {if $has_add_right}<td></td>{/if}
	  {/if}

	  {* delete column *}
	  <td>
 	    {if !$template->get_type_dflt() && !$template->locked()}
	      {if $template->get_owner_id() == get_userid() || $manage_templates}
		<a href="{$delete_tpl}" title="{$mod->Lang('delete_template')}">{admin_icon icon='delete.gif' title=$mod->Lang('delete_template')}</a>
	      {/if}
	    {/if}
	  </td>

	  {* checkbox column *}
	  <td>
	    {if !$template->locked() && ($template->get_owner_id() == get_userid() || $manage_templates) }
	      <input type="checkbox" class="tpl_select" name="{$actionid}tpl_select[]" value="{$template->get_id()}" title="{$mod->Lang('title_tpl_bulk')}"/>
	    {/if}
	  </td>

	</tr>
      {/strip}{/foreach}
    </tbody>
  </table>

  <div class="row">
    <div class="half options-menu"></div>
    <div class="half options-menu">
      <p class="pageinput" style="text-align: right;">
        <label for="tpl_bulk_action">{$mod->Lang('prompt_with_selected')}:</label>&nbsp;
        <select name="{$actionid}bulk_action" id="tpl_bulk_action" class="tpl_bulk_action" title="{$mod->Lang('title_tpl_bulkaction')}">
          <option value="delete">{$mod->lang('prompt_delete')}</option>
          <option value="export">{$mod->lang('export')}</option>
          <option value="import">{$mod->lang('import')}</option>
        </select>
        <input id="tpl_bulk_submit" class="tpl_bulk_action" type="submit" name="{$actionid}submit_bulk" value="{$mod->Lang('submit')}"/>&nbsp;{cms_help key2='help_bulk_templates' title=$mod->lang('prompt_delete')}
      </p>
    </div>
    <div class="clearb"></div>
  </div>

{else}
  {page_warning msg=$mod->Lang('warning_no_templates_available')}
{/if}

{/strip}{form_end}