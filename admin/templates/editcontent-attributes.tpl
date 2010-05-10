{if $page.default_content == 1}
    <label>{lang string='active'}:</label> {html_checkbox selected=$page.active disabled='true'}<br />
{else}
    <label>{lang string='active'}:</label> {html_checkbox name='page[active]' selected=$page.active}<br />
{/if}
<label>{lang string='showinmenu'}:</label> {html_checkbox name='page[show_in_menu]' selected=$page.show_in_menu}<br />
<label>{lang string='cachable'}:</label> {html_checkbox name='page[cachable]' selected=$page.cachable}<br />
<label>{lang string='secure_page'}:</label> {html_checkbox name='page[secure]' selected=$page.secure}<br />

<label>{lang string='pagedata_codeblock'}:</label><br />{html_textarea name="page[pagedata]" value=$page.pagedata}<br />

<label>{lang string='titleattribute'}:</label> {html_input name='page[titleattribute]' value=$page.titleattribute}<br />
<label>{lang string='accesskey'}:</label> {html_input name='page[accesskey]' value=$page.accesskey}<br />
<label>{lang string='tabindex'}:</label> {html_input name='page[tabindex]' value=$page.tabindex}<br />

<label>{lang string='disable_wysiwyg'}:</label> {html_checkbox name='page[disable_wysiwyg]' selected=$page.disable_wysiwyg}<br />

<label>{lang string='extra1'}:</label> {html_input name='page[extra1]' value=$page.extra1}<br />
<label>{lang string='extra2'}:</label> {html_input name='page[extra2]' value=$page.extra2}<br />
<label>{lang string='extra3'}:</label> {html_input name='page[extra3]' value=$page.extra3}<br />
