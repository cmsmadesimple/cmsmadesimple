{if $page.default_content == 1}
    <label>Active:</label> {html_checkbox selected=$page.active disabled='true'}<br />
{else}
    <label>Active:</label> {html_checkbox name='page[active]' selected=$page.active}<br />
{/if}
<label>Show In Menu:</label> {html_checkbox name='page[show_in_menu]' selected=$page.show_in_menu}<br />
<label>Cachable:</label> {html_checkbox name='page[cachable]' selected=$page.cachable}<br />
<label>Use HTTPS for this page:</label> {html_checkbox name='page[secure]' selected=$page.secure}<br />

<label>Smarty data or logic that is specific to this page:</label><br />{html_textarea name="page[pagedata]" value=$page.pagedata}<br />

<label>Description (title attribute):</label> {html_input name='page[titleattribute]' value=$page.titleattribute}<br />
<label>Access Key:</label> {html_input name='page[accesskey]' value=$page.accesskey}<br />
<label>Tab Index:</label> {html_input name='page[tabindex]' value=$page.tabindex}<br />

<label>Disable WYSIWYG editor on this page (regardless of template or user settings):</label> {html_checkbox name='page[disable_wysiwyg]' selected=$page.disable_wysiwyg}<br />

<label>Extra Page Attribute 1:</label> {html_input name='page[extra1]' value=$page.extra1}<br />
<label>Extra Page Attribute 2:</label> {html_input name='page[extra2]' value=$page.extra2}<br />
<label>Extra Page Attribute 3:</label> {html_input name='page[extra3]' value=$page.extra3}<br />
