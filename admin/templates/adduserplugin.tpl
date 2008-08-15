{validation_errors for=$udt_object}
<div class="pagecontainer">
  <div class="pageoverflow">
    {$header_name}
  </div><!-- pageoverflow -->
<form enctype="multipart/form-data" action="{$action}" method="post">
	{admin_input type='input' label='name' id='userplugin_name' name='udt[name]' value=$udt_object->name}
	{admin_input type='textarea' label='code' id='content' name='udt[code]' value=$udt_object->code}
	<input type="hidden" name="userplugin" value="true" />
	{if $udt_object->id > 0}
		<input type="hidden" name="userplugin_id" value="{$udt_object->id}" />
	{/if}		
	{include file='elements/buttons.tpl'}
</form>
</div>