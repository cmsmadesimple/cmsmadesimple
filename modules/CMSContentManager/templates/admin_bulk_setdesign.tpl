<h3>{$mod->Lang('prompt_bulk_setdesign')}:</h3>

{form_start multicontent=$multicontent}
<div class="pageoverflow">
<ul>
  {foreach from=$displaydata item='rec'}
  <li>({$rec.id}) : {$rec.name} <em>({$rec.alias})</em></li>
  {/foreach}
<ul>
</div>

<div class="pageoverflow">
  <p class="pagetext"><label for="design_ctl">{$mod->Lang('prompt_design')}:</label></p>
  <p class="pageinput">
    <select id="design_ctl" name="{$actionid}design">
    {html_options options=$alldesigns selected=$dflt_design_id}
    </select>
  </p>
</div>

<div class="pageoverflow">
  <p class="pagetext"><label for="template_ctl">{$mod->Lang('prompt_template')}:</label></p>
  <p class="pageinput">
    <select id="template_ctl" name="{$actionid}template">
    {html_options options=$alltemplates selected=$dflt_tpl_id}
    </select>
  </p>
</div>

<div class="pageoverflow">
  <p class="pagetext">{$mod->Lang('prompt_confirm_operation')}:</p>
  <p class="pageinput">
    <input type="checkbox" id="confirm1" value="1" name="{$actionid}confirm1">&nbsp;
    <label for="confirm1">{$mod->Lang('prompt_confirm1')}</label><br/>
    <input type="checkbox" id="confirm2" value="1" name="{$actionid}confirm2">&nbsp;
    <label for="confirm2">{$mod->Lang('prompt_confirm2')}</label>
  </p>
</div>

<div class="pageoverflow">
  <p class="pagetext"></p>
  <p class="pageinput">
    <input type="submit" name="{$actionid}submit" value="{$mod->Lang('submit')}"/>
    <input type="submit" name="{$actionid}cancel" value="{$mod->Lang('cancel')}"/>
  </p>
</div>
{form_end}