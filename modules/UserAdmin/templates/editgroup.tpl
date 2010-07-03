<h3>{tr}Action{/tr}:&nbsp;{tr}{$module_action}{/tr}</h3>

{if isset($group)}
  {validation_errors for=$group}
{/if}

{mod_form action=$module_action}
{if $group->id gt 0}
  {mod_hidden name='gid' value=$group->id}
{/if}

  <p>
    {mod_label name='groupname'}{tr}name{/tr}{/mod_label}:*<br/>
    {mod_textbox name='groupname' value=$group->name size="25" maxlength="25"}
  </p>
  {if $group->id gt 1}
  <p>
    {mod_label name="active"}{tr}active{/tr}{/mod_label}:*<br/>
    {mod_checkbox name="active" checked=$group->active}
  </p>
  {/if}

  <p>
  {if $action_name == 'admin_addgroup'}
    {mod_submit name='submit' value='Submit' confirm_text='confirm_addgroup' class="positive"}
  {else}
    {mod_submit name='submit' value='Submit' confirm_text='confirm_editgroup' class="positive"}
  {/if}
    {mod_submit name='cancel' value='Cancel' class="negative"}
  </p>
  <table border="0" cellspacing="0">
    <tr><td>*</td><td>Indicates a required field</td>
    <tr><td>**</td><td>Indicates a recommended field</td>
  </table>
{/mod_form}