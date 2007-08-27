<h3>{translate}Account Setup{/translate}</h3>

<p>
  {translate}This is a paragraph about the wonders of config setup.{/translate}
</p>

<form action="index.php" method="post" id="configform">
<div class="callout">
  <fieldset>
    <legend>{translate}Config Setup{/translate}</legend>
    <p>
      <span class="go_left">
        {translate}Root file path{/translate}:
      </span>
      <span class="go_right">
        <input type="text" id="config_rootpath" name="config[rootpath]" value="{$smarty.session.config.rootpath}" />
      </span>
    </p>
    <p>
      <span class="go_left">
        {translate}Root URL{/translate}:
      </span>
      <span class="go_right">
        <input type="text" id="config_rooturl" name="config[rooturl]" value="{$smarty.session.config.rooturl}" />
      </span>
    </p>
  </fieldset>
</div>

<br style="clear: both;" />

<p>
  <input type="submit" name="back" value="{translate}Back{/translate}" />
  <input type="submit" name="next" value="{translate}Next{/translate}" />
</p>
<input type="hidden" name="action" value="create_config" />
</form>