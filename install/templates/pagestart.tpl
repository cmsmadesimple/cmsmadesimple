<h2>Thanks for choosing CMS Made Simple&trade;</h2>

<form action="{strip_tags($smarty.server.PHP_SELF)}?sessiontest=1" method="post" name="pagestartform" id="pagestartform">
<table class="settings" border="0">
	<thead class="tbcaption">
	    <tr>
	    	<td style="font-size: 0.8em; text-align:center;">Choose the language you would prefer to use<br /><em>This setting affects the install process and sets your initial admin user preference</em></td>
	    </tr>
    </thead>
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

<div class="continue">
	<input type="submit" name="submit" value="Submit" />
</div>

</form>