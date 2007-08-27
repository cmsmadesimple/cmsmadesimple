<h3>{tr}Environment Check{/tr}</h3>

<p>
  {tr}Here is a paragraph about the environment check.{/tr}
</p>

<div class="callout">
  <fieldset>
    <legend>{tr}Required Settings{/tr}</legend>
    <p>
      <span class="go_left">{tr}PHP version 5.0.4+{/tr}</span>
      <span class="go_right">{$php_version}</span>
    </p>
    <p>
      <span class="go_left">{tr}At least 1 database module{/tr}</span>
      <span class="go_right">({$which_database}) {$has_database}</span>
    </p>
    <p>
      <span class="go_left">{tr}XML module{/tr}</span>
      <span class="go_right">{$has_xml}</span>
    </p>
    <p>
      <span class="go_left">{tr}SimpleXML module{/tr}</span>
      <span class="go_right">{$has_simplexml}</span>
    </p>
    <p>
      <span class="go_left">{tr}Write permission on{/tr} {$templates_path}</span>
      <span class="go_right">{$canwrite_templates}</span>
    </p>
    <p>
      <span class="go_left">{tr}Write permission on{/tr} {$cache_path}</span>
      <span class="go_right">{$canwrite_cache}</span>
    </p>
  </fieldset>
</div>

<div class="callout">
  <fieldset>
    <legend>{tr}Recommended Settings{/tr}</legend>
    <p>
      <span class="go_left">{tr}File Uploads{/tr} {tr}(recommended: On){/tr}</span></span>
      <span class="go_right">{$file_uploads}</span>
    </p>
    <p>
      <span class="go_left">{tr}Safe Mode{/tr} {tr}(recommended: Off){/tr}</span>
      <span class="go_right">{$safe_mode}</span>
    </p>
    <p>
      <span class="go_left">{tr}Output Buffering{/tr} {tr}(recommended: Off){/tr}</span>
      <span class="go_right">{$output_buffering}</span>
    </p>
    <p>
      <span class="go_left">{tr}Register Globals{/tr} {tr}(recommended: Off){/tr}</span>
      <span class="go_right">{$register_globals}</span>
    </p>
    <p>
      <span class="go_left">{tr}Magic Quotes Runtime{/tr} {tr}(recommended: Off){/tr}</span>
      <span class="go_right">{$magic_quotes_runtime}</span>
    </p>
    <p>
      <span class="go_left">{tr}Write permission on{/tr} {$uploads_path} {tr}(recommended: On){/tr}</span>
      <span class="go_right">{$canwrite_uploads}</span>
    </p>
    <p>
      <span class="go_left">{tr}Write permission on{/tr} {$modules_path} {tr}(recommended: On){/tr}</span>
      <span class="go_right">{$canwrite_modules}</span>
    </p>
  </fieldset>
</div>

<p>
    {if $failure or $failure2}
      <form action="index.php" method="get" style="display: inline;">
        <input type="submit" name="check_again" value="{tr}Check Again{/tr}" />
        <input type="hidden" name="action" value="check" />
      </form>
    {/if}
    <form action="index.php" method="get" style="display: inline;">
      <input type="submit" name="back" value="{tr}Back{/tr}" />
      <input type="hidden" name="action" value="intro" />
    </form>
    <form action="index.php" method="get" style="display: inline;">
      <input type="submit" name="next" value="{tr}Next{/tr}" />
      <input type="hidden" name="action" value="database" />
    </form>
  </form>
</p>
