{foreach from=$errors item='error'}
<div class="error">{$error}</div>
{/foreach}

<h3>{lang_install a=install_admin_umask}</h3>

<form action="{$smarty.server.PHP_SELF|strip_tags}" method="post" name="page3form" id="page3form">

<table class="settings" border="0">
<thead class="tbcaption">
    <tr>
    <td colspan="2">{lang_install a=global_umask}</td>
    </tr>
    </thead>
<tbody>
		<tr class="row1">
			<td colspan="2">{lang_install a=test_umask_text}</td>
		</tr>
		<tr class="row2">
			<td>{lang_install a=global_umask}</td>
			<td><input class="umask" type="text" name="umask" value="{$umask}" size="4" maxlength="4" /></td>
		</tr>

{if isset($test)}
		<tr class="{cycle values='row1,row2'}">
			<td>
				<span class="have">{$test->value}</span>
				<strong>{$test->title}:</strong>
	{if isset($test->error)}
				<p class="error">{$test->error}</p>
	{/if}
	{if isset($test->opt)}
				<p>{lang_install a=owner}: {$test->opt.username}</p>
				<p>{lang_install a=group}: {$test->opt.usergroup}</p>
				<p>{lang_install a=permissions}: {$test->opt.permsstr} ({$test->opt.permsdec})</p>
	{/if}
	{if isset($test->message)}
				<p>
					<em>{$test->message}</em>
				</p>
	{/if}
			</td>
			<td class="col2">
				<img class="{$test->res}" src="images/{$test->res}.gif" alt="{$test->res_text}" title="{$test->res_text}" />
				<a class="external" rel="external" href="{$cms_install_help_url}{if isset($error_fragment)}#{$error_fragment}{/if}">?</a>
			</td>
		</tr>
{/if}

	</tbody>
</table>


<div class="msg-botton">
	{lang_install a=install_test_umask}<br /><br />
	<input type="submit" name="recheck" value="{lang_install a=test}" />
</div>
<div class="continue">
	<input type="hidden" name="page" value="4" />
	<input type="hidden" name="default_cms_lang" value="{$default_cms_lang}" />
	<input type="submit" value="{lang_install a=install_continue}" />
</div>

</form>
