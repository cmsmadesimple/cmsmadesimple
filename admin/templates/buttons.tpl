<button type="submit" name="submitbutton" class="positive">
	{$submit_image}
	{lang string='submit'}
</button>    

<button type="submit" name="cancel" class="negative">
	{$cancel_image}
	{lang string='cancel'}
</button>    

{* only show apply if set *}
{if $use_apply_button}
<button type="submit" name="previewbutton" class="positive">
	{$preview_image}
	{lang string='preview'}
</button>    
{/if}

{* only show preview if set *}
{if $use_preview_button}
<button type="submit" name="previewbutton" class="positive">
	{$preview_image}
	{lang string='preview'}
</button>    
{/if}
