{foreach from=$errors item=error}
<div class="error">{$error}</div>
{/foreach}

<h3>{lang_install a=install_admin_checksum}</h3>

<form action="{$smarty.server.PHP_SELF|strip_tags}" method="post" name="page1form" id="page1form" enctype="multipart/form-data">

<table class="settings" border="0">
		<tr class="tbcaption">
			<td colspan="2">{lang_install a=checksum}</td>
		</tr>

{if isset($results)}
	{foreach from=$results item='test'}
		<tr class="{cycle values='row1,row2'}">
			<td>
		{if isset($test->secondvalue)}
				<span class="have"><strong>{$test->secondvalue}</strong></span>
		{/if}
				{$test->value}
		{if isset($test->opt)}
				<p>{$test->opt.file_timestamp|date_format:$test->opt.format_timestamp}</p>
		{/if}
		{if isset($test->message)}
				<p>
					<em>{$test->message}</em>
				</p>
		{/if}
			</td>
			<td class="col2"><img src="images/{$test->res}.gif" alt="{$test->res_text}" title="{$test->res_text}" /></td>
		</tr>
	{/foreach}
		<tr>
			<td><strong>{lang_install a=checksum_failed}</strong></td>
			<td class="col2"><a class="external" rel="external" href="{$cms_install_help_url}#{$error_fragment}">?</a></td>
		</tr>
{else}
	{if isset($try_test)}
		<tr class="row2">
			<td><strong>{lang_install a=checksum_passed}</strong></td>
			<td class="col2"><img src="images/green.gif" alt="{lang_install a=success}" title="{lang_install a=success}" /></td>
		</tr>
	{/if}

{/if}

	
</table>


<div class="msg-botton">
	{lang_install a=install_test_checksum}<br /><br />
	<input type="file" name="cksumdat" id="cksumdat" maxlength="255" /><br />
	<input type="submit" name="recheck" value="{lang_install a=test}" />
</div>
<div class="continue">
	<input type="hidden" name="page" value="2" />
	<input type="hidden" name="default_cms_lang" value="{$default_cms_lang}" />
	<input type="submit" value="{lang_install a=install_continue}" />
</div>

</form>
