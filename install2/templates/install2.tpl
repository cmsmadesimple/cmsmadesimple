<p class="important">
	{tr}Please read the <a href="http://wiki.cmsmadesimple.org/index.php/User_Handbook/Installation/Troubleshooting">Installation Troubleshooting</a> page in the CMS Made Simple Documentation Wiki.{/tr}
</p>

<h3>{tr}Checking permissions and PHP settings{/tr}</h3>



<table class="settings" border="0">
	<thead class="tbcaption">
		<tr>
			<td colspan="2">
				{tr}System Information{/tr}
			</td>
		</tr>
	</thead>
	<tbody>
{foreach from=$settings.info key='key' item='test'}
		<tr class="{cycle values='row1,row2'}">
			<td>
				{tr}{$key}{/tr}
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
				{tr}Required settings{/tr}
			</td>
		</tr>
	</thead>
	<tbody>
		<tr class="tbhead">
			<th>
				{tr}Test{/tr}
			</th>
			<th>
				{tr}Result{/tr}
			</th>
		</tr>

{foreach from=$settings.required item='test'}
		<tr class="{cycle values='row1,row2'}">
			<td>
	{if isset($test->value) && $test->value != ''}
				<span class="have">{tr}You have{/tr} {$test->value}</span>
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
				<img src="images/{$test->res}.gif" alt="{$test->res_text}" title="{$test->res_text}" />
			</td>
		</tr>
{/foreach}
	</tbody>
</table>



<table class="settings" border="0">
	<thead class="tbcaption">
		<tr>
			<td colspan="2">
				{tr}Recommended settings{/tr}
			</td>
		</tr>
	</thead>
<tbody>

<tr class="tbhead">
			<th>
				{tr}Test{/tr}
			</th>
			<th>
				{tr}Result{/tr}
			</th>
		</tr>

{foreach from=$settings.recommended item='test'}
		<tr class="{cycle values='row1,row2'}">
			<td>
	{if isset($test->value) && $test->value != ''}
				<span class="have">{tr}You have{/tr} {$test->value}</span>
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
				<img src="images/{$test->res}.gif" alt="{$test->res_text}" title="{$test->res_text}" />
			</td>
		</tr>
{/foreach}
	</tbody>
</table>



<table class="legend" border="0">
	<thead class="tbcaption">
		<tr>
			<td colspan="2">
				{tr}Legend{/tr}
			</td>
		</tr>
	</thead>
	<tbody>
		<tr class="tbhead">
			<th>
				{tr}Symbol{/tr}
			</th>
			<th>
				{tr}Definition{/tr}
			</th>
		</tr>
		<tr>
			<td class="col2">
				<img src="images/true.gif" alt="{tr}Success{/tr}" title="{tr}Success{/tr}" />
			</td>
			<td>
				{tr}A required test passed{/tr}
			</td>
		</tr>
		<tr>
			<td class="col2">
				<img src="images/false.gif" alt="{tr}Failure{/tr}" title="{tr}Failure{/tr}" />
			</td>
			<td>
				{tr}A required test failed{/tr}
			</td>
		</tr>
		<tr>
			<td class="col2">
				<img src="images/red.gif" alt="{tr}Failure{/tr}" title="{tr}Failure{/tr}" />
			</td>
			<td>
				{tr}A setting is below a required minimum value{/tr}
			</td>
		</tr>
		<tr>
			<td class="col2">
				<img src="images/yellow.gif" alt="{tr}Caution{/tr}" title="{tr}Caution{/tr}" />
			</td>
			<td>
				{tr}A setting is above the required value, but below the recommended value or... a capability that <em>may</em> be required for some optional functionality is unavailable{/tr}
			</td>
		</tr>
		<tr>
			<td class="col2">
				<img src="images/green.gif" alt="{tr}Success{/tr}" title="{tr}Success{/tr}" />
			</td>
			<td>
				{tr}A setting meets or exceeds the recommended threshhold or... a capability that <em>may</em> be required for some optional functionality is available{/tr}
			</td>
		</tr>
	</tbody>
</table>


<form action="{$smarty.server.PHP_SELF}?sessiontest=1" method="post" name="page2form" id="page2form">
	<div class="continue">
		<input type="reset" name="phpinfo" value="{tr}Toggle PHPInfo display{/tr}" onclick="$('#phpinfo').toggle();" />
		<input type="hidden" name="page" value="3" />
	</div>
	<div class="callout">
		<fieldset>
			<div id="phpinfo" style="display: none;">{$phpinfo}</div>
		</fieldset>
	</div>

{if $continueon}
	{if $special_failed}
		<div class="failure">
			{tr}One or more tests have failed. You can still install the system but some functions may not work correctly.{/tr}
			<br />
			{tr}Please try to correct the situation and click "Try Again", or click the Continue button.{/tr}
		</div>
		<div class="continue">
			<input type="submit" name="check" value="{tr}Try Again{/tr}" />
		</div>
	{else}
		<div class="continue">
			<span><b>{tr}All tests passed (at least at a minimum level). Please click the Continue button.{/tr}</b></span>
		</div>
	{/if}
		<div class="continue">
			<input type="submit" value="{tr}Continue{/tr}" />
		</div>
{else}
	<div class="failure">
		{tr}One or more tests have failed. Please correct the problem and click the button below to recheck.{/tr}
	</div>
	<div class="continue">
		<input type="submit" name="check" value="{tr}Try Again{/tr}" />
	</div>
{/if}
</form>
