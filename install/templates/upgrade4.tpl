{foreach from=$errors item=error}
<div class="error">{$error}</div>
{/foreach}


<table class="settings" border="0">
	<thead class="tbcaption">
		<tr>
			<td colspan="2">{lang_install a=upgrade_cache_dirs}</td>
		</tr>
	</thead>
	<tbody>

		<tr class="{cycle values='row1,row2'}">
			<td>
				<strong>{lang_install a=install_admin_clear_cache}</strong>
		{if ! $result}
				<p class="error">{lang_install a=cannot_clean_cache_dirs}</p>
		{/if}
			</td>
			<td class="col2">
		{if $result}
				<img src="images/green.gif" alt="{lang_install a=success}" title="{lang_install a=success}" />
		{else}
				<img src="images/yellow.gif" alt="{lang_install a=caution}" title="{lang_install a=caution}" />
		{/if}
			</td>
		</tr>
	</tbody>
</table>


<form action="{$smarty.server.PHP_SELF|strip_tags}" method="post" name="page4form" id="page4form">
        <div class="continue">
		<input type="hidden" name="page" value="5" />
                <input type="hidden" name="default_cms_lang" value="{$default_cms_lang}" />
        </div>
{if $result}
        <div class="continue">
                <input type="submit" value="{lang_install a=install_continue}" />
        </div>
{else}
        <div class="failure">
                {lang_install a=upgrade_failed_again}
        </div>
        <div class="continue">
                <input type="submit" name="recheck" value="{lang_install a=install_try_again}" />
        </div>
{/if}
</form>
