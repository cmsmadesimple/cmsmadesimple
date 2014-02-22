{form_start action='admin_general_tab'}
<div class="pageoverflow">
  <p class="pagetext"></p>
  <p class="pageinput">
    <input type="submit" name="{$actionid}submit" value="{$mod->Lang('submit')}" accesskey="s"/>
  </p>
</div>
<div class="pageoverflow">
  <p class="pagetext"><label for="locktimeout">{$mod->Lang('prompt_locktimeout')}:</label>&nbsp;{cms_help key2='help_general_locktimeout' title=$mod->Lang('prompt_locktimeout')}</p>
  <p class="pageinput">
    <input type="text" name="{$actionid}locktimeout" value="{$locktimeout}" size="3" maxlength="3"/>
  </p>
</div>
<div class="pageoverflow">
  <p class="pagetext"><label for="lockrefresh">{$mod->Lang('prompt_lockrefresh')}:</label>&nbsp;{cms_help key2='help_general_lockrefresh' title=$mod->Lang('prompt_lockrefresh')}</p>
  <p class="pageinput">
    <input type="text" name="{$actionid}lockrefresh" value="{$lockrefresh}" size="4" maxlength="4"/>
  </p>
</div>
{form_end}