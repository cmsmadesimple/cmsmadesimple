{if $error_msg}
<div class="pageerrorcontainer">
    <p>{$error_msg}</p>
</div>
{/if}

{mod_form action=$module_action}
  {mod_formrow name="password_minlength"}
    {mod_label name="password_minlength"}{tr}Minimum Password Length{/tr}:{/mod_label}
    {mod_textbox name='password_minlength' value=$cms_mapi_module->get_preference('password_minlength') size="5" maxlength="5"}
    {mod_helptext name='password_minlength' value="help-password_minlength"}
  {/mod_formrow}

  {mod_formrow name="username_minlength"}
    {mod_label name="username_minlength"}{tr}Minimum Username Length}{/tr}:{/mod_label}
    {mod_textbox name='username_minlength' value=$cms_mapi_module->get_preference('username_minlength') size="5" maxlength="5"}
    {mod_helptext name='username_minlength' value="help-username_minlength"}
  {/mod_formrow}

  {mod_hidden name='selected_tab' value='prefs'}
  {mod_formrow name="submitrow" class="submitrow"}
    {mod_submit name='submitprefs' value='Submit' confirm_text='Are you sure you want to adjust user preferences' class="positive"}
  {/mod_formrow}
{/mod_form}