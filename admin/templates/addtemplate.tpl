{validation_errors for=$template_object}

<div class="pagecontainer">

  <div class="pageoverflow">
    {$header_name}
  </div>

  <form method="post" name="templateform" id="templateform" action="{$action}">
    
    {* Name *}
		<div class="pageoverflow">
			<p class="pagetext">*{lang string='name'}:</p>
			<p class="pageinput">
			 {html_input id='template_name' name='template[name]' value=$template_object->name}
			</p>
		</div>

    {* Content *}
		<div class="pageoverflow">
			<p class="pagetext">*{lang string='content'}:</p>
			<p class="pageinput">
			  {$content_box}
			</p>
		</div>

		{* Encoding *}
		<div class="pageoverflow">
			<p class="pagetext">{lang string='encoding'}:</p>
			<p class="pageinput">
			  {$encoding_dropdown}
			</p>
		</div>

    {* Active *}
		<div class="pageoverflow">
			<p class="pagetext">{lang string='active'}:</p>
			<p class="pageinput">
			  {html_checkbox id='template_active' name='template[active]' selected=$template_object->active}
			</p>
		</div>
    
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
