{foreach from=$errors item=error}
<p class="error">{$error}</p>
{/foreach}

<form action="index.php" method="post" name="page3form" id="page3form">

	<h3>{lang_install a=sitename}</h3>

	<p>{lang_install a=install_admin_sitename}</p>
	<p style="text-align: center;"><input class="defaultfocus selectall" type="text" name="sitename" size="40" value="{$values.sitename}" /></p>

	<h3>{lang_install a=install_admin_db}</h3>

	{lang_install a=install_admin_db_info}

	<p>{lang_install a=install_admin_follow}:</p>
	<br />
	<table cellpadding="2" border="1" class="regtable">
		<tr class="row2">
			<td>{lang_install a=install_admin_db_type}:</td>
			<td>
				<select name="dbms">
{foreach from=$dbms_options item=option}
					<option value="{$option.name}"{if $values.db.dbms == $option.name} selected="selected"{/if} />{$option.title}</option>
{/foreach}
				</select>
{if empty($dbms_options)}
				<div class="error">{lang_install a=install_admin_no_db}</div>
{/if}
			</td>
		</tr>
		<tr class="row1">
			<td>{lang_install a=install_admin_db_host}</td>
			<td><input type="text" name="host" value="{$values.db.host}" size="20" maxlength="50" /></td>
		</tr>
		<tr class="row2">
			<td>{lang_install a=install_admin_db_name}</td>
			<td><input type="text" name="database" value="{$values.db.database}" size="20" maxlength="50" /></td>
		</tr>
		<tr class="row1">
			<td>{lang_install a=username}</td>
			<td><input type="text" name="username" value="{$values.db.username}" size="20" maxlength="50" /></td>
		</tr>
		<tr class="row2">
			<td>{lang_install a=password}</td>
			<td><input type="password" name="password" value="{$values.db.password}" size="20" maxlength="50" /></td>
		</tr>
		<tr class="row1">
			<td>Table prefix</td>
			<td>
				<input type="text" name="prefix" value="{$values.db.prefix}" size="20" maxlength="50" />
				<input type="hidden" name="page" value="4" />
				<input type="hidden" name="adminusername" value="{$values.admininfo.username}" />
				<input type="hidden" name="adminemail" value="{$values.admininfo.email}" />
				<input type="hidden" name="adminpassword" value="{$values.admininfo.password}" />
				<input type="hidden" name="email_accountinfo" value="{$values.email_accountinfo}" />

			</td>
		</tr>
		<tr class="row2">
			<td>{lang_install a=install_admin_db_create}</td>
			<td><input type="checkbox" name="createtables"{if $values.createtables == 1} checked="checked"{/if} /></td>
		</tr>
{if $extra_sql}
		<tr class="row1">
			<td>{lang_install a=install_admin_db_sample}</td>
			<td><input type="checkbox" name="createextra"{if $values.createextra == 1} checked="checked"{/if} /></td>
		</tr>
{/if}
	</table>
	<p align="center" class="continue">
{if empty($dbms_options)}
		<input type="submit" name="recheck" value="{lang_install a=retry}" />
{else}
		<input type="submit" value="{lang_install a=install_continue}" />
{/if}
		<input type="hidden" name="default_cms_lang" value="{$default_cms_lang}" />
	</p>

</form>
