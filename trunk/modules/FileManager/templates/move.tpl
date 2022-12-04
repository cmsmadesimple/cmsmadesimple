<h3>{$mod->Lang('prompt_move')}</h3>
<p class="pageoverflow">{$mod->Lang('info_move')}:</p>

{$startform}
<div class="pageoverflow">
  <p class="pagetext">{$mod->Lang('itemstomove')}:</p>
  <p class="pageinput">
    <ul>
    {foreach from=$selall item='one'}
      <li>{$one}</li>
    {/foreach}
    </ul>
  </p>
</div>
<div class="pageoverflow">
  <p class="pagetext"><label for="destdir">{$mod->Lang('move_destdir')}:</label></p>
  <p class="pageinput">
    <select id="destdir" name="{$actionid}destdir">
    {html_options options=$dirlist selected=$cwd}
    </select>
  </p>
</div>
<div class="pageoverflow">
  <p class="pagetext"></p>
  <p class="pageinput">
    <input type="submit" name="{$actionid}submit" value="{$mod->Lang('move')}"/>
    <input type="submit" name="{$actionid}cancel" value="{$mod->Lang('cancel')}"/>
  </p>
</div>
{$endform}