{* basic buttons visible on all pages *}
<button type="submit" name="submitbutton" class="positive">
	<span class="text">{lang string='submit'}</span>
</button>    

<button type="submit" name="cancel" class="negative">
	{$cancel_image}
	{lang string='cancel'}
</button>    

{* loop through to get custom buttons *}
{foreach from=$DisplayButtons item=OneButton}
<button type="submit" name="{$OneButton.name}" class="{$OneButton.class}">
	{$OneButton.image}
	{$OneButton.caption}
</button> 

{$OneButton.image}
{/foreach}

