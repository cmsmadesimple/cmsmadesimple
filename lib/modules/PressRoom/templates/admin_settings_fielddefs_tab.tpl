<style>
.text-center {
   text-align: center;
}
#fielddeflist {
   cursor: move;
}
#savezone {
   display: none;
}
</style>
<script>
$(function(){
  var opts = {
      cursor: 'move',
      stop: function( event, ui ) {
        $('#savezone').show();
      }
    }
  $('#fielddeflist').sortable( opts )
  $('#savebtn').click(function(ev){
     ev.preventDefault();
     var form = $(this).closest('form');
     var str = $('#fielddeflist').sortable('toArray', { attribute: 'data-id' }).join(',');
     $('#datafld').val(str);
     form.submit();
  })
})
</script>

{form_start action=admin_edit_fielddef}
<div class="c_full cf">
   <label class="grid_3" for="fldtype">{$mod->Lang('add_fielddef')}</label>
   <select class="grid_6" name="fldtype">
      {html_options options=$field_type_list}
   </select>
   <button>{$mod->Lang('submit')}</button>
</div>
{form_end}

{if empty($fielddefs)}
   <div class="information">{$mod->Lang('warn_nofielddefs')}</div>
{else}
   <div class="c_full cf text-center" id="savezone">
       <form method="post" action="{cms_action_url action=admin_order_fielddefs forjs=1}">
          <input type="hidden" id="datafld" name="data"/>
          <button id="savebtn">{$mod->Lang('btn_save_order')}</button>
       </form>
   </div>
   <table class="pagetable">
      <thead>
          <tr>
	      <th>{$mod->lang('lbl_name')}</th>
	      <th>{$mod->lang('lbl_label')}</th>
	      <th>{$mod->lang('lbl_type')}</th>
	      <th class="pageicon">{* edit *}</th>
	      <th class="pageicon">{* del *}</th>
	  </tr>
      </thead>
      <tbody id="fielddeflist">
          {foreach $fielddefs as $def}
	     {cms_action_url action=admin_edit_fielddef fdid=$def->id assign=edit_url}
	     <tr class="{cycle values='row1,row2'}" data-id="{$def->id}">
	        <td><a href="{$edit_url}" title="{$mod->Lang('t_edit_fielddef')}">{$def->name}</a></td>
	        <td>{$def->label}</td>
	        <td>{$field_type_list[$def->type]}</td>
		<td>
		    <a href="{$edit_url}" title="{$mod->Lang('t_edit_fielddef')}">{admin_icon icon='edit'}</a>
		</td>
		<td>
		    <a href="{cms_action_url action=admin_del_fielddef fdid=$def->id}" title="{$mod->Lang('t_del_fielddef')}">{admin_icon icon='delete'}</a>
		</td>
	     </tr>
          {/foreach}
      </tbody>
   </table>
{/if}