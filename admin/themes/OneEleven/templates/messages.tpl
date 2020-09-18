{strip}

{if isset($errors) && $errors[0] != ''}
<aside class="message pageerrorcontainer" role="alert">
{foreach from=$errors item='error'}
	{if $error}
	<p>{$error|cms_escape}</p>
	{/if}
{/foreach}
</aside>	
{/if}
{if isset($messages) && $messages[0] != ''}
<aside class="message pagemcontainer" role="status">
{foreach from=$messages item='message'}
	{if $message}
	<p>{$message|cms_escape}</p>
	{/if}
{/foreach}
</aside>
{/if}


{/strip}
