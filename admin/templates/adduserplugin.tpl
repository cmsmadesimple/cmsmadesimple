{validation_errors for=$udt_object}
{$header_name}
<form enctype="multipart/form-data" action="{$action}" method="post">
	{admin_input type='input' label='name' id='userplugin_name' name='udt[name]' value=$udt_object->name}
	{admin_input type='textarea' label='code' id='content' name='udt[code]' value=$udt_object->code}
	<div class="submitrow">
		<input type="hidden" name="userplugin" value="true" />
		{if $udt_object->id > 0}
			<input type="hidden" name="userplugin_id" value="{$udt_object->id}" />
		{/if}		
		<input type="submit" name="submitbutton" value="{lang string='submit'}" class="pagebutton" onmouseover="this.className='pagebuttonhover'" onmouseout="this.className='pagebutton'" />
		<input type="submit" name="cancel" value="{lang string='cancel'}" class="pagebutton" onmouseover="this.className='pagebuttonhover'" onmouseout="this.className='pagebutton'" />
	    {if $can_apply eq true}
		    <input type="submit" name="applybutton" value="{lang string='apply'}" class="pagebutton" onmouseover="this.className='pagebuttonhover'"   onmouseout="this.className='pagebutton'" />
	    {/if}		
	</div>	
</form>