{if $databasetestresult.have_connection}
  <p>{translate}You've successfully connected to the database server.{/translate}</p>
  {if $databasetestresult.have_existing_db}
    <p>{translate}The database name you've entered already exists.{/translate}</p>
  {else}
    {if $databasetestresult.have_create_ability}
      <p>{translate}You have permissions to create the database.{/translate}</p>
    {else}
      <p>{translate}You do not have permission to create this database.  Please have a system administrator create it, and then click "Test" above again to recheck your settings.{/translate}</p>
    {/if}
  {/if}
{else}
  <p>{translate}We could not make a connection.  Please make sure your connection settings are correct.{/translate}</p>
{/if}
