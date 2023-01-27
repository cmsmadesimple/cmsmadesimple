{form_start action='admin_listsettings_tab'}
<div class="pageoverflow">
	<p class="pagetext"></p>
	<p class="pageinput">
		<input type="submit" name="{$actionid}submit" value="{$mod->Lang('submit')}" accesskey="s"/>
	</p>
</div>
<div class="pageoverflow">
	<p class="pagetext"><label for="namecolumn">{$mod->Lang('prompt_list_namecolumn')}:</label>&nbsp;{cms_help key2='help_listsettings_namecolumn' title=$mod->Lang('prompt_list_namecolumn')}</p>
	<p class="pageinput">
		<select id="namecolumn" name="{$actionid}list_namecolumn">
			{html_options options=$namecolumnopts selected=$list_namecolumn}
		</select></p>
</div>
<div class="pageoverflow">
	<p class="pagetext"><label for="visiblecolumns">{$mod->Lang('prompt_list_visiblecolumns')}:</label>&nbsp;{cms_help key2='help_listsettings_visiblecolumns' title=$mod->Lang('prompt_list_visiblecolumns')}</p>
	<p class="pageinput">
		<select id="visiblecolumns" name="{$actionid}list_visiblecolumns[]" multiple="multiple" size="5">
			{html_options options=$visible_column_opts selected=$list_visiblecolumns}
		</select></p>
</div>
{form_end}