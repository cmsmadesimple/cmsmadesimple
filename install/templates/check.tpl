<h3>{translate}Environment Check{/translate}</h3>

<p>
  {translate}Here is a paragraph about the environment check.{/translate}
</p>

<div class="callout">
  <fieldset>
    <legend>{translate}Required Settings{/translate}</legend>
    <p>{translate}PHP version 5.0.4+{/translate}<span class="go_right">{$php_version}</span></p>
    <p>{translate}At least 1 database module{/translate}<span class="go_right">{$has_database} ({$which_database})</span></p>
    <p>{translate}XML module{/translate}<span class="go_right">{$has_xml}</span></p>
    <p>{translate}SimpleXML module{/translate}<span class="go_right">{$has_simplexml}</span></p>
    <p>{translate}Write permission on{/translate} {$templates_path}<span class="go_right">{$canwrite_templates}</span></p>
    <p>{translate}Write permission on{/translate} {$cache_path}<span class="go_right">{$canwrite_cache}</span></p>
  </fieldset>
</div>

<p>
  {if $failure}
    <a href="index.php?action=check">{translate}Check Again{/translate}</a>
  {/if}
  <a href="index.php?action=intro">{translate}Back{/translate}</a>
  <a href="index.php?action=database">{translate}Next{/translate}</a>
</p>
