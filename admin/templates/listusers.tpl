<script type="text/javascript">
function toggle_bulk()
{
  var l = $('.multiselect:checked').length;
  if( l > 0 ) {
    $('#withselected').removeAttr('disabled');
    $('#bulksubmit').removeAttr('disabled');
    if( $('#bulksubmit').hasClass('ui-button') ) {
      $('#bulksubmit').button('option','disabled',false);
    }
  } else {
    $('#withselected').attr('disabled','disabled');
    $('#bulksubmit').attr('disabled','disabled');
    if( $('#bulksubmit').hasClass('ui-button') ) {
      $('#bulksubmit').button('option','disabled',true);
    }
  }
}

$(document).ready(function(){
  toggle_bulk();
  $('.toggleactive').click(function(){
    return confirm('{lang('confirm_toggleuseractive')}');
  });
  $(document).on('click', '#bulksubmit', function(){
    return confirm('{lang('confirm_bulkuserop')}');
  });
  $('.multiselect').click(function(){ 
    toggle_bulk();
    $('#sel_all').removeAttr('checked');
  });
  $('#sel_all').click(function(){
    var v = $(this).attr('checked');
    if( v == 'checked' ) {
      $('.multiselect').attr('checked','checked');
    } else {
      $('.multiselect').removeAttr('checked');
    }
    toggle_bulk();
  });
  $('#withselected').change(function(){
    var v = $(this).val();
    if( v == 'copyoptions' ) {
      $('#userlist').show();
    } else {
      $('#userlist').hide();
    }
  });
});
</script>

 <div class="pageoptions">
    <a href="adduser.php{$urlext}" title="{lang('info_adduser')}">{admin_icon icon='newobject.gif'}&nbsp;{lang('adduser')}</a>
</div>


  {form_start url='listusers.php'}
  <table class="pagetable" cellspacing="0">
    <thead>
      <tr>
        <th>{lang('username')}</th>
        <th style="text-align: center;">{lang('active')}</th>
        <th class="pageicon"></th>
        <th class="pageicon"></th>
        <th class="pageicon"><input type="checkbox" id="sel_all" value="1" title="{lang('selectall')}"/></th>
      </tr>
    </thead>
    <tbody>
    {foreach from=$users item='user'}
      <tr>
        {$can_edit=1}
        {if !$user->access_to_user }
          {$can_edit=0}
        {/if}
        <td>
           {if $can_edit}
              <a href="edituser.php{$urlext}&amp;user_id={$user->id}" title="{lang('edituser')}">{$user->username}</a>
           {else}
              <span title="{lang('info_noedituser')}">{$user->username}</span>
           {/if}
        </td>
        <td style="text-align: center;">
           {if $can_edit && $user->id != $my_userid}
            <a href="listusers.php{$urlext}&amp;toggleactive={$user->id}" title="{lang('info_user_active2')}" class="toggleactive">
             {if $user->active}{admin_icon icon='true.gif'}{else}{admin_icon icon='false.gif'}{/if}
            </a>
           {/if}
        </td>
        <td>
          {if $can_edit}
            <a href="edituser.php{$urlext}&amp;user_id={$user->id}" title="{lang('edituser')}">{admin_icon icon='edit.gif'}</a>
          {/if}
        </td>
        <td>
          {if $can_edit && $user->id != $my_userid}
            <a href="deleteuser.php{$urlext}&amp;user_id={$user->id}" title="{lang('deleteuser')}">{admin_icon icon='delete.gif'}</a>
          {/if}
        </td>
        <td>
          {if $can_edit && $user->id != $my_userid}
            <input class="multiselect" type="checkbox" name="multiselect[]" value="{$user->id}" title="{lang('info_selectuser')}"/>
          {/if}
        </td>
      </tr>
    {/foreach}
    </tbody>
  </table>
  <div class="pageoptions">
    <div style="width: 40%; float: left;">
      <a href="adduser.php{$urlext}" title="{lang('info_adduser')}">{admin_icon icon='newobject.gif'}&nbsp;{lang('adduser')}</a>
    </div>
    <div style="width: 40%; float: right; text-align: right;">
      <label for="withselected">{lang('selecteditems')}:</label>&nbsp;
      <select name="bulkaction" id="withselected">
        <option value="delete">{lang('delete')}</option>
        <option value="clearoptions">{lang('clearusersettings')}</option>
        <option value="copyoptions">{lang('copyusersettings2')}</option>
        <option value="disable">{lang('disable')}</option>
        <option value="enable">{lang('enable')}</option>
      </select>&nbsp;
      <div id="userlist" style="display: none;">
        <label for="userlist_sub">{lang('copyfromuser')}:</label>&nbsp;
        <select name="userlist" id="userlist_sub">
          {html_options options=$userlist}
        </select>&nbsp;
      </div>
      <input type="submit" id="bulksubmit" name="bulk" value="{lang('submit')}"/>
    </div>
  </div>
  {form_end}
