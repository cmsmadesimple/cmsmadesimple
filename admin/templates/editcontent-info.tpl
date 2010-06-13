{if $page.id > -1}
    <label>Id:</label> <span id="page_id">{$page.id}</span><br />
{/if}
<label>{lang string='title'}:</label> {html_input name='page[name][en_US]' value=$page.name.en_US}<br />
<label>{lang string='menutext'}:</label> {html_input name='page[menu_text][en_US]' value=$page.menu_text.en_US}<br />
<label>{lang string='page_type'}:</label> {html_options class='page_type_picker' name="page[type]" options=$page_types selected=$page.type}<br />
<label>{lang string='page_template'}:</label> {html_options name='page[template_id]' id='template_id' options=$template_items selected=$page.template_id}<br />
<label>{lang string='parent'}:</label> {$parent_dropdown}<br />
<label>{lang string='url'}:</label> <span id="parent_path">{$parent_path}</span>{html_input html_id='url_text' name='page[url_text]' autocomplete="off" value=$page.url_text}&nbsp;&nbsp;<span id="url_text_ok" style="color: green;">Ok</span><br />
<label>{lang string='unique_alias'}:</label> {html_input html_id='alias' name='page[alias]' autocomplete="off" value=$page.alias}&nbsp;&nbsp;<span id="alias_ok" style="color: green;">Ok</span><br />

{literal}
<script type='text/javascript'>
<!--
$(function() {
    $('#alias').delayedObserver(function()
    {
        cms_ajax_check_alias({name:'alias', value:$('#alias').val()}, {name:'page_id', value:$('#page_id').html()}, {name:'serialized_page', value:$('#serialized_page').val()});
    }, 0.5);
    $('#url_text').delayedObserver(function()
    {
        cms_ajax_check_url({name:'alias', value:$('#url_text').val()}, {name:'page_id', value:$('#page_id').html()}, {name:'parent_id', value:$('#parent_dropdown').val()}, {name:'serialized_page', value:$('#serialized_page').val()});
    }, 0.5);
    $('#parent_dropdown').change(function()
    {
        var args = [{name:'parent_id', value:$('#parent_dropdown').val()}, {name:'page_id', value:$('#page_id').html()}, {name:'serialized_page', value:$('#serialized_page').val()}];
        cms_ajax_call("change_parent", args, {
            success: function (data, textStatus) {
                cms_ajax_callback(data);
                cms_ajax_check_url({name:'alias', value:$('#url_text').val()}, {name:'page_id', value:$('#page_id').html()}, {name:'parent_id', value:$('#parent_dropdown').val()}, {name:'serialized_page', value:$('#serialized_page').val()});
            }
        });
    });
    $('#template_id').change(function()
    {
		cms_ajax_change_template($('#content_form').serializeForCmsAjax());
    });
});
//-->
</script>
{/literal}