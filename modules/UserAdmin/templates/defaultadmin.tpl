{* set the default help text, the javascript in the admin theme will look for this div *}
<div id="default_helptext" class="hidden">
{mod_lang string="help-default_text"}
</div>

{has_permission perm="Modify Templates"}
	<div class="pageoverflow" style="text-align: right; width: 80%;"><a href="listmodtemplates.php" title="{tr}modify_templates{/tr}">{tr}modify_templates{/tr}</a></div><br/>
{/has_permission}
{tabs}
  {has_permission check='Manage Users'}
    {tab_content name='users'}
      {tab_header name='users'}{tr}users{/tr}{/tab_header}
      {mod_template template='users_tab.tpl'}
    {/tab_content}
  {/has_permission}

  {has_permission check='Manage Groups'}
    {tab_content name='groups'}
      {tab_header name='groups'}{tr}groups{/tr}{/tab_header}
      {mod_template template='groups_tab.tpl'}
    {/tab_content}
  {/has_permission}

  {has_permission check='Manage Site Preferences'}
    {tab_content name='prefs'}
      {tab_header name='prefs'}{tr}preferences{/tr}{/tab_header}
      {mod_template template='prefs_tab.tpl'}
    {/tab_content}
  {/has_permission}
{/tabs}