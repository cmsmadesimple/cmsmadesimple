{foreach from=$errors item=error}
<div class="error">{$error}</div>
{/foreach}

<p>{lang_install a=install_admin_setup}</p>

<form action="{$smarty.server.PHP_SELF}" method="post" name="page6form" id="page6form">
<table cellpadding="2" border="1" class="regtable">
	<tr class="row1">
		<td>{lang_install a=install_admin_docroot}</td>
		<td>
			<input type="text" name="docroot" value="{$docroot}" size="50" maxlength="100" />
		</td>
	</tr>
	<tr class="row2">
		<td>{lang_install a=install_admin_docroot_path}</td>
		<td>
			<input type="text" name="docpath" value="{$docpath}" size="50" maxlength="100" />
		</td>
	</tr>
	<tr class="row1">
		<td>{lang_install a=install_admin_querystring}</td>
		<td>
			<input type="text" name="querystr" value="page" size="20" maxlength="20" />
		</td>
	</tr>
	<tr class="row2">
		<td>{lang_install a=install_admin_frontendlang}</td>
		<td>
			<select name="frontendlang">
				<option value="" selected="selected"> ---</option>
{foreach from=$languages item=lang}
				<option value="{$lang}">{$lang}</option>
{/foreach}
			</select>
		</td>
	</tr>
   </table>

	<div class="continue">
		<input type="hidden" name="umask" value="{$values.umask}" />
		<input type="hidden" name="host" value="{$values.db.host}" />
		<input type="hidden" name="dbms" value="{$values.db.dbms}" />
		<input type="hidden" name="database" value="{$values.db.database}" />
		<input type="hidden" name="username" value="{$values.db.username}" />
		<input type="hidden" name="password" value="{$values.db.password}" />
		<input type="hidden" name="db_port" value="{$values.db.db_port}" />
	        <input type="hidden" name="timezone" value="{$values.timezone}"/>
		<!-- input type="hidden" name="db_socket" value="{$values.db.db_socket}" / -->
		<input type="hidden" name="prefix" value="{$values.db.prefix}" />
		<input type="hidden" name="createtables" value="{$values.createtables}" />
		<input type="hidden" name="email_accountinfo" value="{$values.admininfo.email_accountinfo}" />
		<input type="hidden" name="adminemail" value="{$values.admininfo.email}" />
		<input type="hidden" name="adminusername" value="{$values.admininfo.username}" />
		<input type="hidden" name="adminpassword" value="{$values.admininfo.password}" />

		<input type="hidden" name="page" value="7" />
		<input type="hidden" name="default_cms_lang" value="{$default_cms_lang}" />
		<input type="submit" value="{lang_install a=install_continue}" />
	</div>
</form>
