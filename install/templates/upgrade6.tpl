{foreach from=$errors item=error}
<div class="error">{$error}</div>
{/foreach}


<table class="settings" border="0">
	<thead class="tbcaption">
		<tr>
			<td colspan="2">{lang_install a=upgrade_modules}</td>
		</tr>
	</thead>
	<tbody>

		<tr class="{cycle values='row1,row2'}">
			<td>
{foreach from=$test->messages item=message}
				<p>{$message}</p>
{/foreach}
			</td>
			<td class="col2">
		{if ! $test->error}
				<img src="images/green.gif" alt="{lang_install a=success}" title="{lang_install a=success}" />
		{else}
				<img src="images/yellow.gif" alt="{lang_install a=caution}" title="{lang_install a=caution}" />
		{/if}
			</td>
		</tr>
	</tbody>
</table>


<form action="{$smarty.server.PHP_SELF|strip_tags}" method="post" name="page6form" id="page6form">
        <div class="continue">
		<input type="hidden" name="page" value="7" />
                <input type="hidden" name="default_cms_lang" value="{$default_cms_lang}" />
        </div>
{if ! $test->error}
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
