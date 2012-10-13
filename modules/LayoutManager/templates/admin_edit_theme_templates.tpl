<fieldset class="pageoverflow" style="color:black;padding:5px;background-color:white;border:2px dotted orange">{$mod->Lang('info_edittemplate_templates_tab')}</fieldset>
{if !isset($all_templates)}
<fieldset class="pageoverflow" style="color:black;padding:5px;background-color:white;border:2px dotted red">{$mod->Lang('warning_edittemplate_notemplates')}</fieldset>
{else}

{assign var='tmpl' value=$theme->get_templates()}
<table class="pagetable" cellspacing="0" style="border: none;">
<tr valign="center">
  <td valign="top">
    <fieldset>
      <legend>{$mod->Lang('available_templates')}:</legend>
      <select id="avail_tpl" multiple="multiple" size="10">
      {foreach from=$all_templates item='tpl'}
        {if !$tmpl || !in_array($tpl->get_id(),$tmpl)}
          <option value="{$tpl->get_id()}">{$tpl->get_name()}</option>
        {/if}
      {/foreach}
      </select>
    </fieldset>
  </td>
  <td style="text-align: center;" valign="center">
    <div>{admin_icon icon='up.gif' id='tpl_up' title=$mod->Lang('help_move_up')}</div>
    <div>{admin_icon icon='left.gif' id='tpl_left' title=$mod->Lang('help_move_left')}</div>
    <div>{admin_icon icon='right.gif' id='tpl_right' title=$mod->Lang('help_move_right')}</div>
    <div>{admin_icon icon='down.gif' id='tpl_down' title=$mod->Lang('help_move_down')}</div>
  </td>
  <td valign="top">
    <fieldset>
      <legend>{$mod->Lang('attached_templates')}:</legend>
      <select class="selall" id="assoc_tpl" name="{$actionid}assoc_tpl[]" size="10">
      {foreach from=$all_templates item='tpl'}
        {if $tmpl && in_array($tpl->get_id(),$tmpl)}
          <option value="{$tpl->get_id()}">{$tpl->get_name()}</option>
        {/if}
      {/foreach}
      </select>
    </fieldset>
  </td>
  </tr>
</table>

<script type="text/javascript">
$(document).ready(function(){
  $('#tpl_right').click(function(){
    var x = $('#avail_tpl :selected');
    if( $(x).val() ) {
      var x1 = $(x).clone();
      $('#assoc_tpl').append(x1);
      $(x).remove();
    }
  });
  $('#tpl_left').click(function(){
    var x = $('#assoc_tpl :selected');
    if( $(x).val() ) {
      var x1 = $(x).clone();
      $('#avail_tpl').append(x1);
      $(x).remove();
    }
  });
  $('#tpl_up').click(function(){
    var x = $('#assoc_tpl :selected');
    var i = $(x).index();
    if( i > 0 ) {
      var x1 = $(x).clone().attr('selected','selected');
      $(x).remove();
      $('#assoc_tpl option').eq(i-1).before(x1);
    }
  });
  $('#tpl_down').click(function(){
    var x = $('#assoc_tpl :selected');
    var i = $(x).index();
    if( i < $('#assoc_tpl option').length - 1 ) {
      var x1 = $(x).clone().attr('selected','selected');
      $(x).remove();
      $('#assoc_tpl option').eq(i).after(x1);
    }
  });
});
</script>

{/if}