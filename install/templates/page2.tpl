{foreach from=$errors item=error}
<p class="error">{$error}</p>
{/foreach}
<h3>Admin Account Information</h3>
<p>
Select the username, password and email address for your admin account.  Please make sure you record this password somewhere, as
there will be no other way to login to your CMS Made Simple admin system without it.
</p>

<form action="index.php" method="post" name="page2form" id="page2form">

<table border="0" class="adminaccount">

	<tr class="row1">
		<td>Username</td>
		<td><input class="defaultfocus" type="text" name="adminusername" value="{$values.username}" size="20" maxlength="50" /></td>
	</tr>

	<tr class="row2">
		<td>E-mail Address</td>
		<td><input type="text" name="adminemail" value="{$values.email}" size="20" maxlength="50" /></td>
	</tr>

	<tr class="row1">
		<td>Password</td>
		<td><input type="password" name="adminpassword" value="" size="20" maxlength="50" /></td>
	</tr>

	<tr class="row2">
		<td>Password Again</td>
		<td><input type="password" name="adminpasswordagain" value="" size="20" maxlength="50" /></td>
	</tr>
{if $mail_function_exists == true}
	<tr class="row1">
		<td>E-Mail Account Information</td>
		<td><input type="checkbox" name="email_accountinfo" value="1" {if $values.email_accountinfo == 1} checked="checked"{/if} /></td>
	</tr>
{/if}
</table>

<p align="center" class="continue">
	<input type="hidden" name="page" value="3" />
	<input type="submit" value="Continue" />
</p>

</form>
