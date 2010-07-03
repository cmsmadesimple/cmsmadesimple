{* UserAdmin login form *}
{if !empty($error)}
<p><strong>{$error}</strong></p>
{/if}
{mod_form action='login' inline='true'}
<p>{tr}Username{/tr}:&nbsp;{mod_textbox name='username' size='25' maxlength='25'}</p>
<p>{tr}Password{/tr}:&nbsp;{mod_password name='password' size='25' maxlength='25'}</p>
<p>{tr}OpenID{/tr}:&nbsp;{mod_text name='openid' size='15' maxlength='15'}</p>
<p>{mod_submit name='submit' value='submit'}</p>
{/mod_form}