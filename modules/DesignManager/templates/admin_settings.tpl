{form_start}
<div class="pageoverflow">
  <p class="pagetext"></p>
  <p class="pageinput">
    <input type="submit" name="{$actionid}submit" value="{$mod->Lang('submit')}"/>
  </p>
</div>
<fieldset>
  <legend>{$mod->Lang('prompt_locksettings')}:</legend>
  <div class="pageoverflow">
    <p class="pagetext"><label for="locktimeout">{$mod->Lang('lock_timeout')}:</label>&nbsp;{cms_help key2='help_locktimeout' title=$mod->Lang('lock_timeout')}</p>
    <p class="pageinput">
      <input id="locktimeout" type="text" name="{$actionid}lock_timeout" value="{$lock_timeout}" size="3" maxlength="3"/>
    </p>
  </div>
  <div class="pageoverflow">
    <p class="pagetext"><label for="lockrefresh">{$mod->Lang('lock_refresh')}:</label>&nbsp;{cms_help key2='help_lockrefresh' title=$mod->Lang('lock_refresh')}</p>
    <p class="pageinput">
      <input id="lockrefresh" type="text" name="{$actionid}lock_refresh" value="{$lock_refresh}" size="4" maxlength="4"/>
    </p>
  </div>
</fieldset>
{form_end}