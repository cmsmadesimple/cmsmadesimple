{strip}

{if $errors[0] != ''}
<aside class="message error" role="alert">
{foreach from=$errors item='error'}
	{if $error}
	<p>{$error}</p>
	{/if}
{/foreach}
</aside>	
{/if}
{if $messages[0] != ''}
<aside class="message success" role="status">
{foreach from=$messages item='message'}
	{if $message}
	<p>{$message}</p>
	{/if}
{/foreach}
</aside>
{/if}


{/strip}
