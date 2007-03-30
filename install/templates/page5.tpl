{foreach from=$errors item=error}
<p class="error">{$error}</p>
{/foreach}
{if empty($errors)}
<h4>Congratulations, you are all setup - here is your <a href="{$homepage_link}">CMS site</a></h4>
{/if}
{if $modman_installed}
<form action="{$homepage_link}/admin/login.php" method="post">
	<input type="submit" value="Install additional modules">
	<input type="hidden" name="username" value="{$values.admininfo.username}" />
	<input type="hidden" name="password" value="{$values.admininfo.password}" />
	<input type="hidden" name="loginsubmit" value="1" />
	<input type="hidden" name="redirect_url" value="{$homepage_link}/admin/moduleinterface.php?module=ModuleManager" />
</form>
{/if}
