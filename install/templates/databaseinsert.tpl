{if $databasetestresult.have_connection}
  <p>{tr}You've successfully connected to the database server.{/tr}</p>
  {if $databasetestresult.have_existing_db}
    <p>{tr}The database name you've entered already exists.{/tr}</p>
  {else}
    {if $databasetestresult.have_create_ability}
      <p>{tr}You have permissions to create the database.{/tr}</p>
    {else}
      <p>{tr}You do not have permission to create this database.  Please have a system administrator create it, and then click "Test" above again to recheck your settings.{/tr}</p>
    {/if}
  {/if}
{else}
  <p>{tr}We could not make a connection.  Please make sure your connection settings are correct.{/tr}</p>
{/if}
