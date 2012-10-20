{if isset($templates)}
{form_start}{strip}
  {assign var='ntemplates' value=count($templates)}
  <div class="pageoptions" style="text-align: right;">
    <label for="filter">{$mod->Lang('prompt_filter')}:</label>
    &nbsp;<select id="filter" name="{$actionid}filter">{html_options options=$filter_options selected=$filter}</select>&nbsp;<input type="submit" name="{$actionid}dofilter" value="{$mod->Lang('submit')}"/>
  </div>

  <table class="pagetable" cellspacing="0">
    <thead>
      <tr>
        <th width="5%">{$mod->Lang('prompt_id')}</th>
        <th>{$mod->Lang('prompt_name')}</th>
        <th>{$mod->Lang('prompt_type')}</th>
        <th>{$mod->Lang('prompt_design')}</th>
        <th>{$mod->Lang('prompt_owner')}</th>
        <th>{$mod->Lang('prompt_modified')}</th>
        <th class="pageicon">{$mod->Lang('prompt_dflt')}</th>{* dflt *}
        <th class="pageicon"></th>{* edit *}
	{if $has_add_right}
        <th class="pageicon"></th>{* copy *}
        {/if}
        <th class="pageicon"></th>{* delete *}
        <th class="pageicon"><input type="checkbox" id="tpl_selall" title="{$mod->Lang('prompt_select_all')}" class="tpl_select"/></th>{* checkbox *}
      </tr>
    </thead>
    <tbody>
    {foreach from=$templates item='template'}
     {cycle values="row1,row2" assign='rowclass'}
     <tr class="{$rowclass}" onmouseover="this.className='{$rowclass}hover';" onmouseout="this.className='{$rowclass}';">
        {cms_action_url action='admin_edit_template' tpl=$template->get_id() assign='edit_tpl'}
	{if $has_add_right}
          {cms_action_url action='admin_copy_template' tpl=$template->get_id() assign='copy_tpl'}
        {/if}
        {cms_action_url action='admin_delete_template' tpl=$template->get_id() assign='delete_tpl'}
        <td><a href="{$edit_tpl}" title="{$mod->Lang('edit_template')}">{$template->get_id()}</a></td>
        <td><a href="{$edit_tpl}" title="{$mod->Lang('edit_template')}">{$template->get_name()}</a></td>
        <td>{assign var='n' value=$template->get_type_id()}{$list_types.$n}</td>
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
          {else}<span title="{$mod->Lang('help_template_multiple_designs')}">({count($t1)})</span>{/if}
        </td>
        <td>{if isset($list_users)}{assign var='u' value=$template->get_owner_id()}{$list_users.$u}{else}n/a{/if}</td>
	<td>{$template->get_modified()|date_format:'%x %X'}</td>
        <td>
	 {assign var='the_type' value=$list_all_types.$n}
         {if $the_type->get_dflt_flag()}
           {if $template->get_type_dflt()}{admin_icon icon='true.gif' title=$mod->Lang('prompt_dflt')}{else}{admin_icon icon='false.gif' title=$mod->Lang('prompt_notdflt')}{/if}
         {else}
           {$mod->Lang('prompt_na')}
         {/if}
        </td>
        <td><a href="{$edit_tpl}" title="{$mod->Lang('edit_template')}">{admin_icon icon='edit.gif' title=$mod->Lang('prompt_edit')}</a></td>
	{if $has_add_right}
        <td><a href="{$copy_tpl}" title="{$mod->Lang('copy_template')}">{admin_icon icon='copy.gif' title=$mod->Lang('prompt_copy')}</a></td>
        {/if}
        <td>
         {if $template->get_owner_id() == get_userid()}
           <a href="{$delete_tpl}" title="{$mod->Lang('delete_template')}">{admin_icon icon='delete.gif' title=$mod->Lang('delete_template')}</a>
         {/if}
        </td>
        <td><input type="checkbox" class="tpl_select" name="tpl_select[]" value="{$template->get_id()}"/></td>
      </tr>
    {/foreach}
    </tbody>
  </table>
{/strip}{form_end}
{else}
  {page_warning msg=$mod->Lang('warning_no_templates_available')}
{/if}

<div style="width: 100%;">
{if $has_add_right}
  {form_start action='admin_edit_template'}
  <div style="float: left; width: 49%;>
  <p>
    <label for="tpl_import_type">{$mod->Lang('create_template')}:</label>&nbsp;
    <select name="{$actionid}import_type" id="tpl_import_type">
      {html_options options=$list_types}
    </select>
    <input type="submit" name="{$actionid}submit" value="{$mod->Lang('create')}"/>&nbsp;{admin_icon name="help_create" class="viewhelp" icon='info.gif' title=$mod->Lang('prompt_help')}
  </p>
  </div>
  {form_end}
{/if}
<div style="float: right; width: 48%; text-align: right;>
  {form_start action='admin_bulk_template'}
  <p class="pageinput" style="text-align: right;">
    <label for="tpl_bulk_action">{$mod->Lang('prompt_with_selected')}:</label>&nbsp;
    <select name="{$actionid}bulk_action" id="tpl_bulk_action" class="tpl_bulk_action">
      {html_options options=$list_types}
    </select>
    <input class="tpl_bulk_action" type="submit" name="{$actionid}submit" value="{$mod->Lang('submit')}"/>&nbsp;{admin_icon name="help_bulk" class="viewhelp" icon='info.gif' title=$mod->Lang('prompt_help')}
  </p>
  {form_end}
</div>
<div class="clearb"></div>
</div>