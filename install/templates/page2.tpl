{foreach from=$errors item=error}
<p class="error">{$error}</p>
{/foreach}

<h3>{lang_install a=install_admin_header}</h3>

<p>{lang_install a=install_admin_info}</p>

<form action="index.php" method="post" name="page2form" id="page2form">

<table border="0" class="adminaccount">
	<tr class="row1">
		<td>{lang_install a=username}</td>
		<td><input class="defaultfocus" type="text" name="adminusername" value="{$values.username}" size="20" maxlength="50" /></td>
	</tr>

	<tr class="row2">
		<td>{lang_install a=install_admin_email}</td>
		<td><input type="text" name="adminemail" value="{$values.email}" size="20" maxlength="50" /></td>
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
</table>

<p align="center" class="continue">
	<input type="hidden" name="page" value="3" />
	<input type="hidden" name="default_cms_lang" value="{$default_cms_lang}" />
	<input type="submit" value="{lang_install a=install_continue}" />
</p>

</form>
