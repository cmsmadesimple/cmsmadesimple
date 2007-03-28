{foreach from=$errors item=error}
<p class="error">{$error}</p>
{/foreach}
<form action="index.php" method="post" name="page3form" id="page3form">

	<h3>Site Name</h3>
	
	<p>This is the name of your site.  It will be used in various places of the default templates and can be used anywhere with the
	{literal}{sitename}{/literal} tag.</p>
	<p style="text-align: center;"><input class="defaultfocus selectall" type="text" name="sitename" size="40" value="{$values.sitename}" /></p>
	
	<h3>Database Information</h3>
	
	<p>Make sure you have created your database and granted full privileges to a user to use that database.</p>
	<p>For MySQL, use the following:</p>
	<p>Log in to mysql from a console and run the following commands:</p>
	<ol>
	<li>create database cms; (use whatever name you want here but make sure to remember it, you&apos;ll need to enter it on this page)</li>
	<li>grant all privileges on cms.* to cms_user@localhost identified by 'cms_pass';</li>
	</ol>
	
	<p>Please complete the following fields:</p>
	
	<table cellpadding="2" border="1" class="regtable">
		<tr class="row2">
			<td>Database Type:</td>
			<td>
				<select name="dbms">
{foreach from=$dbms_options item=option}
					<option value="{$option.name}"{if $values.db.dbms == $option.name} selected{/if} />{$option.title}</option>
{/foreach}
				</select>
{if empty($dbms_options)}
				<div class="error">No valid database drivers appear to be compiled into your PHP install. Please confirm that you have mysql, mysqli, and/or postgres7 support installed, and try again.</div>
{/if}
			</td>
		</tr>
		<tr class="row1">
			<td>Database host address</td>
			<td><input type="text" name="host" value="{$values.db.host}" size="20" maxlength="50" /></td>
		</tr>
		<tr class="row2">
			<td>Database name</td>
			<td><input type="text" name="database" value="{$values.db.database}" size="20" maxlength="50" /></td>
		</tr>
		<tr class="row1">
			<td>Username</td>
			<td><input type="text" name="username" value="{$values.db.username}" size="20" maxlength="50" /></td>
		</tr>
		<tr class="row2">
			<td>Password</td>
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
			<td>Create Tables (Warning: Deletes existing data)</td>
			<td><input type="checkbox" name="createtables"{if $values.createtables == 1} checked="checked"{/if} /></td>
		</tr>
{if $extra_sql}
		<tr class="row1">
			<td>Install sample content and templates</td>
			<td><input type="checkbox" name="createextra"{if $values.createextra == 1} checked="checked"{/if} /></td>
		</tr>
{/if}
	</table>
	<p align="center" class="continue">
{if empty($dbms_options)}
		<input type="submit" name="recheck" value="Retry" />
{else}
		<input type="submit" value="Continue" />
{/if}
	</p>

</form>
