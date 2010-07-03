{* set the default help text, the javascript in the admin theme will look for this div *}
<div id="default_helptext" class="hidden">
{if $user->id gt 0}
  {mod_lang string="help-edit_user"}
{else}
  {mod_lang string="help-add_user"}
{/if}
</div>

<h3>{tr}Action{/tr}:&nbsp;{tr}{$module_action}{/tr}</h3>

{if isset($user)}
  {validation_errors for=$user}
{/if}

{mod_form action=$module_action}
{if $user->id gt 0}
  {mod_hidden name='uid' value=$user->id}
{/if}

  {mod_formrow name='username'}
    {mod_label name="name"}{tr}Username{/tr}:*{/mod_label}
    {mod_textbox name="name" value=$user->name size="25" maxlength="25"}
    {mod_helptext name='username' value='help-input_username'}
  {/mod_formrow}

  {mod_formrow name='first_name'}
    {mod_label name="first_name"}{tr}First Name{/tr}:{/mod_label}
    {mod_textbox name="first_name" value=$user->first_name size="25" maxlength="25"}
    {mod_helptext name='firstname' value='help-input_first_name'}
  {/mod_formrow}

  {mod_formrow name='last_name'}
    {mod_label name="last_name"}{tr}Last Name{/tr}:{/mod_label}
    {mod_textbox name="last_name" value=$user->last_name size="25" maxlength="25"}
    {mod_helptext name='firstname' value='help-input_last_name'}
  {/mod_formrow}

  {mod_formrow name='email'}
    {mod_label name="email"}{tr}Email Address"}{/tr}:**{/mod_label}
    {mod_textbox name="email" value=$user->email size="25" maxlength="25"}
    {mod_helptext name='email' value='help-input_email'}
  {/mod_formrow}

  {mod_formrow name='openid'}
    {mod_label name="openid"}{tr}OpenID{/tr}:{/mod_label}
    {mod_textbox name="openid" value=$user->openid size="25" maxlength="25"}
    {mod_helptext name='first_name' value='help-input_openid'}
  {/mod_formrow}

  <hr/><br/>
  {if $mod_action == 'admin_edituser'}
  {mod_formrow}
    {mod_lang string='help_password_edituser'}
  {/mod_formrow}
  {/if}

  {mod_formrow name='password'}
    {mod_label name="password"}{tr}Password{/tr}:*{/mod_label}
    {mod_password name="password" size="25" maxlength="25" value="$password"}
    {mod_helptext name='first_name' value='help-input_password'}
  {/mod_formrow}

  {mod_formrow name='repeat'}
    {mod_label name="repeat"}{tr}Repeat Password{/tr}:*{/mod_label}
    {mod_password name="repeat" size="25" maxlength="25" value="$repeat"}
    {mod_helptext name='first_name' value='help-input_repeat_password'}
  {/mod_formrow}

  <hr/><br/>
  {if $user->id gt 1}
  {mod_formrow name='active'}
    {mod_label name="active"}{tr}Active{/tr}:*{/mod_label}
    {mod_checkbox name="active" checked=$user->active}
    {mod_helptext name='first_name' value='help-input_user_active'}
  {/mod_formrow}
  {/if}

  {if count($groups)}
      <br/>
      <h5>{tr}Group Membership{/tr}:</h5><br/>
      <table class="pagetable" cellspacing="0" width="50%" border="0">
      {foreach from=$groups item='onegroup'}
	{assign var='selected' value=$onegroup->member}
        {if $onegroup->name == 'Anonymous' && $module_action == 'admin_adduser'}
          {assign var='selected' value='1'}
        {/if}
        <tr>
          <td>{assign var='tmp' value=$onegroup->id}{$onegroup->name}</td>
          <td>{mod_checkbox name="groups[$tmp]" checked=$selected}</td>
        </tr>
      {/foreach}
      </table>
      <br/>
    {/if}

    {mod_formrow name='submitrow' class='submitrow'}
      {if $module_action == 'admin_adduser'}
        {mod_submit name='submit' value='Submit' confirm_text='confirm_adduser' class="positive"}
      {else}
        {mod_submit name='submit' value='Submit' confirm_text='confirm_edituser' class="positive"}
      {/if}
      {mod_submit name='cancel' value='Cancel' class="negative"}
    {/mod_formrow}

  <table border="0" cellspacing="0">
    <tr><td>*</td><td>Indicates a required field</td>
    <tr><td>**</td><td>Indicates a recommended field</td>
  </table>
{/mod_form}
