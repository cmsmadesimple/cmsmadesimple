{validation_errors for=$template_object}

<div class="pagecontainer">
{$header_name}

  <form method="post" name="templateform" id="templateform" action="{$action}">
    
    {* Name *}
	{admin_input type='input' label='name' id='template_name' name='template[name]' value=$template_object->name}

    {* Content *}
		<div class="row">
			<label>*{lang string='content'}:</label>
			  {$content_box}
		</div>

		{* Encoding *}
		<div class="row">
			<label>{lang string='encoding'}:</label>
 		    {$encoding_dropdown}
		</div>

    {* Active *}
	{admin_input type='checkbox' label='active' id='template_active' name='template[active]' selected=$template_object->active}	
    
    {* Preview *}
    {if $showpreview eq true}
      <iframe name="previewframe" class="preview" id="previewframe" src="{$previewfname}"></iframe>
    {/if}
    
    <input type="submit" name="submitbutton" value="{lang string='submit'}" class="pagebutton" onmouseover="this.className='pagebuttonhover'" onmouseout="this.className='pagebutton'" />
    <input type="submit" name="cancel" value="{lang string='cancel'}" class="pagebutton" onmouseover="this.className='pagebuttonhover'" onmouseout="this.className='pagebutton'" />
    {if $can_apply eq true}
    <input type="submit" name="applybutton" value="{lang string='apply'}" class="pagebutton" onmouseover="this.className='pagebuttonhover'"   onmouseout="this.className='pagebutton'" />
    {/if}
    {if $can_preview eq true}
      <input type="submit" name="previewbutton" value="{lang string='preview'}" class="pagebutton" onmouseover="this.className=\'pagebuttonhover\'" onmouseout="this.className=\'pagebutton\'" />
    {/if}
    
    {html_hidden name='is_postback' value='true'}
    {html_hidden name='template[id]' value=$template_object->id}
    {html_hidden name='template_id' value=$template_object->id}

  </form>

</div>
