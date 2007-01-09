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
        <input type="text" id="connection_hostname" name="connection[hostname]" />
      </span>
    </p>
    <p>
      <span class="go_left">
        {translate}Username{/translate}:
      </span>
      <span class="go_right">
        <input type="text" id="connection_username" name="connection[username]" />
      </span>
    </p>
    <p>
      <span class="go_left">
        {translate}Password{/translate}:
      </span>
      <span class="go_right">
        <input type="password" id="connection_password" name="connection[password]" />
      </span>
    </p>
    <p>
      <span class="go_left">
        {translate}Database Name{/translate}:
      </span>
      <span class="go_right">
        <input type="text" id="connection_dbname" name="connection[dbname]" />
      </span>
    </p>
    <p>
      <span class="go_left">
        {translate}Click here to test your connection settings{/translate}:
      </span>
      <span class="go_right">
        <input type="submit" value="{translate}Test{/translate}" onclick="{literal}if (Element.visible('connection_options')) { Element.hide('connection_options'); } xajax_test_connection(xajax.getFormValues('connectionform')); return false;{/literal}" />
      </span>
    </p>
    <input type="hidden" name="action" value="database" />
  </fieldset>
</div>
</form>

<br style="clear: both;" />

<div class="callout">
  <fieldset>
    <legend>{translate}Connection Options{/translate}</legend>
    <div id="connection_options" style="display: none;">
    </div>
  </fieldset>
</div>

<br style="clear: both;" />

<p>
  <a href="index.php?action=check">{translate}Back{/translate}</a>
  <a href="index.php?action=account">{translate}Next{/translate}</a>
</p>
