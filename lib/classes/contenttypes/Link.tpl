{* URL *}
<div class="pageoverflow">
	<p class="pagetext">{lang string='url'}:</p>
	<p class="pageinput">
	  {html_input id='content_property_url' name='content[property][url]' value=$page_object->get_property_value('url')}
	</p>
</div>

{* Target *}
<div class="pageoverflow">
	<p class="pagetext">{lang string='target'}:</p>
	<p class="pageinput">
    <select name="content[property][target]" id="content_property_target" class="standard">
      {html_options options=$link_targets selected=$page_object->get_property_value('target')}
    </select>
	</p>
</div>
