{if count($users)}
  <div class="pageoverflow">
  <table class="pagetable" cellspacing="0">
    <thead>
      <tr>
        <th>{tr}Name{/tr}</td>
        <th class="pageicon">{tr}Active{/tr}</td>
        <th class="pageicon">&nbsp;</td>
        <th class="pageicon">&nbsp;</td>
      </tr>
    </thead>
  {foreach from=$users item='oneuser'}
    {cycle values='row1,row2' assign='rowclass'}
    <tr class="{$rowclass}" onmouseover="this.classame='{$rowclass}hover';" onmouseout="this.className='{$rowclass}';">
       <td>{$oneuser->name}{if $oneuser->id == 1}&nbsp;<i>({tr}Special User{/tr}*){/if}</td>
       <td>{if $oneuser->active}{mod_link uid=$oneuser->id action='admin_setactive' value='Set Inactive' theme_image='icons/system/true.gif'}{else}{mod_link uid=$oneuser->id action='admin_setactive' value='Set Active' theme_image='icons/system/false.gif'}{/if}</td>
       <td>{mod_link action='admin_edituser' uid=$oneuser->id value='Edit User' theme_image='icons/system/edit.gif'}</td>
       <td>{if $oneuser->id != 1}{mod_link action='admin_deleteuser' uid=$oneuser->id value='Delete User' theme_image='icons/system/delete.gif' warn_message='confirm_deleteuser'}{/if}</td>
     </tr>
  {/foreach}
  </table>
  </div>
  <br/>
{/if}
<div class="pageoverflow">
  {mod_link action='admin_adduser' value='Add User' theme_image='icons/system/newobject.gif' showtext='1'}
</div>
