<script>
$(function(){
   var url = '{cms_action_url action=defaultadmin page=xxx forjs=1}';
   $('#pagenum').change(function(){
       var v = $(this).val();
       var t_url = url.replace('xxx',v);
       window.location = t_url;
   })

   $('#filterbtn').click(function(){
       $('#filter_dlg').dialog({
           modal: true,
	   width: 'auto',
	   height: 'auto',
       })
   });
})
</script>


<div class="c_full cf">
 <a href="{cms_action_url action=download}&showtemplate=false">{admin_icon icon='attachment.gif'} {$mod->Lang('download')}</a>
 {if $mod->CheckPermission('Clear Admin Log')}
 <a href="{cms_action_url action=clear}">{admin_icon icon='delete.gif'} {$mod->Lang('clearlog')}</a>
 {/if}
 <a id="filterbtn" href="javascript:void()">{admin_icon icon='run.gif'} {$mod->Lang('filter')} ...</a>

{if $pagelist && count($pagelist) > 1}
  <div style="text-align: right; float: right;">
    {$mod->Lang('page')}:  <select id="pagenum">{html_options options=$pagelist selected=$page}</select>
  </div>
{/if}
</div>

<div id="filter_dlg" title="{$mod->Lang('filter')}" style="display: none; min-width: 500px;">
  {form_start}
  <div class="c_full cf">
    <label class="grid_3" for="f_act">{$mod->Lang('f_sev')}:</label>
    <select class="grid_8" id="f_sev" name="{$actionid}f_sev">{html_options options=$severity_list selected=$filter->severity}</select>
  </div>
  <div class="c_full cf">
    <label class="grid_3" for="f_act">{$mod->Lang('f_msg')}:</label>
    <input class="grid_8" id="f_act" name="{$actionid}f_msg" value="{$filter->msg}"/>
  </div>
  <div class="c_full cf">
    <label class="grid_3" for="f_item">{$mod->Lang('f_subj')}:</label>
    <input class="grid_8" id="f_item" name="{$actionid}f_subj" value="{$filter->subject}"/>
  </div>
  <div class="c_full cf">
    <label class="grid_3" for="f_user">{$mod->Lang('f_user')}:</label>
    <input class="grid_8" id="f_user" name="{$actionid}f_user" value="{$filter->username}"/>
  </div>
  <div class="c_full cf">
    <div class="grid_12">
       <input type="submit" name="{$actionid}filter" value="{$mod->Lang('filter')}"/>
       <input type="submit" name="{$actionid}reset" value="{$mod->Lang('reset')}"/>
    </div>
  </div>
  {form_end}
</div>

{if !empty($results) }
  <table class="pagetable">
    <thead>
      <tr>
        <th>{$mod->Lang('severity')}</th>
	<th>{$mod->Lang('date')}</th>
        <th>{$mod->Lang('subject')}</th>
	<th>{$mod->Lang('msg')}</th>
        <th>{$mod->Lang('itemid')}</th>
        <th>{lang('ip_addr')}</th>
        <th>{lang('username')}</th>
      </tr>
    </thead>
    <tbody>
      {foreach $results as $one}
        {$rowclass=''}
	{if $one.severity == 1}
	  {$rowclass='adminlog_notice'}
	{elseif $one.severity == 2}
	  {$rowclass='adminlog_warning'}
	{elseif $one.severity == 3}
	  {$rowclass='adminlog_error'}
	{/if}
      <tr class="{cycle values='row1,row2'} {$rowclass}">
        <td>{$severity_list[$one.severity]}</td>
        <td>{$one.timestamp|date_format:'%e %h. %Y %H:%M:%S'}</td>
        <td>{$one.subject}</td>
        <td>{$one.msg}</td>
        <td>{if $one.item_id != -1}{$one.item_id}{/if}</td>
        <td>{$one.ip_addr|default:''}</td>
        <td>{$one.username}</td>
      </tr>
      {/foreach}
    </tbody>
  </table>
{/if}
