{foreach from=$errors item=error}
<div class="error">{$error}</div>
{/foreach}

<form action="{$smarty.server.PHP_SELF|htmlspecialchars}" method="post" name="page5form" id="page5form">

	<h3>{lang_install a=sitename}</h3>

	<p>{lang_install a=install_admin_sitename}</p>
	<p style="text-align: center;"><input class="defaultfocus selectall" type="text" name="sitename" size="40" value="{$values.sitename}" /></p>

	{if isset($timezones)}
	<h3>{lang_install a=timezone}</h3>
        <p>{lang_install a=install_timezone2}</p>
        <p style="text-align: center;">
          <select name="timezone">{html_options options=$timezones selected=$values.timezone}</select>
        </p>
        {/if}
        
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
					<option value="{$option.name}"{if $values.db.dbms == $option.name} selected="selected"{/if}>{$option.title}</option>
{/foreach}
				</select>
{if empty($dbms_options)}
				<div class="error">{lang_install a=install_admin_no_db}</div>
{/if}
			</td>
		</tr>
		<tr class="row1">
			<td>{lang_install a=install_admin_db_host}</td>
			<td>
				<input type="text" name="host" value="{$values.db.host}" size="20" maxlength="50" />
			</td>
		</tr>
		<tr class="row2">
			<td>{lang_install a=install_admin_db_name}</td>
			<td>
				<input type="text" name="database" value="{$values.db.database}" size="20" maxlength="50" />
			</td>
		</tr>
		<tr class="row1">
			<td>{lang_install a=username}</td>
			<td>
				<input type="text" name="username" value="{$values.db.username}" size="20" maxlength="50" />
			</td>
		</tr>
		<tr class="row2">
			<td>{lang_install a=password}</td>
			<td>
				<input type="password" name="password" value="{$values.db.password}" size="20" maxlength="50" />
			</td>
		</tr>
		<tr class="row1">
			<td>{lang_install a=install_admin_db_port}</td>
			<td>
				<input type="text" name="db_port" value="{$values.db.db_port}" size="20" maxlength="50" />
				{lang_install a=install_admin_db_port_info}
			</td>
		</tr>
		<!-- tr class="row2">
			<td>{lang_install a=install_admin_db_socket}</td>
			<td>
				<input type="text" name="db_socket" value="{$values.db.db_socket|default:''}" size="20" maxlength="50" />
				{lang_install a=install_admin_db_socket_info}
			</td>
		</tr -->
		<tr class="row1">
			<td>{lang_install a=install_admin_db_prefix}</td>
			<td>
				<input type="text" name="prefix" value="{$values.db.prefix}" size="20" maxlength="50" />
			</td>
		</tr>
		<tr class="row2">
			<td>{lang_install a=install_admin_db_create}</td>
			<td>
				<input type="checkbox" name="createtables"{if $values.createtables == 1} checked="checked"{/if} /><br/>
				{lang_install a=install_warn_db_createtables}
			</td>
		</tr>
{if $extra_sql}
		<tr class="row1">
			<td>{lang_install a=install_admin_db_sample}</td>
			<td>
				<input type="checkbox" name="createextra"{if $values.createextra == 1} checked="checked"{/if} />
			</td>
		</tr>
{/if}
	</table>
	<div  class="continue">
		<input type="hidden" name="umask" value="{$values.umask}" />
		<input type="hidden" name="adminusername" value="{$values.admininfo.username}" />
		<input type="hidden" name="adminemail" value="{$values.admininfo.email}" />
		<input type="hidden" name="adminsalt" value="{$values.admininfo.salt|default:''}" />
		<input type="hidden" name="adminpassword" value="{$values.admininfo.password}" />
		<input type="hidden" name="email_accountinfo" value="{$values.email_accountinfo}" />

{if empty($dbms_options)}
		<input type="submit" name="recheck" value="{lang_install a=retry}" />
{else}
		<input type="submit" value="{lang_install a=install_continue}" />
{/if}
		<input type="hidden" name="page" value="6" />
		<input type="hidden" name="default_cms_lang" value="{$default_cms_lang}" />
	</div>

</form>
