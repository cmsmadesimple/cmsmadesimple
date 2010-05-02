{if $page.id > -1}
    <label>Id:</label> <span id="page_id">{$page.id}</span><br />
{/if}
<label>Page Name:</label> {html_input name='page[name][en_US]' value=$page.name}<br />
<label>Menu Text:</label> {html_input name='page[menu_text]' value=$page.menu_text}<br />
<label>Page Type:</label> {html_options class='page_type_picker' name="page[type]" options=$page_types selected=$page.type}<br />
<label>Page Template:</label> {html_options name='page[template_id]' options=$template_items selected=$page.template_id}<br />
<label>Active:</label> <span><a href="#">True</a></span><br />
<label>Show In Menu:</label> <span><a href="#">True</a></span><br />
<label>Path to Page:</label> /{html_input html_id='url_text' name='page[url_text]' autocomplete="off" value=$page.url_text}&nbsp;&nbsp;<span id="url_text_ok" style="color: green;">Ok</span><br />
<label>Unique Alias:</label> {html_input html_id='alias' name='page[alias]' autocomplete="off" value=$page.alias}&nbsp;&nbsp;<span id="alias_ok" style="color: green;">Ok</span><br />

{literal}
<script type='text/javascript'>
<!--
$(function() {
    $('#alias').delayedObserver(function()
    {
        cms_ajax_check_alias({name:'alias', value:$('#alias').val()}, {name:'page_id', value:$('#page_id').html()});
    }, 0.5);
    $('#url_text').delayedObserver(function()
    {
        cms_ajax_check_url({name:'alias', value:$('#url_text').val()}, {name:'page_id', value:$('#page_id').html()});
    }, 0.5);
});
//-->
</script>
{/literal}