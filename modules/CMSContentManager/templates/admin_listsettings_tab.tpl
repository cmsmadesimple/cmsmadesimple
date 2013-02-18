{form_start action='admin_listsettings_tab'}
<div class="pageoverflow">
  <p class="pagetext">{$mod->Lang('prompt_list_namecolumn')}:</p>
  <p class="pageinput">
    <select name="{$actionid}list_namecolumn">
    {html_options options=$namecolumnopts selected=$list_namecolumn}
    </select>
  </p>
</div>
<div class="pageoverflow">
  <p class="pagetext">{$mod->Lang('prompt_list_visiblecolumns')}:</p>
  <p class="pageinput">
    <select name="{$actionid}list_visiblecolumns[]" multiple="multiple" size="5">
    {html_options options=$visible_column_opts selected=$list_visiblecolumns}
    </select>
  </p>
</div>
<div class="pageoverflow">
  <p class="pagetext"></p>
  <p class="pageinput">
    <input type="submit" name="{$actionid}submit" value="{$mod->Lang('submit')}" accesskey="s"/>
  </p>
</div>
{form_end}