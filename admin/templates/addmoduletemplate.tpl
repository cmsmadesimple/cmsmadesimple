{validation_errors for=$template_object}
<div class="pagecontainer">
  <div class="pageoverflow">
    {$header_name}
  </div><!-- pageoverflow -->

<form method="post" name="tempalteform" id="tempalteform" action="{$action}">

	{admin_input type='input' label='name' id='css_name' name='template[name]' value=$template_object->name}
	{admin_input type='textarea' label='content' id='css_text' name='template[content]' value=$template_object->content}

	{if $template_object->id > 0}
		<input type="hidden" name="template_id" value="{$template_object->id}" />
	{/if}
	{if $template_object->module ne ''}
		<input type="hidden" name="module_name" value="{$template_object->module}" />
	{/if}
	{if $template_object->template_type ne ''}
		<input type="hidden" name="template_type" value="{$template_object->template_type}" />
	{/if}
	{include file='elements/buttons.tpl'}

</form>
</div>