<p class="important">
	{lang_install a=install_please_read}
</p>

<h3>{lang_install a=install_checking}</h3>

{$test}

<table class="settings" border="0">
	<caption class="tbcaption">{lang_install a=systeminfo}</caption>
	<tbody>
{foreach from=$settings.info key=key item=test}
		<tr class="{cycle values='row1,row2'}">
			<td>
				{lang_install a=$key}
			</td>
			<td>
				{$test}
			</td>
		</tr>
{/foreach}
	</tbody>
</table>


<table class="settings" border="0">
	<caption class="tbcaption">{lang_install a=install_required_settings}</caption>
	<thead class="tbhead">
		<tr>
			<th>{lang_install a=install_test}</th>
			<th>{lang_install a=install_result}</th>
		</tr>
	</thead>
	<tbody>
{foreach from=$settings.required item=test}
		<tr class="{cycle values='row1,row2'}">
			<td>
	{if isset($test->value) && $test->value != ''}
				<span class="have">{lang_install a=install_you_have} {$test->value}</span>
	{/if}
				{$test->title}
	{if isset($test->error)}
				<p class="error">{$test->error}</p>
	{/if}
	{if isset($test->message)}
				<p>
					<em>{$test->message}</em>
				</p>
	{/if}
			</td>
			<td class="col2"><img src="images/{$test->res}.gif" alt="{$test->res_text}" height="16" width="16" border="0" /></td>
		</tr>
{/foreach}
	</tbody>
</table>


<table class="settings" border="0">
	<caption class="tbcaption">{lang_install a=install_recommended_settings}</caption>
	<thead class="tbhead">
		<tr>
			<th>{lang_install a=install_test}</th>
			<th>{lang_install a=install_result}</th>
		</tr>
	</thead>
	<tbody>
{foreach from=$settings.recommended item=test}
		<tr class="{cycle values='row1,row2'}">
			<td>
	{if isset($test->value) && $test->value != ''}
				<span class="have">{lang_install a=install_you_have} {$test->value}</span>
	{/if}
				{$test->title}
	{if isset($test->error)}
				<p class="error">{$test->error}</p>
	{/if}
	{if isset($test->message)}
				<p>
					<em>{$test->message}</em>
				</p>
	{/if}
			</td>
			<td class="col2"><img src="images/{$test->res}.gif" alt="{$test->res_text}" height="16" width="16" border="0" /></td>
		</tr>
{/foreach}
	</tbody>
</table>


<table class="legend" border="0">
	<caption class="tbcaption">{lang_install a=install_legend}</caption>
	<thead class="tbhead">
		<tr>
			<th>{lang_install a=install_symbol}</th>
			<th>{lang_install a=install_definition}</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td><img src="images/true.gif" alt="{$success}" height="16" width="16" border="0" /></td>
			<td>{lang_install a=install_value_passed}</td>
		</tr>
		<tr>
			<td><img src="images/false.gif" alt="{$failure}" height="16" width="16" border="0" /></td>
			<td>{lang_install a=install_value_failed}</td>
		</tr>
		<tr>
			<td><img src="images/red.gif" alt="{$failure}" height="16" width="16" border="0" /></td>
			<td>{lang_install a=install_value_required}</td>
		</tr>
		<tr>
			<td><img src="images/yellow.gif" alt="{$caution}" height="16" width="16" border="0" /></td>
			<td>{lang_install a=install_value_recommended}</td>
		</tr>
		<tr>
			<td><img src="images/green.gif" alt="{$success}" height="16" width="16" border="0" /></td>
			<td>{lang_install a=install_value_exceed}</td>
		</tr>
	</tbody>
</table>


<form method="post" action="index.php">
{if $continueon}
	<p class="failure" align="center">
	{if $special_failed}
		{lang_install a=install_test_failed}<br />
		<input type="submit" name="recheck" value="{lang_install a=install_try_again}" />
	</p>
	{else}
		{lang_install a=install_test_passed}
	{/if}
	</p>
	<p class="failure" align="center">
		<input type="submit" value="{lang_install a=install_continue}" />
		<input type="hidden" name="page" value="2" />
		<input type="hidden" name="default_cms_lang" value="{$default_cms_lang}" />
	</p>
{else}
	<p class="failure" align="center">{lang_install a=install_failed_again}</p>
	<p class="continue" align="center"><input type="submit" value="{lang_install a=install_try_again}" /></p>
{/if}
</form>
