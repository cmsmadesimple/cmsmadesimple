<script type="text/javascript">
$(document).ready(function(){
    cms_checkAll('#tpl_selall');

    $('a.steal_lock').on('click',function(e) {
      // we're gonna confirm stealing this lock.
      var v = confirm('{$mod->Lang('confirm_steal_lock')}');
      return v;
    });

    $('a.edit_tpl').on('click',function() {
      if( $(this).hasClass('steal_lock') ) return true;

      // do a double check to see if this page is locked or not.
      var tpl_id = $(this).attr('data-tpl-id');
      var url = '{$admin_url}/ajax_lock.php?showtemplate=false';
      var opts = { opt: 'check', type: 'template', oid: tpl_id };
      var ok = false;
      opts[cms_data.secure_param_name] = cms_data.user_key;
      $.ajax({
        url: url,
        async: false,
        data: opts,
        success: function(data,textStatus,jqXHR) {
	  if( data.status == 'success' ) {
            if( data.locked ) {
              // gotta display a message.
	      cms_alert('{$mod->Lang('error_contentlocked')}');
            }
            else {
              // we're okay to edit
	      ok = true;
            }
          }
        }
      });
      return ok;
    });

    $('#tpl_bulk_submit').on('click', function(e){
        var n = $('input:checkbox:checked.tpl_select').length
            if( n == 0 ) {
                cms_alert('{$mod->Lang('error_nothingselected')}');
                return false;
            }
            return confirm('{$mod->Lang('confirm_bulk_tmplop')}');
        });

    $('#editfilter').on('click', function () {
      $('#filterdialog').dialog({
        buttons: {
          '{$mod->Lang('submit')}': function () {
            $(this).dialog('close');
            $('#filterdialog_form').submit();
          },
          '{$mod->Lang('cancel')}': function () {
            $(this).dialog('close');
          },
        }
      });
    });
    $(document).on('click','#addtemplate', function () {
      $('#addtemplatedialog').dialog({
        buttons: {
          '{$mod->Lang('submit')}': function () {
            $(this).dialog('close');
            $('#addtemplate_form').submit();
          },
          '{$mod->Lang('cancel')}': function () {
            $(this).dialog('close');
          },
        }
      });
    });
});
</script>
<div id="filterdialog" style="display: none;" title="{$mod->Lang('filter')}">
{form_start action='defaultadmin' id='filterdialog_form'}
  <div class="pageoverflow">
    <p class="pagetext"><label for="filter_tpl">{$mod->Lang('prompt_options')}:</label></p>
    <p class="pageinput">
      <select id="filter_tpl" name="{$actionid}filter_tpl" title="{$mod->Lang('title_filter')}">
	{html_options options=$filter_tpl_options selected=$tpl_filter.tpl}
      </select>
    </p>
  </div>
  <div class="pageoverflow">
    <input type="hidden" name="{$actionid}submit_filter_tpl" value="1"/>
    <p class="pagetext"><label for="filter_limit">{$mod->Lang('prompt_limit')}:</label></p>
    <p class="pageinput">
      <select id="filter_limit" name="{$actionid}filter_limit_tpl" title="{$mod->Lang('title_filterlimit')}">
	<option value="2"{if $tpl_filter.limit == 2} selected="selected"{/if}>2</option>
	<option value="5"{if $tpl_filter.limit == 5} selected="selected"{/if}>5</option>
	<option value="10"{if $tpl_filter.limit == 10} selected="selected"{/if}>10</option>
	<option value="25"{if $tpl_filter.limit == 25} selected="selected"{/if}>25</option>
	<option value="50"{if $tpl_filter.limit == 50} selected="selected"{/if}>50</option>
	<option value="100"{if $tpl_filter.limit == 100} selected="selected"{/if}>100</option>
      </select>
    </p>
  </div>
{form_end}
</div>
{if $has_add_right}
<div id="addtemplatedialog" style="display: none;" title="{$mod->Lang('create_template')}">
{form_start id="addtemplate_form"}
  <div class="pageoverflow">
    <input type="hidden" name="{$actionid}submit_create" value="1"/>
    <p class="pagetext"><label for="tpl_import_type">{$mod->Lang('tpl_type')}:</label></p>
      <select name="{$actionid}import_type" id="tpl_import_type" title="{$mod->Lang('title_tpl_import_type')}">
	{html_options options=$list_types}
      </select>
    <p class="pageinput">
    </p>
  </div>
{form_end}
</div>
{/if}

{form_start}
{strip}
<div class="row">
  <div class="pageoptions options-menu half">
    <ul class="options-menu">
        <li class="parent">{admin_icon icon='run.gif' alt=$mod->Lang('prompt_options')}&nbsp;{$mod->lang('prompt_options')}
          <ul id="popupmenucontents">
            {if $has_add_right}
              <li><a id="addtemplate" accesskey="a" title="{$mod->Lang('create_template')}">{admin_icon icon='newobject.gif' alt=$mod->Lang('create_template')}&nbsp;{$mod->Lang('create_template')}</li>
            {/if}
            <li><a id="editfilter" accesskey="f" title="{$mod->Lang('prompt_editfilter')}">{admin_icon icon='edit.gif' alt=$mod->Lang('prompt_editfilter')}&nbsp;{$mod->Lang('filter')}</li>
          </ul>
        </li>
        {if $tpl_filter.tpl != '' && $tpl_filter != -1}
        <li><span style="color: green;" title="{$mod->Lang('title_filterapplied')}">{$mod->Lang('filterapplied')}</span></li>
        {/if}
    </ul>
  </div>

