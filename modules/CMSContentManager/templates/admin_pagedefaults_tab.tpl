{form_start action=admin_pagedefaults_tab pagedefaults=1}
<div class="information">{$mod->Lang('info_pagedflt')}</div>
<div class="pageoverflow">
  <p class="pagetext">{$mod->Lang('prompt_pagedflt_contenttype')}:&nbsp;{cms_help key2='help_pagedflt_contenttype'}</p>
  <p class="pageinput">
    <select name="{$actionid}contenttype">
      {html_options options=$all_contenttypes selected=$page_prefs.contenttype}
    </select>
  </p>
</div>
<div class="pageoverflow">
  <p class="pagetext">{$mod->Lang('prompt_pagedflt_disallowed_types')}:&nbsp;{cms_help key2='help_pagedflt_disallowed_types'}</p>
  <p class="pageinput">
    <select name="{$actionid}disallowed_types[]" multiple="multiple" size="5">
      {html_options options=$all_contenttypes selected=$page_prefs.disallowed_types}
    </select>
  </p>
</div>
<div class="pageoverflow">
  <p class="pagetext">{$mod->Lang('prompt_pagedflt_design_id')}:&nbsp;{cms_help key2='help_pagedflt_design_id'}</p>
  <p class="pageinput">
    <select name="{$actionid}design_id">
      {html_options options=$design_list selected=$page_prefs.design_id}
    </select>
  </p>
</div>
<div class="pageoverflow">
  <p class="pagetext">{$mod->Lang('prompt_pagedflt_template_id')}:&nbsp;{cms_help key2='help_pagedflt_template_id'}</p>
  <p class="pageinput">
    <select name="{$actionid}template_id">
      {html_options options=$template_list selected=$page_prefs.template_id}
    </select>
  </p>
</div>
<div class="pageoverflow">
  <p class="pagetext">{$mod->Lang('prompt_pagedflt_secure')}:&nbsp;{cms_help key2='help_pagedflt_secure'}</p>
  <p class="pageinput">
    <select name="{$actionid}secure">
      {cms_yesno selected=$page_prefs.secure}
    </select>
  </p>
</div>
<div class="pageoverflow">
  <p class="pagetext">{$mod->Lang('prompt_pagedflt_cachable')}:&nbsp;{cms_help key2='help_pagedflt_cachable'}</p>
  <p class="pageinput">
    <select name="{$actionid}cachable">
      {cms_yesno selected=$page_prefs.cachable}
    </select>
  </p>
</div>
<div class="pageoverflow">
  <p class="pagetext">{$mod->Lang('prompt_pagedflt_active')}:&nbsp;{cms_help key2='help_pagedflt_active'}</p>
  <p class="pageinput">
    <select name="{$actionid}active">
      {cms_yesno selected=$page_prefs.active}
    </select>
  </p>
</div>
<div class="pageoverflow">
  <p class="pagetext">{$mod->Lang('prompt_pagedflt_showinmenu')}:&nbsp;{cms_help key2='help_pagedflt_showinmenu'}</p>
  <p class="pageinput">
    <select name="{$actionid}showinmenu">
      {cms_yesno selected=$page_prefs.showinmenu}
    </select>
  </p>
</div>
<div class="pageoverflow">
  <p class="pagetext">{$mod->Lang('prompt_pagedflt_searchable')}:&nbsp;{cms_help key2='help_pagedflt_searchable'}</p>
  <p class="pageinput">
    <select name="{$actionid}searchable">
      {cms_yesno selected=$page_prefs.searchable}
    </select>
  </p>
</div>
<div class="pageoverflow">
  <p class="pagetext">{$mod->Lang('prompt_pagedflt_addteditors')}:&nbsp;{cms_help key2='help_pagedflt_addteditors'}</p>
  <p class="pageinput">
    <select name="{$actionid}addteditors[]" multiple="multiple" size=6>
      {html_options options=$addteditor_list selected=$page_prefs.addteditors}
    </select>
  </p>
</div>
<div class="pageoverflow">
  <p class="pagetext">{$mod->Lang('prompt_pagedflt_extra1')}:&nbsp;{cms_help key2='help_pagedflt_extra1'}</p>
  <p class="pageinput">
    <input type="text" name="{$actionid}extra1" value="{$page_prefs.extra1}" size="80" maxlength="255"/>
  </p>
</div>
<div class="pageoverflow">
  <p class="pagetext">{$mod->Lang('prompt_pagedflt_extra2')}:&nbsp;{cms_help key2='help_pagedflt_extra2'}</p>
  <p class="pageinput">
    <input type="text" name="{$actionid}extra2" value="{$page_prefs.extra2}" size="80" maxlength="255"/>
  </p>
</div>
<div class="pageoverflow">
  <p class="pagetext">{$mod->Lang('prompt_pagedflt_extra3')}:&nbsp;{cms_help key2='help_pagedflt_extra3'}</p>
  <p class="pageinput">
    <input type="text" name="{$actionid}extra3" value="{$page_prefs.extra3}" size="80" maxlength="255"/>
  </p>
</div>
<div class="pageoverflow">
  <p class="pagetext"></p>
  <p class="pageinput">
    <input type="submit" name="{$actionid}submit" value="{$mod->Lang('submit')}"/>
  </p>
</div>
{form_end}