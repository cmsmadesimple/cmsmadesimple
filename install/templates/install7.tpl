{foreach from=$errors item=error}
<div class="error">{$error}</div>
{/foreach}

{if empty($errors)}
  <div class="success">
  {if isset($tables_notinstalled)}
    {lang_install a=install_admin_tablesnotcreated} {$site_link}
  {else}
    {lang_install a=install_admin_congratulations} {$site_link}
  {/if}
  </div>
{/if}

<div class="continue">
<form action="{$base_url}/admin/login.php" method="post" name="page7form" id="page7form">
	<input type="submit" value="{lang_install a=go_to_admin}" />
	<input type="hidden" name="username" value="{$values.admininfo.username}" />
	<input type="hidden" name="password" value="{$values.admininfo.password}" />
	<input type="hidden" name="loginsubmit" value="1" />
	<input type="hidden" name="redirect_url" value="{$base_url}/admin/" />
</form>
</div>

