{* basic buttons visible on all pages *}
<div class="submitrow">
	<input type="submit" value="{lang string='submit'}" name="submitbutton" class="positive" />
	<input type="submit" value="{lang string='cancel'}" name="cancel" class="negative" />

	{* loop through to get custom buttons *}
	{foreach from=$DisplayButtons item=OneButton}
		<input type="submit" value="{$OneButton.caption}" name="{$OneButton.name}" class="{$OneButton.class}" {if isset($OneButton.class)} onclick="{$OneButton.onclick}" {/if} />
	{/foreach}

</div>