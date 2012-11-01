{if isset($stylesheets)}
  {form_start}{strip}
  <div class="pageoptions" style="text-align: right;">
    <label for="filter_limit_css">{$mod->Lang('prompt_limit')}:</label>
    &nbsp;<select id="filter_limit_css" name="{$actionid}filter_limit_css">
      <option value="2"{if $filter.limit_css == 2} selected="selected"{/if}>2</option>
      <option value="5"{if $filter.limit_css == 5} selected="selected"{/if}>5</option>
      <option value="10"{if $filter.limit_css == 10} selected="selected"{/if}>10</option>
      <option value="25"{if $filter.limit_css == 25} selected="selected"{/if}>25</option>
      <option value="50"{if $filter.limit_css == 50} selected="selected"{/if}>50</option>
      <option value="100"{if $filter.limit_css == 100} selected="selected"{/if}>100</option>
    </select>
    <input type="submit" name="{$actionid}submit_filter_css" value="{$mod->Lang('submit')}"/>
  </div>
  {/strip}{form_end}

  {if isset($css_nav)}
  <div class="pageoptions" style="text-align: right;">
  {if $css_nav.curpage > 1}
    {cms_action_url action='defaultadmin' css_page=1 assign='fp_url'}
    <a href="{$fp_url}" title="{$mod->Lang('prompt_firstpage')}">&lt;&lt;</a>&nbsp;
    {cms_action_url action='defaultadmin' css_page=$css_nav.curpage-1 assign='pp_url'}
    <a href="{$pp_url}" title="{$mod->Lang('prompt_prevpage')}">&lt;</a>&nbsp;
    &nbsp;
  {/if}
  {$mod->Lang('prompt_page')}&nbsp;{$css_nav.curpage}&nbsp;{$mod->Lang('prompt_of')}&nbsp;{$css_nav.numpages}
  {if $css_nav.curpage < $css_nav.numpages}
    &nbsp;
    {cms_action_url action='defaultadmin' css_page=$css_nav.curpage+1 assign='np_url'}
    <a href="{$np_url}" title="{$mod->Lang('prompt_nextpage')}">&gt;</a>&nbsp;
    {cms_action_url action='defaultadmin' css_page=$css_nav.numpages assign='lp_url'}
    <a href="{$lp_url}" title="{$mod->Lang('prompt_lastpage')}">&gt;&gt;</a>
  {/if}
  </div>
  {/if}

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