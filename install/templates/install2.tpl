<p class="important">
	{lang_install a=install_please_read}
</p>

<h3>{lang_install a=install_checking}</h3>



<table class="settings" border="0">
	<thead class="tbcaption">
		<tr>
			<td colspan="2">
				{lang_install a=systeminfo}
			</td>
		</tr>
	</thead>
	<tbody>
{foreach from=$settings.info key='key' item='test'}
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
	<thead class="tbcaption">
		<tr>
			<td colspan="2">
				{lang_install a=install_required_settings}
			</td>
		</tr>
	</thead>
	<tbody>
		<tr class="tbhead">
			<th>
				{lang_install a=install_test}
			</th>
			<th>
				{lang_install a=install_result}
			</th>
		</tr>
	
{foreach from=$settings.required item='test'}
		<tr class="{cycle values='row1,row2'}">
			<td>
	{if isset($test->value) && $test->value != '' && $test->display_value != 0}
				<span class="have">{lang_install a=install_you_have} {$test->value}</span>
	{/if}
				{$test->title}
	{if isset($test->error)}
				<p class="error">{$test->error}</p>
	{/if}
	{if isset($test->message)}
				<p><em>{$test->message}</em></p>
	{/if}
	{if isset($test->opt)}
		{foreach from=$test->opt key='key' item='opt'}
				<p><img src="images/{$opt.res}.gif" alt="{$opt.res_text}" title="{$opt.res_text}" /> {$key}: {$opt.message}</p>
		{/foreach}
	{/if}
			</td>
			<td class="col2">
				<img class="{$test->res}" src="images/{$test->res}.gif" alt="{$test->res_text}" title="{$test->res_text}" />
	{if isset($test->error_fragment)}
				<a class="external" rel="external" href="{$cms_install_help_url}#{$test->error_fragment}">?</a>
	{/if}
			</td>
		</tr>
{/foreach}
	</tbody>
</table>



<table class="settings" border="0">
	<thead class="tbcaption">
		<tr>
			<td colspan="2">
				{lang_install a=install_recommended_settings}
			</td>
		</tr>
	</thead>
<tbody>

<tr class="tbhead">
			<th>
				{lang_install a=install_test}
			</th>
			<th>
				{lang_install a=install_result}
			</th>
		</tr>

{foreach from=$settings.recommended item='test'}
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
				<p><em>{$test->message}</em></p>
	{/if}
	{if isset($test->opt)}
		{foreach from=$test->opt key='key' item='opt'}
				<p><img src="images/{$opt.res}.gif" alt="{$opt.res_text}" title="{$opt.res_text}" /> {$key}: {$opt.message}</p>
		{/foreach}
	{/if}
			</td>
			<td class="col2">
				<img class="{$test->res}" src="images/{$test->res}.gif" alt="{$test->res_text}" title="{$test->res_text}" />
	{if isset($test->error_fragment)}
				<a class="external" rel="external" href="{$cms_install_help_url}#{$test->error_fragment}">?</a>
	{/if}
			</td>
		</tr>
{/foreach}
	</tbody>
</table>



<table class="legend" border="0">
	<thead class="tbcaption">
		<tr>
			<td colspan="2">
				{lang_install a=install_legend}
			</td>
		</tr>
	</thead>
	<tbody>
		<tr class="tbhead">
			<th>
				{lang_install a=install_symbol}
			</th>
			<th>
				{lang_install a=install_definition}
			</th>
		</tr>
		<tr>
			<td class="col2">
				<img src="images/green.gif" alt="{if isset($success)}{$success}{/if}" title="{if isset($success)}{$success}{/if}" />
			</td>
			<td>
				{lang_install a=install_value_passed}
			</td>
		</tr>
		<tr>
			<td class="col2">
				<img src="images/red.gif" alt="{if isset($failure)}{$failure}{/if}" title="{if isset($failure)}{$failure}{/if}" />
			</td>
			<td>
				{lang_install a=install_value_failed}
			</td>
		</tr>
		<tr>
			<td class="col2">
				<img src="images/yellow.gif" alt="{if isset($caution)}{$caution}{/if}" title="{if isset($caution)}{$caution}{/if}"{/if}/>
			</td>
			<td>
				{lang_install a=install_value_recommended}
			</td>
		</tr>
		<tr>
			<td class="col2">
				<img src="images/info-external.gif" alt="{lang_install a=install_error_fragment}" title="{lang_install a=install_error_fragment}" />
			</td>
			<td>
				{lang_install a=install_error_fragment}
			</td>
		</tr>
	</tbody>
</table>

{literal}
<script type='text/javascript'>
<!--
function toggle(obj) {
	var el = document.getElementById(obj);
	el.style.display = (el.style.display != 'none' ? 'none' : '' );
}
//-->
</script>
{/literal}
<form action="{$smarty.server.PHP_SELF}?sessiontest=1" method="post" name="page2form" id="page2form">
	<div class="continue">
		<input type="reset" name="togglephpinfo" value="{lang_install a=phpinfo}" onclick="toggle('phpinfo');return false;" />
		<input type="hidden" name="page" value="3" />
		<input type="hidden" name="default_cms_lang" value="{$default_cms_lang}" />
	</div>
	<div class="callout">
		<fieldset>
			<div id="phpinfo" style="display:none;">{$phpinfo}</div>
		</fieldset>
	</div>
{if $continueon}
	{if $special_failed}
	<div class="warning">
		{lang_install a=install_test_failed}
	</div>
	<div class="continue">
		<input type="submit" name="recheck" value="{lang_install a=install_try_again}" />
	</div>
	{else}
	<div class="continue">
		<span><b>{lang_install a=install_test_passed}</b></span>
	</div>
	{/if}
	<div class="continue">
		<input type="submit" value="{lang_install a=install_continue}" />
	</div>
{else}
	<div class="failure">
		{lang_install a=install_failed_again}
	</div>
	<div class="continue">
		<input type="submit" name="recheck" value="{lang_install a=install_try_again}" />
	</div>
{/if}
</form>
