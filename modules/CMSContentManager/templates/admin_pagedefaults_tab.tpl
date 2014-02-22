{form_start action=admin_pagedefaults_tab pagedefaults=1}
<div class="information">
	{$mod->Lang('info_pagedflt')}
</div>
<div class="pageoverflow">
	<p class="pagetext"></p>
	<p class="pageinput">
		<input type="submit" name="{$actionid}submit" value="{$mod->Lang('submit')}"/>
	</p>
</div>
<div class="pageoverflow">
	<p class="pagetext"><label for="contenttype">{$mod->Lang('prompt_pagedflt_contenttype')}</label>:&nbsp;{cms_help key2='help_pagedflt_contenttype' title=$mod->Lang('prompt_pagedflt_contenttype')}</p>
	<p class="pageinput">
		<select id="contenttype" name="{$actionid}contenttype">
			{html_options options=$all_contenttypes selected=$page_prefs.contenttype}
		</select></p>
</div>
<div class="pageoverflow">
	<p class="pagetext"><label for="design_id">{$mod->Lang('prompt_pagedflt_design_id')}:</label>&nbsp;{cms_help key2='help_pagedflt_design_id' title=$mod->Lang('prompt_pagedflt_design_id')}</p>
	<p class="pageinput">
		<select id="design_id" name="{$actionid}design_id">
			{html_options options=$design_list selected=$page_prefs.design_id}
		</select></p>
</div>
<div class="pageoverflow">
	<p class="pagetext"><label for="template_id">{$mod->Lang('prompt_pagedflt_template_id')}:</label>&nbsp;{cms_help key2='help_pagedflt_template_id' title=$mod->Lang('prompt_pagedflt_template_id')}</p>
	<p class="pageinput">
		<select id="template_id" name="{$actionid}template_id">
			{html_options options=$template_list selected=$page_prefs.template_id}
		</select></p>
</div>
<div class="pageoverflow">
	<p class="pagetext"><label for="metadata">{$mod->Lang('prompt_pagedflt_metadata')}:</label>&nbsp;{cms_help key2='help_pagedflt_metadata' title=$mod->Lang('prompt_pagedflt_metadata')}</p>
	<p class="pageinput">		<textarea id="metadata" name="{$actionid}metadata" rows="5" cols="80">{$page_prefs.metadata}</textarea></p>
</div>
<div class="pageoverflow">
	<p class="pagetext"><label for="content">{$mod->Lang('prompt_pagedflt_content')}:</label>&nbsp;{cms_help key2='help_pagedflt_content' title=$mod->Lang('prompt_pagedflt_content')}</p>
	<p class="pageinput">		<textarea id="content" name="{$actionid}content" rows="5" cols="80">{$page_prefs.content}</textarea></p>
</div>
<div class="pageoverflow">
	<p class="pagetext"><label for="secure">{$mod->Lang('prompt_pagedflt_secure')}:</label>&nbsp;{cms_help key2='help_pagedflt_secure' title=$mod->Lang('prompt_pagedflt_secure')}</p>
	<p class="pageinput">
		<select id="secure" name="{$actionid}secure">
			{cms_yesno selected=$page_prefs.secure}
		</select></p>
</div>
<div class="pageoverflow">
	<p class="pagetext"><label for="cachable">{$mod->Lang('prompt_pagedflt_cachable')}:</label>&nbsp;{cms_help key2='help_pagedflt_cachable' title=$mod->Lang('prompt_pagedflt_cachable')}</p>
	<p class="pageinput">
		<select id="cachable" name="{$actionid}cachable">
			{cms_yesno selected=$page_prefs.cachable}
		</select></p>
</div>
<div class="pageoverflow">
	<p class="pagetext"><label for="active">{$mod->Lang('prompt_pagedflt_active')}:</label>&nbsp;{cms_help key2='help_pagedflt_active' title=$mod->Lang('prompt_pagedflt_active')}</p>
	<p class="pageinput">
		<select id="active" name="{$actionid}active">
			{cms_yesno selected=$page_prefs.active}
		</select></p>
</div>
<div class="pageoverflow">
	<p class="pagetext"><label for="showinmenu">{$mod->Lang('prompt_pagedflt_showinmenu')}:</label>&nbsp;{cms_help key2='help_pagedflt_showinmenu' title=$mod->Lang('prompt_pagedflt_showinmenu')}</p>
	<p class="pageinput">
		<select id="showinmenu" name="{$actionid}showinmenu">
			{cms_yesno selected=$page_prefs.showinmenu}
		</select></p>
</div>
<div class="pageoverflow">
	<p class="pagetext"><label for="searchable">{$mod->Lang('prompt_pagedflt_searchable')}:</label>&nbsp;{cms_help key2='help_pagedflt_searchable' title=$mod->Lang('prompt_pagedflt_searchable')}</p>
	<p class="pageinput">
		<select id="searchable" name="{$actionid}searchable">
			{cms_yesno selected=$page_prefs.searchable}
		</select></p>
</div>
<div class="pageoverflow">
	<p class="pagetext"><label for="addteditors">{$mod->Lang('prompt_pagedflt_addteditors')}:</label>&nbsp;{cms_help key2='help_pagedflt_addteditors' title=$mod->Lang('prompt_pagedflt_addteditors')}</p>
	<p class="pageinput">
		<select id="addteditors" name="{$actionid}addteditors[]" multiple="multiple" size="6">
			{html_options options=$addteditor_list selected=$page_prefs.addteditors}
		</select></p>
</div>
<div class="pageoverflow">
	<p class="pagetext"><label for="extra1">{$mod->Lang('prompt_pagedflt_extra1')}:</label>&nbsp;{cms_help key2='help_pagedflt_extra1' title=$mod->Lang('prompt_pagedflt_extra1')}</p>
	<p class="pageinput">
		<input id="extra1" type="text" name="{$actionid}extra1" value="{$page_prefs.extra1}" size="80" maxlength="255"/>
	</p>
</div>
<div class="pageoverflow">
	<p class="pagetext"><label for="extra2">{$mod->Lang('prompt_pagedflt_extra2')}:</label>&nbsp;{cms_help key2='help_pagedflt_extra2' title=$mod->Lang('prompt_pagedflt_extra2')}</p>
	<p class="pageinput">
		<input id="extra2" type="text" name="{$actionid}extra2" value="{$page_prefs.extra2}" size="80" maxlength="255"/>
	</p>
</div>
<div class="pageoverflow">
	<p class="pagetext"><label for="extra3">{$mod->Lang('prompt_pagedflt_extra3')}:</label>&nbsp;{cms_help key2='help_pagedflt_extra3' title=$mod->Lang('prompt_pagedflt_extra3')}</p>
	<p class="pageinput">
		<input id="extra3" type="text" name="{$actionid}extra3" value="{$page_prefs.extra3}" size="80" maxlength="255"/>
	</p>
</div>
<div class="pageoverflow">
	<p class="pagetext"></p>
	<p class="pageinput">
		<input type="submit" name="{$actionid}submit" value="{$mod->Lang('submit')}"/>
	</p>
</div>
{form_end}