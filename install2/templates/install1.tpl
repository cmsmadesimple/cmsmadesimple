{foreach from=$errors item=error}
<div class="error">{$error}</div>
{/foreach}

<h3>{tr}Check your installation{/tr}</h3>

<form action="{$smarty.server.PHP_SELF}" method="post" name="page1form" id="page1form" enctype="multipart/form-data">

<table class="settings" border="0">
	<thead class="tbcaption">
		<tr>
			<td colspan="2">{tr}Checksum test{/tr}</td>
		</tr>
	</thead>
	<tbody>

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
{else}
	{if isset($try_test)}
		<tr class="row2">
			<td><strong>{tr}All checksums match!{/tr}</strong></td>
			<td class="col2"><img src="images/green.gif" alt="{tr}Success{/tr}" title="{tr}Success{/tr}" /></td>
		</tr>
	{/if}
{/if}

	</tbody>
</table>


<div class="msg-botton">
	{tr}You can validate the integrity of your CMS files by comparing against original CMS checksum. It can assist in finding problems with uploads.{/tr}<br /><br />
	<input type="file" name="cksumdat" id="cksumdat" maxlength="255" /><br />
	<input type="submit" name="recheck" value="{tr}Test{/tr}" />
</div>
<div class="continue">
	<input type="hidden" name="page" value="2" />
	<input type="submit" value="{tr}Continue{/tr}" />
</div>

</form>
