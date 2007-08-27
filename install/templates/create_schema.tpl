{if $installed}
  <p>{tr}The database was properly installed.{/tr}</p>
  {if $user_created}
    <p>{tr}The user account was created successfully.{/tr}</p>
	{if $config_created}
		<p>{tr}The config file was created.{/tr}</p>
	{else}
		<p>{tr}The config file could not be created{/tr}</p>
	{/if}
  {else}
    <p>{tr}The user account could not be created.{/tr}</p>
  {/if}
{else}
  <p>{tr}The database could not be created.{/tr}</p>
{/if}
