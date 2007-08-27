<h3>{tr}Database Setup{/tr}</h3>

<p>
  {tr}This is a paragraph about the wonders of database setup.{/tr}
</p>

<form action="index.php" method="post" id="connectionform">
<div class="callout">
  <fieldset>
    <legend>{tr}Connection Details{/tr}</legend>
    <p>
      <span class="go_left">
        {tr}Database Driver{/tr}:
      </span>
      <span class="go_right">
        <select id="connection_driver" name="connection[driver]">
          {html_options options=$databases selected=$selected_database}
        </select>
      </span>
    </p>
    <p>
      <span class="go_left">
        {tr}Hostname{/tr}:
      </span>
      <span class="go_right">
        <input type="text" id="connection_hostname" name="connection[hostname]" value="{$smarty.session.connection.hostname}" />
      </span>
    </p>
    <p>
      <span class="go_left">
        {tr}Username{/tr}:
      </span>
      <span class="go_right">
        <input type="text" id="connection_username" name="connection[username]" value="{$smarty.session.connection.username}" />
      </span>
    </p>
    <p>
      <span class="go_left">
        {tr}Password{/tr}:
      </span>
      <span class="go_right">
        <input type="password" id="connection_password" name="connection[password]" value="{$smarty.session.connection.password}" />
      </span>
    </p>
    <p>
      <span class="go_left">
        {tr}Database Name{/tr}:
      </span>
      <span class="go_right">
        <input type="text" id="connection_dbname" name="connection[dbname]" value="{$smarty.session.connection.dbname}" />
      </span>
    </p>
    <p>
      <span class="go_left">
        {tr}Drop Existing Tables{/tr}:
      </span>
      <span class="go_right">
        <input type="hidden" name="connection[drop_tables]" value="0" />
        <input type="checkbox" id="connection_drop_tables" name="connection[drop_tables]" value="1" />
      </span>
    </p>
    <p>
      <span class="go_left">
        {tr}Table Prefix{/tr}:
      </span>
      <span class="go_right">
        <input type="text" id="connection_table_prefix" name="connection[table_prefix]" value="{$smarty.session.connection.table_prefix}" />
      </span>
    </p>
    <p>
      <span class="go_left">
        {tr}Click here to test your connection settings{/tr}:
      </span>
      <span class="go_right">
        <input type="submit" name="test_connection" value="{tr}Test{/tr}" onclick="{literal}$('#connection_options').hide(); xajax_test_connection(xajax.getFormValues('connectionform')); return false;{/literal}" />
      </span>
    </p>
    <input type="hidden" name="action" value="database" />
  </fieldset>
</div>

<br style="clear: both;" />

<div class="callout">
  <fieldset>
    <legend>{tr}Test Results{/tr}</legend>
    <div id="connection_options" style="display: none;">
    </div>
  </fieldset>
</div>

<br style="clear: both;" />

<p>
  <input type="submit" name="back" value="{tr}Back{/tr}" />
  <input type="submit" name="next" value="{tr}Next{/tr}" />
</p>

</form>