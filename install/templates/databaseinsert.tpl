{if $databasetestresult.have_connection}
  <p>{translate}You've successfully connected to the database.{/translate}</p>
  {if $databasetestresult.have_existing_db}
    <p>{translate}The database name you've entered already exists.{/translate}</p>
  {else}
    {if $databasetestresult.have_create_ability}
      <p>{translate}You have permissions to create the database.{/translate}</p>
    {else}
      <p>{translate}You do not have permission to create this database.  Please have a system administrator create it before continuing.{/translate}</p>
    {/if}
  {/if}
{else}
  <p>{translate}We could not make a connection.  Please make sure your connection settings are correct.{/translate}</p>
{/if}

{if $databasetestresult.have_connection and ($databasetestresult.have_existing_db or $databasetestresult.have_create_ability)}
<p>
  <span class="go_left">
    {translate}Drop Existing Tables{/translate}:
  </span>
  <span class="go_right">
    <input type="checkbox" id="connection_drop_tables" name="connection[drop_tables]" value="1" />
  </span>
</p>
<p>
  <span class="go_left">
    {translate}Table Prefix{/translate}:
  </span>
  <span class="go_right">
    <input type="text" id="connection_table_prefix" name="connection[table_prefix]" value="cms_" />
  </span>
</p>
<p style="text-align: center;">
  <input type="submit" name="create_database" value="{translate}Create Database{/translate}" />
</p>
{/if}
