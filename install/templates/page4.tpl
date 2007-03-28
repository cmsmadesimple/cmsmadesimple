{foreach from=$errors item=error}
<p class="error">{$error}</p>
{/foreach}
<p>Now let's continue to setup your configuration file, we already have most of the stuff we need. Chances are you can leave all these values alone, so when you are ready, click Continue.</p>
<form action="index.php" method="post" name="page4form" id="page4form">
<table cellpadding="2" border="1" class="regtable">
	<tr class="row1">
		<td>CMS Document root (as seen from the webserver)</td>
		<td><input type="text" name="docroot" value="{$docroot}" size="50" maxlength="100" /></td>
	</tr>
	<tr class="row2">
		<td>Path to the Document root</td>
		<td><input type="text" name="docpath" value="{$docpath}" size="50" maxlength="100" /></td>
	</tr>
	<tr class="row1">
		<td>Query string (leave this alone unless you have trouble, then edit config.php by hand)</td>
		<td>
			<input type="text" name="querystr" value="page" size="20" maxlength="20" />
			<input type="hidden" name="page" value="5" />
			<input type="hidden" name="host" value="{$values.db.host}" />
			<input type="hidden" name="dbms" value="{$values.db.dbms}" />
			<input type="hidden" name="database" value="{$values.db.database}" />
			<input type="hidden" name="username" value="{$values.db.username}" />
			<input type="hidden" name="password" value="{$values.db.password}" />
			<input type="hidden" name="prefix" value="{$values.db.prefix}" />
			<input type="hidden" name="createtables" value="{$values.createtables}" />
{if $values.email_accountinfo}
			<input type="hidden" name="email_accountinfo" value="{$values.email_accountinfo}" />
			<input type="hidden" name="adminemail" value="{$values.admininfo.email}" />
			<input type="hidden" name="adminusername" value="{$values.admininfo.username}" />
			<input type="hidden" name="adminpassword" value="{$values.admininfo.password}" />
{/if}
		</td>
	</tr>
   </table>
   <p align="center" class="continue"><input type="submit" value="Continue" /></p>
</form>
