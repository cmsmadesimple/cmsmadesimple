<h3>{$mod->Lang('delete_stylesheet')}: {$css->get_name()} ({$css->get_id()})</h3>

{form_start css=$css->get_id()}

<fieldset>
  <div style="width: 49%; float: left;">
    <div class="pageoverflow">
      <p class="pagetext"><label for="css_name">*{$mod->Lang('prompt_name')}:</label></p>
      <p class="pageinput">
        <input id="css_name" type="text" readonly="readonly" size="50" maxlength="50" value="{$css->get_name()}"/>
      </p>
    </div>
  </div>{* column *}

  <div style="width: 49%; float: right;">
    {if $css->get_id()}
    <div class="pageoverflow">
      <p class="pagetext"><label for="css_created">{$mod->Lang('prompt_created')}:</label></p>
      <p class="pageinput">
        <input type="text" id="css_created" value="{$css->get_created()|localedate_format:'%x %X'}" readonly="readonly"/>
      </p>
    </div>
    <div class="pageoverflow">
      <p class="pagetext"><label for="css_modified">{$mod->Lang('prompt_modified')}:</label></p>
      <p class="pageinput">
        <input type="text" id="css_modified" value="{$css->get_modified()|localedate_format:'%x %X'}" readonly="readonly"/>
      </p>
    </div>
    {/if}
  </div>{* column *}
</fieldset>

<div class="pageoverflow">
  <p class="pagetext"></p>
  <p class="pageinput">
    <input id="check1" type="checkbox" name="{$actionid}check1" value="1">&nbsp;<label for="check1">{$mod->Lang('confirm_delete_css_1')}</label>
    <br/>
    <input id="check2" type="checkbox" name="{$actionid}check2" value="1">&nbsp;<label for="check2">{$mod->Lang('confirm_delete_css_2')}</label>
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
