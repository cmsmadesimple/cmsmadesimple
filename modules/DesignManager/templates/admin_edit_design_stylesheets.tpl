{* stylesheets tab for edit template *}
<div class="pagewarning">{$mod->Lang('info_edittemplate_stylesheets_tab')}</div>
{if !isset($all_stylesheets)}
<div class="pagewarning">{$mod->Lang('warning_edittemplate_nostylesheets')}</div>
{/if}

{assign var='cssl' value=$design->get_stylesheets()}
<div>
  <table class="pagetable" cellspacing="0" style="border: none;">
  <tr valign="center">
    <td valign="top">
      <fieldset>
        <legend>{$mod->Lang('available_stylesheets')}:</legend>
        <select id="avail_css" multiple="multiple" size="10">
        {foreach from=$all_stylesheets item='css'}
          {if !$cssl or !in_array($css->get_id(),$cssl)}
          <option value="{$css->get_id()}">{$css->get_name()}</option>
          {/if}
        {/foreach}
        </select>
      </fieldset>
    </td>
    <td style="text-align: center;" valign="center">
      <div>{admin_icon icon='up.gif' id='css_up' title=$mod->Lang('help_move_up')}</div>
      <div>{admin_icon icon='left.gif' id='css_left' title=$mod->Lang('help_move_left')}</div>
      <div>{admin_icon icon='right.gif' id='css_right' title=$mod->Lang('help_move_right')}</div>
      <div>{admin_icon icon='down.gif' id='css_down' title=$mod->Lang('help_move_down')}</div>
    </td>
    <td valign="top">
      <fieldset>
        <legend>{$mod->Lang('attached_stylesheets')}:</legend>
        <select class="selall" id="assoc_css" name="{$actionid}assoc_css[]" multiple="multiple" size="10">
          {foreach from=$design->get_stylesheets() item='one'}
          <pre>{$one|@print_r}</pre>
          <option value="{$one}">{$list_stylesheets.$one}</option>
          {/foreach}
        </select>
      </fieldset>
    </td>
    </tr>
  </table>
</div>

<script type="text/javascript">
$(document).ready(function(){
  $('#css_right').click(function(){
    var x = $('#avail_css :selected');
    if( $(x).val() ) {
      var x1 = $(x).clone();
      $('#assoc_css').append(x1);
      $(x).remove();
    }
  });
  $('#css_left').click(function(){
    var x = $('#assoc_css :selected');
    if( $(x).val() ) {
      var x1 = $(x).clone();
      $('#avail_css').append(x1);
      $(x).remove();
    }
  });
  $('#css_up').click(function(){
    var x = $('#assoc_css :selected');
    var i = $(x).index();
    if( i > 0 ) {
      var x1 = $(x).clone().attr('selected','selected');
      $(x).remove();
      $('#assoc_css option').eq(i-1).before(x1);
    }
  });
  $('#css_down').click(function(){
    var x = $('#assoc_css :selected');
    var i = $(x).index();
    if( i < $('#assoc_css option').length - 1 ) {
      var x1 = $(x).clone().attr('selected','selected');
      $(x).remove();
      $('#assoc_css option').eq(i).after(x1);
    }
  });
});
</script>