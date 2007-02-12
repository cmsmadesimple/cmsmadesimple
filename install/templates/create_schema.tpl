{if $installed}
  <p>{translate}The database was properly installed.{/translate}</p>
  {if $user_created}
    <p>{translate}The user account was created successfully.{/translate}</p>
  {else}
    <p>{translate}The user account could not be created.{/translate}</p>
  {/if}
{else}
  <p>{translate}The database could not be created.{/translate}</p>
{/if}
