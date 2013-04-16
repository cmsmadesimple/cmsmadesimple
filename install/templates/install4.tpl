{foreach from=$errors item=error}
<div class="error">{$error}</div>
{/foreach}

<h3>{lang_install a=install_admin_header}</h3>

<p>{lang_install a=install_admin_info}</p>

<form action="{$smarty.server.PHP_SELF|htmlspecialchars}" method="post" name="page4form" id="page4form">

<table border="0" class="adminaccount">
<thead class="tbcaption">
    <tr>
    <td colspan="2">{lang_install a=install_admin_header}</td>
    </tr>
</thead>
<tbody>
	<tr class="row1">
		<td>{lang_install a=username}</td>
		<td><input class="defaultfocus" type="text" name="adminusername" value="{$values.username}" size="20" maxlength="50" /></td>
	</tr>

	<tr class="row2">
		<td>{lang_install a=install_admin_email}</td>
		<td><input type="text" name="adminemail" value="{$values.email}" size="20" maxlength="50" /></td>
	</tr>

	<tr class="row1">
		<td>{lang_install a=admin_salt}</td>
		<td>
                  <input type="checkbox" name="adminsalt"  value="1" {if $values.salt == 1} checked="checked"{/if} />
                  <br/><p class="row1">{lang_install a=install_admin_pwsalt_note}</p>
                </td>
	</tr>
	<tr class="row1">
		<td>{lang_install a=password}</td>
		<td><input type="password" name="adminpassword" value="" size="20" maxlength="50" /></td>
	</tr>

	<tr class="row2">
		<td>{lang_install a=passwordagain}</td>
		<td><input type="password" name="adminpasswordagain" value="" size="20" maxlength="50" /></td>
	</tr>
{if $mail_function_exists == true}
	<tr class="row1">
		<td>{lang_install a=install_admin_email_info}</td>
		<td>
                   <input type="checkbox" name="email_accountinfo" value="1" {if $values.email_accountinfo == 1} checked="checked"{/if} />
                   <br/><br/><p class="row1">{lang_install a=install_admin_email_note}</p>
                </td>
	</tr>
{/if}
</tbody>
</table>

<p align="center" class="continue">
	<input type="hidden" name="umask" value="{$values.umask}" />
	<input type="hidden" name="page" value="5" />
	<input type="hidden" name="default_cms_lang" value="{$default_cms_lang}" />
	<input type="submit" value="{lang_install a=install_continue}" />
</p>

</form>
