{* set the default help text, the javascript in the admin theme will look for this div *}
<div id="default_helptext" class="hidden">
{mod_lang string="help-default_text"}
</div>


{admin_tabs}
  {tab_headers active=$selected_tab}
    {permission check='Manage Users'}
      {tab_header name='users'}{tr}Users{/tr}{/tab_header}
    {/permission}
    {permission check='Manage Groups'}
      {tab_header name='groups'}{tr}Groups{/tr}{/tab_header}
    {/permission}
    {permission check='Manage Site Preferences'}
      {tab_header name='prefs'}{tr}Preferences{/tr}{/tab_header}
    {/permission}
  {/tab_headers}

  {permission check='Manage Users'}
    {tab_content name='users'}
      {mod_template template='users_tab.tpl'}
    {/tab_content}
  {/permission}

  {permission check='Manage Groups'}
    {tab_content name='groups'}
      {mod_template template='groups_tab.tpl'}
    {/tab_content}
  {/permission}

  {permission check='Manage Site Preferences'}
    {tab_content name='prefs'}
      {mod_template template='prefs_tab.tpl'}
    {/tab_content}
  {/permission}
{/admin_tabs}