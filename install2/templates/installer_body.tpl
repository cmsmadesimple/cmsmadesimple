		<div class="main">

		<h2>Thanks for choosing CMS Made Simple</h2>

		<form action="{$smarty.server.PHP_SELF}?sessiontest=1" method="post" name="pagestartform" id="pagestartform">
			<table class="settings" border="0">
				<thead class="tbcaption">
					<tr>
						<td style="font-size: 0.8em; text-align:center;">Choose the language you would prefer to use for this process<br/> <em>(This does not effect the default settings of CMS Made Simple)</em></td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td align="center">
							<select id="select_language" name="select_language" onchange="this.form.submit();">
{foreach from=$languages item=lang}
								<option value="{$lang}">{$lang}</option>
{/foreach}
							</select>
						</td>
					</tr>
				</tbody>
			</table>

			<div class="continue">
				<input type="hidden" name="page" value="1" />
				<input type="submit" name="submit" value="Submit" />
			</div>
		</form>

	</div>
