<h3>{translate}Account Setup{/translate}</h3>

<p>
  {translate}This is a paragraph about the wonders of account setup.{/translate}
</p>

<form action="index.php" method="post" id="accountform">
<div class="callout">
  <fieldset>
    <legend>{translate}Admin Account{/translate}</legend>
    <p>
      <span class="go_left">
        {translate}Username{/translate}:
      </span>
      <span class="go_right">
        <input type="text" id="admin_account_username" name="admin_account[username]" value="{$smarty.session.admin_account.username}" />
      </span>
    </p>
    <p>
      <span class="go_left">
        {translate}Password{/translate}:
      </span>
      <span class="go_right">
        <input type="password" id="admin_account_password" name="admin_account[password]" value="{$smarty.session.admin_account.password}" />
      </span>
    </p>
    <p>
      <span class="go_left">
        {translate}Password Again{/translate}:
      </span>
      <span class="go_right">
        <input type="password" id="admin_account_password_again" name="admin_account[password_again]" value="{$smarty.session.admin_account.password_again}" />
      </span>
    </p>
    <!--
    <p>
      <span class="go_left">
        {translate}Click here to create this account{/translate}:
      </span>
      <span class="go_right">
        <input type="submit" name="test_connection" value="{translate}Create Account{/translate}" onclick="xajax_create_account(xajax.getFormValues('accountform')); return false;" />
      </span>
    </p>
    -->
    <input type="hidden" name="action" value="account" />
  </fieldset>
</div>

<br style="clear: both;" />

<p>
  <input type="submit" name="back" value="{translate}Back{/translate}" />
  <input type="submit" name="next" value="{translate}Next{/translate}" />
</p>

</form>