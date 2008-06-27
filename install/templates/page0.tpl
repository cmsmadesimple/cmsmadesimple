<h2>Thanks for choosing CMS Made Simple</h2>

<form action="index.php?sessiontest=1" method="post" name="page0form" id="page0form">
<table class="settings" border="0">
	<caption class="tbcaption">Choose the language you would prefer to use for the installer<br/><em><span style="font-size: 0.8em;">(This does not effect the default settings of CMS Made Simple)</em></span></caption>
	<tbody>
	<tr>
		<td align="center">
			<select name="default_cms_lang">
{foreach from=$languages item=lang}
				<option value="{$lang}">{$lang}</option>
{/foreach}
			</select>
		</td>
	</tr>
	</tbody>
</table>

<p align="center" class="continue">
	<input type="submit" name="submit" value="Submit" />
</p>

</form>
