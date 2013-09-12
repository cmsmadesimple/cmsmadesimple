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
  <p class="pagetext"><label for="destdir">{$mod->Lang('copy_destdir')}:</label></p>
  <p class="pageinput">
    <select id="destdir" name="{$actionid}destdir">
    {html_options options=$dirlist selected=$cwd}
    </select>
  </p>
</div>
{if count($selall) == 1}
<div class="pageoverflow">
  <p class="pagetext"><label for="destname">{$mod->Lang('copy_destname')}:</label></p>
  <p class="pageinput">
    <input type="text" id="destname" name="{$actionid}destname" size="50" maxlength="255"/>
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