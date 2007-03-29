<p class="important">
	Please read the <a href="http://wiki.cmsmadesimple.org/index.php/User_Handbook/Installation/Troubleshooting">Installation Troubleshooting</a> page in the CMS Made Simple Documentation Wiki.
</p>
<h3>Checking permissions and PHP settings</h3>

{$test}
<table class="settings" border="0">
	<caption class="tbcaption">Required settings</caption>
	<thead class="tbhead">
		<tr>
			<th>Test</th>
			<th>Result</th>
		</tr>
	</thead>
	<tbody>
{foreach from=$settings.required item=test}
		<tr class="{cycle values='row1,row2'}">
			<td>{$test->title}{if isset($test->message)}<br /><br /><em>{$test->message}</em>{/if}</td>
			<td class="col2">{$test->resultimage}</td>
		</tr>
{/foreach}
	<tbody>
</table>

<table class="settings" border="0">
	<caption class="tbcaption">Recommended settings</caption>
	<thead class="tbhead">
		<tr>
			<th>Test</th>
			<th>Result</th>
		</tr>
	</thead>
	<tbody>
{foreach from=$settings.recommended item=test}
		<tr class="{cycle values='row1,row2'}">
			<td>
{if isset($test->value) && $test->value != ''}
				<span class="have">You have {$test->value}</span>
{/if}
				{$test->title}
{if isset($test->error)}
				<p class="error">{$test->error}</p>
{/if}
{if isset($test->message)}
				<p>
					<em>{$test->message}</em>
				<p>
{/if}
			</td>
			<td class="col2">{$test->resultimage}</td>
		</tr>
{/foreach}
	</tbody>
</table>

<table class="legend" border="0">
	<caption class="tbcaption">Legend</caption>
	<thead class="tbhead">
		<tr>
			<th>Symbol</th>
			<th>Definition</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>{$images.true}</td>
			<td>A required test passed</td>
		</tr>
		<tr>
			<td>{$images.false}</td>
			<td>A required test failed</td>
		</tr>
		<tr>
			<td>{$images.red}</td>
			<td>A setting is below a required minimum value</td>
		</tr>
		<tr>
			<td>{$images.yellow}</td>
			<td>A setting is above the required value, but below the recommended value<br /><br />or... A capability that <em>may</em> be required for some optional functionality is unavailable</td>
		</tr>
		<tr>
			<td>{$images.green}</td>
			<td>A setting meets or exceeds the recommended threshhold<br /><br />or... A capability that <em>may</em> be required for some optional functionality is available</td>
		</tr>
	</tbody>
</table>

<form method="post" action="index.php">
{if $continueon}
{if $special_failed}
	<p class="failure" align="center">
		One or more tests have failed. You can still install the system but some functions may not work correctly.<br />
		Please try to correct the situation and click "Try Again", or click the Continue button.
	</p>
{else}
	<p class="success" align="center">All tests passed (at least at a minimum level). Please click the Continue button.</p>
{/if}
	<p class="continue" align="center">
{if $special_failed}
		<input type="Submit" name="recheck" value="Try Again" />
{/if}
		<input type="submit" value="Continue" />
		<input type="hidden" name="page" value="2" />
	</p>
{else}
	<p class="failure" align="center">One or more tests have failed. Please correct the problem and click the button below to recheck.</p>
	<p class="continue" align="center"><input type="Submit" value="Try Again" /></p>
{/if}