<h3>{translate}Database Setup{/translate}</h3>

<p>
  {translate}This is a paragraph about the wonders of database setup.{/translate}
</p>

<form action="index.php" method="post" id="connectionform">
<div class="callout">
  <fieldset>
    <legend>{translate}Connection Details{/translate}</legend>
    <p>
      <span class="go_left">
        {translate}Database Driver{/translate}:
      </span>
      <span class="go_right">
        <select id="connection_driver" name="connection[driver]">
          {html_options options=$databases selected=$selected_database}
        </select>
      </span>
    </p>
    <p>
      <span class="go_left">
        {translate}Hostname{/translate}:
      </span>
      <span class="go_right">
        <input type="text" id="connection_hostname" name="connection[hostname]" value="{$smarty.session.connection.hostname}" />
      </span>
    </p>
    <p>
      <span class="go_left">
        {translate}Username{/translate}:
      </span>
      <span class="go_right">
        <input type="text" id="connection_username" name="connection[username]" value="{$smarty.session.connection.username}" />
      </span>
    </p>
    <p>
      <span class="go_left">
        {translate}Password{/translate}:
      </span>
      <span class="go_right">
        <input type="password" id="connection_password" name="connection[password]" value="{$smarty.session.connection.password}" />
      </span>
    </p>
    <p>
      <span class="go_left">
        {translate}Database Name{/translate}:
      </span>
      <span class="go_right">
        <input type="text" id="connection_dbname" name="connection[dbname]" value="{$smarty.session.connection.dbname}" />
      </span>
    </p>
    <p>
      <span class="go_left">
        {translate}Drop Existing Tables{/translate}:
      </span>
      <span class="go_right">
        <input type="hidden" name="connection[drop_tables]" value="0" />
        <input type="checkbox" id="connection_drop_tables" name="connection[drop_tables]" value="1" />
      </span>
    </p>
    <p>
      <span class="go_left">
        {translate}Table Prefix{/translate}:
      </span>
      <span class="go_right">
        <input type="text" id="connection_table_prefix" name="connection[table_prefix]" value="{$smarty.session.connection.table_prefix}" />
      </span>
    </p>
    <p>
      <span class="go_left">
        {translate}Click here to test your connection settings{/translate}:
      </span>
      <span class="go_right">
        <input type="submit" name="test_connection" value="{translate}Test{/translate}" onclick="{literal}$('#connection_options').hide(); xajax_test_connection(xajax.getFormValues('connectionform')); return false;{/literal}" />
      </span>
    </p>
    <input type="hidden" name="action" value="database" />
  </fieldset>
</div>

<br style="clear: both;" />

<div class="callout">
  <fieldset>
    <legend>{translate}Test Results{/translate}</legend>
    <div id="connection_options" style="display: none;">
    </div>
  </fieldset>
</div>

<br style="clear: both;" />

<p>
  <input type="submit" name="back" value="{translate}Back{/translate}" />
  <input type="submit" name="next" value="{translate}Next{/translate}" />
</p>

</form>