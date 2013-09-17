{if $ajax == 0}
<script type="text/javascript">
//<![CDATA[
function cms_CMloadUrl(link, lang) {
    $(document).on('click', link, function (e) {
        var url = $(this).attr('href') + '&showtemplate=false&{$actionid}ajax=1';
        if (typeof lang == 'string' && lang.length > 0) {
            if (!confirm(lang)) return false;
        }
        $('#contenttable').load(url + ' #contenttable > *');
	$('#ajax_find').val('');
        e.preventDefault();
    });
}

function cms_CMtoggleState(el) {
    $(el).attr('disabled', true);

    $(document).on('click', 'input:checkbox', function () {
        if ($(this).is(':checked')) {
            $(el).attr('disabled', false);
            $('button' + el).button({ 'disabled' : false });
        } else {
            $(el).attr('disabled', true);
            $('button' + el).button({ 'disabled' : true });
        }
    });
}

$(document).ready(function () {

    cms_CMtoggleState('#multiaction'),
    cms_CMtoggleState('#multisubmit'),
    cms_checkAll('#selectall'),
    // these links can't use ajax as they effect pagination.
    //cms_CMloadUrl('a.expandall'),
    //cms_CMloadUrl('a.collapseall'),
    //cms_CMloadUrl('a.page_collapse'),
    //cms_CMloadUrl('a.page_expand'),
    cms_CMloadUrl('a.page_setinactive', '{$mod->Lang('confirm_setinactive')}'),
    cms_CMloadUrl('a.page_setactive'),
    cms_CMloadUrl('a.page_setdefault', '{$mod->Lang('confirm_setdefault')}'),
    cms_CMloadUrl('a.page_view', '{$mod->Lang('confirm_viewpage')}'),
    cms_CMloadUrl('a.page_sortup'),
    cms_CMloadUrl('a.page_sortdown'),
    cms_CMloadUrl('a.page_delete', '{$mod->Lang('confirm_delete_page')}');

    $('a.steal_lock').on('click',function(e) {
      // we're gonna confirm stealing this lock.
      var v = confirm('{$mod->Lang('confirm_steal_lock')}');
      $(this).data('steal_lock',v);
      if( v ) {
        var url = $(this).attr('href');
        url = url + '{$actionid}steal=1';
        $(this).attr('href',url);
      }
    });

    $('a.page_edit').on('click',function() {
      var v = $(this).data('steal_lock');
      $(this).removeData('steal_lock');
      if( typeof(v) != 'undefined' && v != null && !v ) return false;
      if( typeof(v) == 'undefined' || v != null ) return true;

      // do a double check to see if this page is locked or not.
      var content_id = $(this).attr('data-cms-content');
      var url = '{$admin_url}/ajax_lock.php?showtemplate=false';
      var opts = { opt: 'check', type: 'content', oid: content_id };
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
	      alert('{$mod->Lang('error_contentlocked')}');
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

    $(document).on('click', '#myoptions', function () {
        $('#useroptions').dialog({
            resizable: false,
            buttons: {
                '{$mod->Lang('submit')}': function () {
                    $(this).dialog('close');
                    $('#myoptions_form').submit();
                },
                '{$mod->Lang('cancel')}': function () {
                    $(this).dialog('close');
                },
            }
        });
    });
    
    $('#ajax_find').keypress(function (e) {
        if (e.which == 13) e.preventDefault();
    });
    
    $('#ajax_find').autocomplete({
        source: '{cms_action_url action=admin_ajax_pagelookup forjs=1}&showtemplate=false',
        minLength: 2,
        position: {
            my: "right top",
            at: "right bottom"
        },
        select: function (event, ui) {
            $(this).val(ui.item.label);
            var url = '{cms_action_url action=defaultadmin forjs=1}&showtemplate=false&{$actionid}ajax=1&{$actionid}seek=' + ui.item.value;
            $('#contenttable').load(url + ' #contenttable > *');
            event.preventDefault();
        }
    });
    
    // go to page on option change
    $(document).on('change', '#{$actionid}curpage', function () {
        $(this).closest('form').submit();
    })

    $(document).ajaxComplete(function () {
        $('tr.selected').css('background', 'yellow');
    });

});
//]]>
</script>
<div id="useroptions" style="display: none;" title="{$mod->Lang('title_userpageoptions')}">
{form_start action='defaultadmin' id='myoptions_form'}
	<div class="pageoverflow">
		<input type="hidden" name="{$actionid}setoptions" value="1"/>
			<p class="pagetext">{$mod->Lang('prompt_pagelimit')}:</p>
			<p class="pageinput">
			<select name="{$actionid}pagelimit">
				{html_options options=$pagelimits selected=$pagelimit}
			</select>
			</p>
	</div>
{form_end}
</div>

<div class="clearb"></div>
{/if}{* ajax *}

<div class="row c_full">
	<div class="pageoptions grid_6">
		<ul class="options-menu">
			<li>
			{if $npages > 1}
				{form_start action='defaultadmin'}
					<span>{$mod->Lang('page')}:&nbsp;
					<select name="{$actionid}curpage" id="{$actionid}curpage">
						{html_options options=$pagelist selected=$curpage}
					</select>
					<button name="{$actionid}submitpage" class="invisible ui-button ui-widget ui-corner-all ui-state-default ui-button-text-icon-primary">
						<span class="ui-button-icon-primary ui-icon ui-icon-circle-check"></span>
						<span class="ui-button-text">{$mod->Lang('submit')}</span>
					</button>
					</span>
				{form_end}
			{/if}
			</li>
			
			{if $can_add_content}
			<li><a  href="{cms_action_url action=admin_editcontent}" accesskey="n" title="{$mod->Lang('addcontent')}" class="pageoptions">{admin_icon icon='newobject.gif' alt=$mod->Lang('addcontent')}&nbsp;{$mod->Lang('addcontent')}</a></li>
			{/if}
			
			<li class="parent">{admin_icon icon='run.gif' alt=$mod->Lang('prompt_options')}&nbsp;{$mod->Lang('prompt_options')}
				<ul id="popupmenucontents">
					{if isset($content_list)}
						<li><a class="expandall" href="{cms_action_url action='defaultadmin' expandall=1}" accesskey="e" title="{$mod->Lang('prompt_expandall')}">{admin_icon icon='expandall.gif' alt=$mod->Lang('expandall')}&nbsp;{$mod->Lang('expandall')}</a></li>
						<li><a class="collapseall" href="{cms_action_url action='defaultadmin' collapseall=1}" accesskey="c" title="{$mod->Lang('prompt_collapseall')}">{admin_icon icon='contractall.gif' alt=$mod->Lang('contractall')}&nbsp;{$mod->Lang('contractall')}</a></li>
					{/if}

					{if $can_reorder_content}
					<li><a id="ordercontent" href="{cms_action_url action=admin_ordercontent}" accesskey="r" title="{$mod->Lang('prompt_ordercontent')}">{admin_icon icon='reorder.gif' alt=$mod->Lang('reorderpages')}&nbsp;{$mod->Lang('reorderpages')}</a></li>
					{/if}
					<li><a id="myoptions" accesskey="o" title="{$mod->Lang('prompt_settings')}">{admin_icon icon='edit.gif' alt=$mod->Lang('prompt_settings')}&nbsp;{$mod->lang('prompt_settings')}</a></li>
				</ul>
			</li>
		</ul>
	</div>

	{if isset($content_list)}
	<div class="pageoptions options-form grid_6">
		<span><label for="ajax_find">{$mod->Lang('find')}:</label>&nbsp;<input type="text" id="ajax_find" name="ajax_find" title="{$mod->Lang('title_listcontent_find')}" value="" size="25"/></span>
	</div>
	{/if}
</div>

{form_start action='defaultadmin' id='listform'}
<div id="contentlist">{* everything from here down is part of the ajax stuff *}
{if isset($content_list)}
	{function do_content_row}
	<div id="content_{$row.id}" style="display: none;"></div>
		{foreach from=$columns key='column' item='flag'}
			{if $flag == 0}{continue}{/if}
			<td>
				{if $column == 'expand'}
					{if $row.expand == 'open'}
						<a href="{cms_action_url action='defaultadmin' collapse=$row.id}" class="page_collapse" accesskey="C" title="{$mod->Lang('prompt_page_collapse')}">
							{admin_icon icon='contract.gif' class="hier_contract"}
						</a>
					{elseif $row.expand == 'closed'}
						<a href="{cms_action_url action='defaultadmin' expand=$row.id}" class="page_expand" accesskey="c" title="{$mod->Lang('prompt_page_expand')}">{admin_icon icon='expand.gif' class="hier_expand"}</a>
					{/if}
				{elseif $column == 'hier'}
						{$row.hier}
				{elseif $column == 'page'}
					{if $row.can_edit}
						{repeat string='-&nbsp;&nbsp;' times=$row.depth-2}	
							{* the tooltip *}
							{capture assign='tooltip_pageinfo'}{strip}
								<strong>{$mod->Lang('prompt_content_id')}:</strong> {$row.id}<br/>
								<strong>{$mod->Lang('prompt_title')}:</strong> {$row.title}<br/>
								<strong>{$mod->Lang('prompt_name')}:</strong> {$row.menutext}<br/>
								<strong>{$mod->Lang('prompt_alias')}:</strong> {$row.alias}<br/>
								{if $row.secure}
								<strong>{$mod->Lang('prompt_secure')}:</strong> {$mod->Lang('yes')}<br/>
								{/if}
								<strong>{$mod->Lang('prompt_cachable')}:</strong> {if $row.cachable}{$mod->Lang('yes')}{else}{$mod->Lang('no')}{/if}<br/>
							{/strip}{/capture}
	
						<a href="{cms_action_url action='admin_editcontent' content_id=$row.id}" class="page_edit tooltip" accesskey="e" data-cms-content='{$row.id}' data-cms-description='{$tooltip_pageinfo|htmlentities}'>
							{$row.page}
						</a>
					{else}
						{if isset($row.lock)}
							{capture assign='tooltip_lockinfo'}{strip}
							{if $row.can_steal}
							<strong>{$mod->Lang('locked_steal')}:</strong><br/>
							{/if}
							<strong>{$mod->Lang('locked_by')}:</strong> {$row.lockuser}<br/>
							<strong>{$mod->Lang('locked_since')}:</strong> {$row.lock.created|date_format:'%x %H:%M'}<br/>
							{if $row.lock.expires < $smarty.now}<span style="color: red;"><strong>{$mod->Lang('lock_expired')}:</strong> {$row.lock.expires|relative_time}</span>{else}<strong>{$mod->Lang('lock_expires')}:</strong> {$row.lock.expires|relative_time}{/if}<br/>
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
					{$row.alias}
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
					{/if}
				{elseif $column == 'friendlyname'}
					{$row.friendlyname}
				{elseif $column == 'owner'}
					
					{capture assign='tooltip_ownerinfo'}{strip}
						<strong>{$mod->Lang('prompt_created')}:</strong> {$row.created|cms_date_format}<br/>
						<strong>{$mod->Lang('prompt_lastmodified')}:</strong> {$row.lastmodified|cms_date_format}<br/>
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
					{else if $row.default == 'no'}
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
					{if $row.delete != ''}
						<a href="{cms_action_url action='defaultadmin' delete=$row.id}" class="page_delete" accesskey="r">
						{admin_icon icon='delete.gif' class='page_delete' title=$mod->Lang('prompt_page_delete')}
						</a>
					{/if}
				{elseif $column == 'multiselect'}
					{if $row.multiselect != ''}
						<label class="invisible" for="multicontent-{$row.id}">{$mod->Lang('prompt_multiselect_toggle')}</label>
						<input type="checkbox" class="multicontent" name="{$actionid}multicontent[]" value="{$row.id}" title="{$mod->Lang('prompt_multiselect_toggle')}"/>
					{/if}
				{else}
					{* unknown column *}
				{/if}
			</td>
		{/foreach}
	{/function}

<div id="error_cont" class="error" style="color-red: text-align: center; vertical-align: middle; display: none;">
{$error|default:''}
</div>

{strip}
<table id="contenttable" cellspacing="0" class="pagetable" width="100%">
	<thead>
		<tr>
			{foreach from=$columns key='column' item='flag'}
				{if $flag}
				<th>
					{if $column == 'expand' or $column == 'hier' or $column == 'view' or $column == 'copy' or $column == 'edit' or $column == 'delete'}
					<span title="{$mod->Lang("coltitle_{$column}")}">&nbsp;</span>{* no colum header *}
					{elseif $column == 'multiselect'}
						<input type="checkbox" id="selectall" value="1" title="{$mod->Lang('select_all')}"/>
					{elseif $column == 'page'}
						<span title="{$coltitle_page}">{$colhdr_page}</span>
					{else}
						<span title="{$mod->Lang("coltitle_{$column}")}">{$mod->Lang("colhdr_{$column}")}</span>
					{/if}
				</th>
				{/if}
			{/foreach}
		</tr>
	</thead>
	<tbody class="contentrows">
	{foreach from=$content_list item='row'}
	{cycle values="row1,row2" assign='rowclass'}	
		<tr id="row_{$row.id}" class="{$rowclass} {if isset($row.selected)}selected{/if}">
			{do_content_row|strip row=$row columns=$columns}
		</tr>
	{/foreach}
	</tbody>
</table>
{/strip}

{/if}
</div>{* #contentlist *}

{if isset($content_list) && $multiselect}
<div class="pageoptions" style="float: right">
	<label for="multiaction">{$mod->Lang('prompt_withselected')}:</label>&nbsp;&nbsp;
	<select name="{$actionid}multiaction" id="multiaction">
		{html_options options=$bulk_options}
	</select>
	<input type="submit" id="multisubmit" name="{$actionid}multisubmit" accesskey="s" value="{$mod->Lang('submit')}"/>
</div>
{/if}

{form_end}
<div class="clearb"></div>