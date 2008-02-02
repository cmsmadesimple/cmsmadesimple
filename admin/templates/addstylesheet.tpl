{validation_errors for=$stylesheet_object}
{$header_name}
<form method="post" name="cssform" id="cssform" action="{$action}">
	<div id="page_tabs">
		<ul>
			<li><a href="#content"><span>{tr}Content{/tr}</span></a></li>
			<li><a href="#advanced"><span>{tr}Advanced{/tr}</span></a></li>
		</ul>
	    <div id="content">
			{admin_input type='input' label='name' id='css_name' name='stylesheet[name]' value=$stylesheet_object->name}
			{admin_input type='textarea' label='content' id='css_text' name='stylesheet[value]' value=$stylesheet_object->value}
			{admin_input type='checkbox' label='active' id='css_active' name='stylesheet[active]' selected=$stylesheet_object->active}
		</div>
		<div id="advanced">
			<h3>{lang string='mediatype'}</h3>
			{foreach from=$media_types key='key' item='type'}
				<div class="row">
					{if isset($type.selected)}
						{html_checkbox id=$type.name name='media_types[]' selected=true checked_value=$key unchecked_value='-1'}
					{else}
						{html_checkbox id=$type.name name='media_types[]' checked_value=$key unchecked_value='-1'}
					{/if}
					{capture assign='lang_key'}mediatype_{$type.name}{/capture}
					<label for="{$type.name}" style="white-space:nowrap;margin-left:10px;">{lang string=$lang_key}</label>
				</div>
			{/foreach}
		</div>
	</div>
	<input type="hidden" name="addcss" value="true" />
	{if $stylesheet_object->id > 0}
		<input type="hidden" name="css_id" value="{$stylesheet_object->id}" />
	{/if}
	{include file='elements/buttons.tpl'}	
</form>

<script type="text/javascript">
<!--
	$('#page_tabs').tabs({$start_tab});
//-->
</script>