{if isset($templates)}
	{if isset($tpl_nav) && $tpl_nav.numpages > 1}
		<div class="pageoptions" style="text-align: right;">
		{form_start}
			{$mod->Lang('prompt_page')}&nbsp;
                       	<select name="{$actionid}tpl_page">
                       	{cms_pageoptions numpages=$tpl_nav.numpages curpage=$tpl_nav.curpage}
                       	</select>
                        &nbsp;{$mod->Lang('prompt_of')}&nbsp;{$tpl_nav.numpages}&nbsp;<input type="submit" value="{$mod->Lang('go')}"/>
		{form_end}
		</div>
	{/if}

	<table class="pagetable">
		<thead>
			<tr>
				<th title="{$mod->Lang('title_tpl_id')}" width="5%">{$mod->Lang('prompt_id')}</th>
				<th title="{$mod->Lang('title_tpl_name')}">{$mod->Lang('prompt_name')}</th>
				<th title="{$mod->Lang('title_tpl_type')}">{$mod->Lang('prompt_type')}</th>
				<th title="{$mod->Lang('title_tpl_design')}">{$mod->Lang('prompt_design')}</th>
				<th title="{$mod->Lang('title_tpl_modified')}">{$mod->Lang('prompt_modified')}</th>
				<th title="{$mod->Lang('title_tpl_dflt')}"class="pageicon">{$mod->Lang('prompt_dflt')}</th>{* dflt *}
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
				<td><a href="{$edit_tpl}" data-tpl-id="{$template->get_type_id()}" class="edit_tpl tooltip" title="{$mod->Lang('edit_template')}" data-cms-description='{$tpl_tooltip}'>{$template->get_name()}</a></td>
			{else}
				<td>{$template->get_id()}</td>
				<td><span class="tooltip" data-cms-description='{$tpl_tooltip}'>{$template->get_name()}</span></td>
			{/if}

			{* template type column *}
			<td>
				{$type_id=$template->get_type_id()}
				{include file='module_file_tpl:DesignManager;admin_defaultadmin_tpltype_tooltip.tpl' assign='tpltype_tooltip'}
				<span class="tooltip" data-cms-description='{$tpltype_tooltip}'>{$list_types.$type_id}</span>
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

			{* modified date column *}
			<td>{$template->get_modified()|date_format:'%x %X'}</td>

			{* default column *}
			<td>
	 			{assign var='the_type' value=$list_all_types.$type_id}
				{if $the_type->get_dflt_flag()}
					{if $template->get_type_dflt()}
						{admin_icon icon='true.gif' title=$mod->Lang('prompt_dflt')}
					{else}
						{admin_icon icon='false.gif' title=$mod->Lang('prompt_notdflt_tpl')}
					{/if}
				{else}
					<span title="{$mod->Lang('prompt_title_na')}">{$mod->Lang('prompt_na')}</span>
				{/if}
			</td>

			{* edit/copy iconsm, or steal icons *}
			{if !$template->locked()}
				<td><a href="{$edit_tpl}" data-tpl-id="{$template->get_id()}" class="edit_tpl" title="{$mod->Lang('edit_template')}">{admin_icon icon='edit.gif' title=$mod->Lang('prompt_edit')}</a></td>
				{if $has_add_right}
					<td><a href="{$copy_tpl}" title="{$mod->Lang('copy_template')}">{admin_icon icon='copy.gif' title=$mod->Lang('prompt_copy_template')}</a></td>
				{/if}
			{else}
				<td>
				{$lock=$template->get_lock()}
				{if $lock.expires < $smarty.now}
					<a href="{$edit_tpl}" data-tpl-id="{$template->get_id()}" accesskey="e" class="steal_lock">{admin_icon icon='permissions.gif' class='edit_tpl steal_lock' title=$mod->Lang('prompt_steal_lock')}</a>
				{/if}
				</td>
				<td></td>
			{/if}

			{* delete column *}
			<td>
 			{if !$template->get_type_dflt()}
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
{else}
	{page_warning msg=$mod->Lang('warning_no_templates_available')}
{/if}

<div class="row">
  <div class="half options-menu"></div>
  <div class="half options-menu">
	<p class="pageinput" style="text-align: right;">
	<label for="tpl_bulk_action">{$mod->Lang('prompt_with_selected')}:</label>&nbsp;
		<select name="{$actionid}bulk_action" id="tpl_bulk_action" class="tpl_bulk_action" title="{$mod->Lang('title_tpl_bulkaction')}">
			<option value="delete" title="{$mod->Lang('title_delete')}">{$mod->lang('prompt_delete')}</option>
		</select>
		<input id="tpl_bulk_submit" class="tpl_bulk_action" type="submit" name="{$actionid}submit_bulk" value="{$mod->Lang('submit')}"/>&nbsp;{admin_icon name="help_bulk" class="viewhelp" icon='info.gif' title=$mod->Lang('prompt_help')}
	</p>
</div>
<div class="clearb"></div>
</div>
{/strip}
{form_end}