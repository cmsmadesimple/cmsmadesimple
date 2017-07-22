<h3>{$mod->Lang('createthumbnail')}</h3>
{$startform}
<div class="pageoverflow">
  <p class="pagetext">{$mod->Lang('info_createthumb')}:</p>
  <p class="pagetext">{$thumb}</p>
</div>
<div class="pageoverflow">
  <p class="pagetext"></p>
  <p class="pageinput">
    <input type="submit" name="{$actionid}submit" value="{$mod->Lang('create')}"/>
    <input type="submit" name="{$actionid}cancel" value="{$mod->Lang('cancel')}"/>
  </p>
</div>
{$endform}