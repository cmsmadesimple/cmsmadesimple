<h3>{$mod->Lang('prompt_copy')}</h3>

{$startform}
<div class="pageoverflow">
  <p class="pagetext">{$mod->Lang('itemstocopy')}:</p>
  <p class="pageinput">
    <ul>
    {foreach from=$selall item='one'}
      <li>{$one}</li>
    {/foreach}
    </ul>
  </p>
</div>
<div class="pageoverflow">
  <p class="pagetext">{$mod->Lang('copy_destdir')}:</p>
  <p class="pageinput">
    <select name="{$actionid}destdir">
    {html_options options=$dirlist selected=$cwd}
    </select>
  </p>
</div>
{if count($selall) == 1}
<div class="pageoverflow">
  <p class="pagetext">{$mod->Lang('copy_destname')}:</p>
  <p class="pageinput">
    <input type="text" name="{$actionid}destname" size="50" maxlength="255"/>
  </p>
</div>
{/if}
<div class="pageoverflow">
  <p class="pagetext"></p>
  <p class="pageinput">
    <input type="submit" name="{$actionid}submit" value="{$mod->Lang('copy')}"/>
    <input type="submit" name="{$actionid}cancel" value="{$mod->Lang('cancel')}"/>
  </p>
</div>
{$endform}