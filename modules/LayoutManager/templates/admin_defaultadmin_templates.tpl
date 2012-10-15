{if isset($templates)}
{form_start}{strip}
  {assign var='ntemplates' value=count($templates)}
  <div class="pageoptions" style="text-align: right;">
    <label for="filter">{$mod->Lang('prompt_filter')}:</label>
    &nbsp;<select id="filter" name="{$actionid}filter">{html_options options=$filter_options}</select>&nbsp;<input type="submit" name="{$actionid}dofilter" value="{$mod->Lang('submit')}"/>
  </div>

  <table class="pagetable" cellspacing="0">
    <thead>
      <tr>
        <th width="5%">{$mod->Lang('prompt_id')}</th>
        <th>{$mod->Lang('prompt_name')}</th>
        <th>{$mod->Lang('prompt_type')}</th>
        <th>{$mod->Lang('prompt_owner')}</th>
        <th>{$mod->Lang('prompt_themes')}</th>
        <th class="pageicon">{$mod->Lang('prompt_dflt')}</th>{* dflt *}
        <th class="pageicon"></th>{* edit *}
        <th class="pageicon"></th>{* delete *}
      </tr>
    </thead>
    <tbody>
    {foreach from=$templates item='template'}
     {cycle values="row1,row2" assign='rowclass'}
     <tr class="{$rowclass}" onmouseover="this.className='{$rowclass}hover';" onmouseout="this.className='{$rowclass}';">
        {cms_action_url action='admin_edit_template' tpl=$template->get_id() assign='edit_tpl'}
        {cms_action_url action='admin_delete_template' tpl=$template->get_id() assign='delete_tpl'}
        <td><a href="{$edit_tpl}" title="{$mod->Lang('edit_template')}">{$template->get_id()}</a></td>
        <td><a href="{$edit_tpl}" title="{$mod->Lang('edit_template')}">{$template->get_name()}</a></td>
        <td>{assign var='n' value=$template->get_type_id()}{$list_types.$n}</td>
        <td>{if isset($list_users)}{assign var='u' value=$template->get_owner_id()}{$list_users.$u}{else}n/a{/if}</td>
        <td>
          {assign var='t1' value=$template->get_themes()}
          {if count($t1) == 1}
  	    {assign var='t1' value=$t1[0]}
            {assign var='hn' value=$theme_names.$t1}
            {if $manage_themes}
              {cms_action_url action=admin_edit_theme theme=$t1 assign='edit_theme_url'}
              <a href="{$edit_theme_url}" title="{$mod->Lang('edit_theme')}">{$hn}</a>
            {else}
              {$hn}        
            {/if}
          {else}<span title="{$mod->Lang('help_template_multiple_themes')}">({count($t1)})</span>{/if}
        </td>
        <td>
	 {assign var='the_type' value=$list_all_types.$n}
         {if $the_type->get_dflt_flag()}
           {if $template->get_type_dflt()}{admin_icon icon='true.gif' title=$mod->Lang('prompt_dflt')}{else}{admin_icon icon='false.gif' title=$mod->Lang('prompt_notdflt')}{/if}
         {else}
           {$mod->Lang('prompt_na')}
         {/if}
        </td>
        <td><a href="{$edit_tpl}" title="{$mod->Lang('edit_template')}">{admin_icon icon='edit.gif' title=$mod->Lang('prompt_edit')}</a></td>
        <td>
         {if $template->get_owner_id() == get_userid()}
           <a href="{$delete_tpl}" title="{$mod->Lang('delete_template')}">{admin_icon icon='delete.gif' title=$mod->Lang('delete_template')}</a>
         {/if}
        </td>
      </tr>
    {/foreach}
    </tbody>
  </table>
{/strip}{form_end}
{else}
  {page_warning msg=$mod->Lang('warning_no_templates_available')}
{/if}
{if $has_add_right}
{form_start action='admin_edit_template'}
<fieldset class="pagecontainer">
  <legend>{$mod->Lang('create_template')}&nbsp;{admin_icon name="help_create" class="viewhelp" icon='info.gif' title=$mod->Lang('prompt_help')}</legend>
  <select name="{$actionid}import_type">
  {html_options options=$list_types}
  </select>
  <input type="submit" name="{$actionid}submit" value="{$mod->Lang('create')}"/>
</fieldset>
{form_end}
{/if}