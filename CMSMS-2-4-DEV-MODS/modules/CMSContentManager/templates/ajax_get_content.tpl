<div class="row c_full cf">
  <div class="pageoptions grid_8" style="margin-top: 8px;">
      {if $can_add_content}
        <a href="{cms_action_url action=admin_editcontent}" accesskey="n" title="{$mod->Lang('addcontent')}" class="pageoptions">{admin_icon icon='newobject.gif' alt=$mod->Lang('addcontent')}&nbsp;{$mod->Lang('addcontent')}</a>
      {/if}

      {if !$have_filter && isset($content_list)}
        <a class="expandall" href="{cms_action_url action='defaultadmin' expandall=1}" accesskey="e" title="{$mod->Lang('prompt_expandall')}">{admin_icon icon='expandall.gif' alt=$mod->Lang('expandall')}&nbsp;{$mod->Lang('expandall')}</a>
    <a class="collapseall" href="{cms_action_url action='defaultadmin' collapseall=1}" accesskey="c" title="{$mod->Lang('prompt_collapseall')}">{admin_icon icon='contractall.gif' alt=$mod->Lang('contractall')}&nbsp;{$mod->Lang('contractall')}</a>
    {if $can_reorder_content}
      <a id="ordercontent" href="{cms_action_url action=admin_ordercontent}" accesskey="r" title="{$mod->Lang('prompt_ordercontent')}">{admin_icon icon='reorder.gif' alt=$mod->Lang('reorderpages')}&nbsp;{$mod->Lang('reorderpages')}</a>
    {/if}
    {if $have_locks}
      <a id="clearlocks" href="{cms_action_url action=admin_clearlocks}" accesskey="l" title="{$mod->Lang('title_clearlocks')}">{admin_icon icon='run.gif' alt=''}&nbsp;{$mod->Lang('prompt_clearlocks')}</a>
    {/if}
      {/if}
      <a id="myoptions" accesskey="o" title="{$mod->Lang('prompt_settings')}">{admin_icon icon='edit.gif' alt=$mod->Lang('prompt_settings')}&nbsp;{$mod->lang('prompt_settings')}</a>
      {if !empty($have_filter)}<span style="color: red;"><em>({$mod->Lang('filter_applied')})</em></span>{/if}
  </div>

  <div class="pageoptions options-form grid_4" style="float: right;">
    {if isset($content_list)}
    <span><label for="ajax_find">{$mod->Lang('find')}:</label>&nbsp;<input type="text" id="ajax_find" name="ajax_find" title="{$mod->Lang('title_listcontent_find')}" value="" size="25"/></span>
    {/if}

    {if isset($content_list) && $npages > 1}
      {form_start action='defaultadmin'}
        <span>{$mod->Lang('page')}:&nbsp;
        <select name="{$actionid}curpage" id="{$actionid}curpage">
          {html_options options=$pagelist selected=$curpage}
        </select>
        <button name="{$actionid}submitpage" class="invisible ui-button ui-widget ui-corner-all ui-state-default ui-button-text-icon-primary">
          <span class="ui-button-icon-primary ui-icon ui-icon-circle-check"></span>
          <span class="ui-button-text">{$mod->Lang('go')}</span>
        </button>
        </span>
      {form_end}
    {/if}
  </div>
</div>

