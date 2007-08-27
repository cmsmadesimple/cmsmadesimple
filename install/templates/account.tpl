<h3>{tr}Account Setup{/tr}</h3>

<p>
  {tr}This is a paragraph about the wonders of account setup.{/tr}
</p>

<form action="index.php" method="post" id="accountform">
<div class="callout">
  <fieldset>
    <legend>{tr}Admin Account{/tr}</legend>
    <p>
      <span class="go_left">
        {tr}Username{/tr}:
      </span>
      <span class="go_right">
        <input type="text" id="admin_account_username" name="admin_account[username]" value="{$smarty.session.admin_account.username}" />
      </span>
    </p>
    <p>
      <span class="go_left">
        {tr}Password{/tr}:
      </span>
      <span class="go_right">
        <input type="password" id="admin_account_password" name="admin_account[password]" value="{$smarty.session.admin_account.password}" />
      </span>
    </p>
    <p>
      <span class="go_left">
        {tr}Password Again{/tr}:
      </span>
      <span class="go_right">
        <input type="password" id="admin_account_password_again" name="admin_account[password_again]" value="{$smarty.session.admin_account.password_again}" />
      </span>
    </p>
    <!--
    <p>
      <span class="go_left">
        {tr}Click here to create this account{/tr}:
      </span>
      <span class="go_right">
        <input type="submit" name="test_connection" value="{tr}Create Account{/tr}" onclick="xajax_create_account(xajax.getFormValues('accountform')); return false;" />
      </span>
    </p>
    -->
    <input type="hidden" name="action" value="account" />
  </fieldset>
</div>

<br style="clear: both;" />

<p>
  <input type="submit" name="back" value="{tr}Back{/tr}" />
  <input type="submit" name="next" value="{tr}Next{/tr}" />
</p>

</form>