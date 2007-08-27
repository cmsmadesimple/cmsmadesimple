<h3>{tr}Account Setup{/tr}</h3>

<p>
  {tr}This is a paragraph about the wonders of config setup.{/tr}
</p>

<form action="index.php" method="post" id="configform">
<div class="callout">
  <fieldset>
    <legend>{tr}Config Setup{/tr}</legend>
    <p>
      <span class="go_left">
        {tr}Root file path{/tr}:
      </span>
      <span class="go_right">
        <input type="text" id="config_rootpath" name="config[rootpath]" value="{$smarty.session.config.rootpath}" />
      </span>
    </p>
    <p>
      <span class="go_left">
        {tr}Root URL{/tr}:
      </span>
      <span class="go_right">
        <input type="text" id="config_rooturl" name="config[rooturl]" value="{$smarty.session.config.rooturl}" />
      </span>
    </p>
  </fieldset>
</div>

<br style="clear: both;" />

<p>
  <input type="submit" name="back" value="{tr}Back{/tr}" />
  <input type="submit" name="next" value="{tr}Next{/tr}" />
</p>
<input type="hidden" name="action" value="create_config" />
</form>