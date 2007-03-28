{foreach from=$errors item=error}
<p class="error">{$error}</p>
{/foreach}
{if empty($errors)}
<h4>{$success_message}</h4>
{/if}
