{if isset($stylesheets)}
<table class="pagetable" cellspacing="0">
  <thead>
    <tr/>
    <th>{$mod->Lang('prompt_id')}</th>
    <th>{$mod->Lang('prompt_name')}</th>
    <th>{$mod->Lang('prompt_design')}</th>
    <th>{$mod->Lang('prompt_modified')}</th>
    <th class="pageicon"></th>{* edit *}
    <th class="pageicon"></th>{* delete *}
    </tr>
  </thead>
  <tbody>
  {foreach from=$stylesheets item='css'}
   {cms_action_url action='admin_edit_css' css=$css->get_id() assign='edit_css'}
   {cms_action_url action='admin_delete_css' css=$css->get_id() assign='delete_css'}
   {cycle values="row1,row2" assign='rowclass'}
   <tr class="{$rowclass}" onmouseover="this.className='{$rowclass}hover';" onmouseout="this.className='{$rowclass}';">
     <td><a href="{$edit_css}" title="{$mod->Lang('edit_stylesheet')}">{$css->get_id()}</a></td>
     <td><a href="{$edit_css}" title="{$mod->Lang('edit_stylesheet')}">{$css->get_name()}</a></td>
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
          <span title="{$mod->Lang('help_stylesheet_multiple_designs')}">{$mod->Lang('prompt_multiple')} ({count($t1)})</span>
        {/if}
     </td>
     <td>{$css->get_modified()|date_format:'%x %X'}</td>
     <td><a href="{$edit_css}" title="{$mod->Lang('edit_stylesheet')}">{admin_icon icon='edit.gif' title=$mod->Lang('edit_stylesheet')}</a></td>
     <td><a href="{$delete_css}" title="{$mod->Lang('delete_stylesheet')}">{admin_icon icon='delete.gif' title=$mod->Lang('delete_stylesheet')}</a></td>
   </tr>
  {/foreach}
  </tbody>
</table>
{/if}

<div class="pagecontainer">
  {cms_action_url action='admin_edit_css' assign='url'}
  <a href="{$url}" title="{$mod->Lang('create_stylesheet')}">{admin_icon icon='newobject.gif'}</a>&nbsp;
  <a href="{$url}" title="{$mod->Lang('create_stylesheet')}">{$mod->Lang('create_stylesheet')}</a>
</div>