{form_start action='defaultadmin' id='listform'}
  <div id="contentlist">{* everything from here down is part of the ajax stuff *}
  {* error container *}
  {if isset($error)}
  <div id="error_cont" class="red" style="color: red; width: 80%; margin-left: 2%; margin-right: 10%; text-align: center; vertical-align: middle;">{$error}</div>
  {/if}

  {if isset($content_list)}
    {function do_content_row}
      {foreach $columns as $column => $flag}
        {if !$flag}{continue}{/if}
    <td class="{$column}">
      {if $column == 'expand'}
        {if $row.expand == 'open'}
        <a href="{cms_action_url action='defaultadmin' collapse=$row.id}" class="page_collapse" accesskey="C" title="{$mod->Lang('prompt_page_collapse')}">
          {admin_icon icon='contract.gif' class="hier_contract"}
        </a>
        {elseif $row.expand == 'closed'}
          <a href="{cms_action_url action='defaultadmin' expand=$row.id}" class="page_expand" accesskey="c" title="{$mod->Lang('prompt_page_expand')}">{admin_icon icon='expand.gif' class="hier_expand"}</a>
        {/if}
      {elseif $column == 'icon1'}
        {if isset($row.lock)}
          {admin_icon icon='warning.gif' title=$mod->Lang('title_locked')}
        {/if}
      {elseif $column == 'hier'}
        {$row.hier}
      {elseif $column == 'page'}
        {if $row.can_edit}
          {if $indent}{repeat string='-&nbsp;&nbsp;' times=$row.depth-2}{/if}
          {* the tooltip *}
          {capture assign='tooltip_pageinfo'}{strip}
            <strong>{$mod->Lang('prompt_content_id')}:</strong> {$row.id}<br/>
            <strong>{$mod->Lang('prompt_title')}:</strong> {$row.title|escape}<br/>
            <strong>{$mod->Lang('prompt_name')}:</strong> {$row.menutext|escape}<br/>
            {if isset($row.alias)}<strong>{$mod->Lang('prompt_alias')}:</strong> {$row.alias}<br/>{/if}
            {if $row.secure}<strong>{$mod->Lang('prompt_secure')}:</strong> {$mod->Lang('yes')}<br/>{/if}
            <strong>{$mod->Lang('prompt_cachable')}:</strong> {if $row.cachable}{$mod->Lang('yes')}{else}{$mod->Lang('no')}{/if}<br/>
            <strong>{$mod->Lang('prompt_showinmenu')}:</strong> {if $row.showinmenu}{$mod->Lang('yes')}{else}{$mod->Lang('no')}{/if}<br/>
            <strong>{lang('wantschildren')}:</strong> {if $row.wantschildren|default:1}{$mod->Lang('yes')}{else}{$mod->Lang('no')}{/if}
          {/strip}{/capture}

          <a href="{cms_action_url action='admin_editcontent' content_id=$row.id}" class="page_edit tooltip" accesskey="e" data-cms-content='{$row.id}' data-cms-description='{$tooltip_pageinfo|cms_htmlentities}'>{$row.page|default:''}</a>
        {else}
          {if isset($row.lock)}
            {capture assign='tooltip_lockinfo'}{strip}
          {if $row.can_steal}<strong>{$mod->Lang('locked_steal')}:</strong><br/>{/if}
          <strong>{$mod->Lang('locked_by')}:</strong> {$row.lockuser}<br/>
          <strong>{$mod->Lang('locked_since')}:</strong> {$row.lock.created|localedate_format:'%x H:i'}<br/>
          {if $row.lock.expires < $smarty.now}
            <span style="color: red;"><strong>{$mod->Lang('lock_expired')}:</strong> {$row.lock.expires|relative_time}</span>
          {else}
            <strong>{$mod->Lang('lock_expires')}:</strong> {$row.lock.expires|relative_time}
                  {/if}<br/>
            {/strip}{/capture}
            {if !$row.can_steal}
              <span class="tooltip" data-cms-description='{$tooltip_lockinfo|htmlentities}'>{$row.page}</span>
            {else}
              <a href="{cms_action_url action='admin_editcontent' content_id=$row.id}" class="page_edit tooltip steal_lock" accesskey="e" data-cms-content='{$row.id}' data-cms-description='{$tooltip_lockinfo|htmlentities}'>{$row.page}</a>
            {/if}
          {else}
            {$row.page}
          {/if}
        {/if}
      {elseif $column == 'alias'}
        {$row.alias|default:''}
      {elseif $column == 'url'}
        {if $prettyurls_ok}
          {$row.url}
        {else}
          <span class="text-red">{$row.url}</span>
        {/if}
      {elseif $column == 'template'}
        {if isset($row.template) && $row.template != ''}
          {if $row.can_edit_tpl}
            <a href="{cms_action_url module='DesignManager' action='admin_edit_template' tpl=$row.template_id}" class="page_template" title="{$mod->Lang('prompt_page_template')}">
          {$row.template}
        </a>
          {else}
            {$row.template}
          {/if}
        {elseif $row.viewable}
          <span class="text-red" title="{$mod->Lang('error_template_notavailable')}">{$mod->Lang('critical_error')}</span>
        {/if}
      {elseif $column == 'friendlyname'}
        {$row.friendlyname}
      {elseif $column == 'owner'}
            {capture assign='tooltip_ownerinfo'}{strip}
          <strong>{$mod->Lang('prompt_created')}:</strong> {$row.created|localedate_format:'%x H:i'}<br/>
          <strong>{$mod->Lang('prompt_lastmodified')}:</strong> {$row.lastmodified|localedate_format:'%x H:i'}<br/>
          {if isset($row.lastmodifiedby)}
            <strong>{$mod->Lang('prompt_lastmodifiedby')}:</strong> {$row.lastmodifiedby}<br/>
          {/if}
        {/strip}{/capture}
        <span class="tooltip" data-cms-description='{$tooltip_ownerinfo|htmlentities}'>{$row.owner}</span>
      {elseif $column == 'active'}
        {if $row.active == 'inactive'}
          <a href="{cms_action_url action='defaultadmin' setactive=$row.id}" class="page_setactive" accesskey="a">
            {admin_icon icon='false.gif' title=$mod->Lang('prompt_page_setactive')}
          </a>
        {else if $row.active != 'default' && $row.active != ''}
          <a href="{cms_action_url action='defaultadmin' setinactive=$row.id}" class="page_setinactive" accesskey="a">
        {admin_icon icon='true.gif' title=$mod->Lang('prompt_page_setinactive')}
          </a>
        {/if}
      {elseif $column == 'default'}
        {if $row.default == 'yes'}
          {admin_icon icon='true.gif' class='page_default' title=$mod->Lang('prompt_page_default')}
        {else if $row.default == 'no' && $row.can_edit}
          <a href="{cms_action_url action='defaultadmin' setdefault=$row.id}" class="page_setdefault" accesskey="d">
        {admin_icon icon='false.gif' class='page_setdefault' title=$mod->Lang('prompt_page_setdefault')}
          </a>
        {/if}
      {elseif $column == 'move'}
        {if isset($row.move)}
          {if $row.move == 'up'}
        <a href="{cms_action_url action='defaultadmin' moveup=$row.id}" class="page_sortup" accesskey="m">
          {admin_icon icon='sort_up.gif' title=$mod->Lang('prompt_page_sortup')}
        </a>
          {elseif $row.move == 'down'}
            <a href="{cms_action_url action='defaultadmin' movedown=$row.id}" class="page_sortdown" accesskey="m">
          {admin_icon icon='sort_down.gif' title=$mod->Lang('prompt_page_sortdown')}
        </a>
          {elseif $row.move == 'both'}
        <a href="{cms_action_url action='defaultadmin' moveup=$row.id}" class="page_sortup" accesskey="m">{admin_icon icon='sort_up.gif' title=$mod->Lang('prompt_page_sortup')}</a>
        <a href="{cms_action_url action='defaultadmin' movedown=$row.id}" class="page_sortdown" accesskey="m">{admin_icon icon='sort_down.gif' title=$mod->Lang('prompt_page_sortdown')}</a>
          {/if}
        {/if}
      {elseif $column == 'view'}
        {if $row.view != ''}
          <a class="page_view" target="_blank" href="{$row.view}" accesskey="v">
        {admin_icon icon='view.gif' title=$mod->Lang('prompt_page_view')}
          </a>
        {/if}
      {elseif $column == 'copy'}
        {if $row.copy != ''}
          <a href="{cms_action_url action='admin_copycontent' page=$row.id}" accesskey="o">
        {admin_icon icon='copy.gif' class='page_copy' title=$mod->Lang('prompt_page_copy')}
          </a>
         {/if}
      {elseif $column == 'edit'}
        {if $row.can_edit}
          <a href="{cms_action_url action=admin_editcontent content_id=$row.id}" accesskey="e" class="page_edit" title="{$mod->Lang('addcontent')}" data-cms-content="{$row.id}">
        {admin_icon icon='edit.gif' class='page_edit' title=$mod->Lang('prompt_page_edit')}
          </a>
        {else}
          {if isset($row.lock) && $row.can_steal}
        <a href="{cms_action_url action=admin_editcontent content_id=$row.id}" accesskey="e" class="page_edit" title="{$mod->Lang('addcontent')}" data-cms-content="{$row.id}" class="steal_lock">
          {admin_icon icon='permissions.gif' class='page_edit steal_lock' title=$mod->Lang('prompt_steal_lock_edit')}
        </a>
          {/if}
        {/if}
      {elseif $column == 'delete'}
        {if $row.can_delete && $row.delete != ''}
          <a href="{cms_action_url action='defaultadmin' delete=$row.id}" class="page_delete" accesskey="r">
        {admin_icon icon='delete.gif' class='page_delete' title=$mod->Lang('prompt_page_delete')}
           </a>
        {/if}
      {elseif $column == 'multiselect'}
        {if $row.multiselect != ''}
          <label class="invisible" for="multicontent-{$row.id}">{$mod->Lang('prompt_multiselect_toggle')}</label>
          <input type="checkbox" id="multicontent-{$row.id}" class="multicontent" name="{$actionid}multicontent[]" value="{$row.id}" title="{$mod->Lang('prompt_multiselect_toggle')}"/>
        {/if}
      {else}
        {* unknown column *}
      {/if}
    </td>
      {/foreach}
    {/function}

  {strip}<table id="contenttable" class="pagetable" width="100%">
    <thead>
      <tr>
        {foreach from=$columns key='column' item='flag'}
    {if $flag}
      <th class="{*$column TODO Rolf *} {if $flag=='icon'}pageicon{/if}"><!-- {$column} -->
      {if $column == 'expand' or $column == 'hier' or $column == 'icon1' or $column == 'view' or $column == 'copy' or $column == 'edit' or $column == 'delete'}
            <span title="{$mod->Lang("coltitle_{$column}")}">&nbsp;</span>{* no column header *}
      {elseif $column == 'multiselect'}
        <input type="checkbox" id="selectall" value="1" title="{$mod->Lang('select_all')}"/>
      {elseif $column == 'page'}
        <span title="{$coltitle_page}">{$colhdr_page}</span>
      {else}
        {if ($have_locks == '1') && ($column == 'default' || $column == 'move')}
          <span title="{$mod->Lang('error_action_contentlocked')}">({$mod->Lang("colhdr_{$column}")})</span>
        {else}
          <span title="{$mod->Lang("coltitle_{$column}")}">{$mod->Lang("colhdr_{$column}")}</span>
        {/if}
      {/if}
      </th>
    {/if}
        {/foreach}
      </tr>
    </thead>
    <tbody class="contentrows">
      {foreach $content_list as $row}
        {cycle values="row1,row2" assign='rowclass'}
    <tr id="row_{$row.id}" class="{$rowclass} {if isset($row.selected)}selected{/if}">
      {do_content_row row=$row columns=$columns}
    </tr>
      {/foreach}
    </tbody>
  </table>{/strip}
  {else}

  {/if}
  </div>{* #contentlist *}

{if isset($content_list)}
  <div class="row c_full cf">
    {if $can_add_content}
      <div class="pageoptions grid_6" style="margin-top: 8px;">
        <a  href="{cms_action_url action=admin_editcontent}" accesskey="n" title="{$mod->Lang('addcontent')}" class="pageoptions">{admin_icon icon='newobject.gif' alt=$mod->Lang('addcontent')}&nbsp;{$mod->Lang('addcontent')}</a>
      </div>
    {/if}
    {if $multiselect && isset($bulk_options)}
      <div class="pageoptions grid_6" style="text-align: right;">
        <label for="multiaction">{$mod->Lang('prompt_withselected')}:</label>&nbsp;&nbsp;
        <select name="{$actionid}multiaction" id="multiaction">
          {html_options options=$bulk_options}
        </select>
        <input type="submit" id="multisubmit" name="{$actionid}multisubmit" accesskey="s" value="{$mod->Lang('submit')}"/>
      </div>
    {/if}
  </div>
{/if}
{form_end}
<div class="clearb"></div>
