<h3>{translate}Environment Check{/translate}</h3>

<p>
  {translate}Here is a paragraph about the environment check.{/translate}
</p>

<div class="callout">
  <fieldset>
    <legend>{translate}Required Settings{/translate}</legend>
    <p>
      <span class="go_left">{translate}PHP version 5.0.4+{/translate}</span>
      <span class="go_right">{$php_version}</span>
    </p>
    <p>
      <span class="go_left">{translate}At least 1 database module{/translate}</span>
      <span class="go_right">({$which_database}) {$has_database}</span>
    </p>
    <p>
      <span class="go_left">{translate}XML module{/translate}</span>
      <span class="go_right">{$has_xml}</span>
    </p>
    <p>
      <span class="go_left">{translate}SimpleXML module{/translate}</span>
      <span class="go_right">{$has_simplexml}</span>
    </p>
    <p>
      <span class="go_left">{translate}Write permission on{/translate} {$templates_path}</span>
      <span class="go_right">{$canwrite_templates}</span>
    </p>
    <p>
      <span class="go_left">{translate}Write permission on{/translate} {$cache_path}</span>
      <span class="go_right">{$canwrite_cache}</span>
    </p>
  </fieldset>
</div>

<div class="callout">
  <fieldset>
    <legend>{translate}Recommended Settings{/translate}</legend>
    <p>
      <span class="go_left">{translate}File Uploads{/translate} {translate}(recommended: On){/translate}</span></span>
      <span class="go_right">{$file_uploads}</span>
    </p>
    <p>
      <span class="go_left">{translate}Safe Mode{/translate} {translate}(recommended: Off){/translate}</span>
      <span class="go_right">{$safe_mode}</span>
    </p>
    <p>
      <span class="go_left">{translate}Output Buffering{/translate} {translate}(recommended: Off){/translate}</span>
      <span class="go_right">{$output_buffering}</span>
    </p>
    <p>
      <span class="go_left">{translate}Register Globals{/translate} {translate}(recommended: Off){/translate}</span>
      <span class="go_right">{$register_globals}</span>
    </p>
    <p>
      <span class="go_left">{translate}Magic Quotes Runtime{/translate} {translate}(recommended: Off){/translate}</span>
      <span class="go_right">{$magic_quotes_runtime}</span>
    </p>
    <p>
      <span class="go_left">{translate}Write permission on{/translate} {$uploads_path} {translate}(recommended: On){/translate}</span>
      <span class="go_right">{$canwrite_uploads}</span>
    </p>
    <p>
      <span class="go_left">{translate}Write permission on{/translate} {$modules_path} {translate}(recommended: On){/translate}</span>
      <span class="go_right">{$canwrite_modules}</span>
    </p>
  </fieldset>
</div>

<p>
  {if $failure or $failure2}
    <a href="index.php?action=check">{translate}Check Again{/translate}</a>
  {/if}
  <a href="index.php?action=intro">{translate}Back{/translate}</a>
  <a href="index.php?action=database">{translate}Next{/translate}</a>
</p>
