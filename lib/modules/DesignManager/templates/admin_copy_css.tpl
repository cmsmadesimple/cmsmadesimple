<h3>{$mod->Lang('copy_stylesheet')}</h3>

{form_start css=$actionparams.css}
<fieldset>
  <legend>{$mod->Lang('prompt_source_css')}:</legend>
  <div style="width: 49%; float: left;">
    <div class="pageoverflow">
      <p class="pagetext"><label for="css_name">{$mod->Lang('prompt_name')}:</label></p>
      <p class="pageinput">
        <input id="css_name" type="text" size="50" maxlength="50" value="{$css->get_name()}" readonly/>
      </p>
    </div>
    <div class="pageoverflow">
      <p class="pagetext"><label for="css_name">{$mod->Lang('prompt_designs')}:</label></p>
      <p class="pageinput" style="max-height: 5em; overflow: auto;">
      {foreach $css->get_designs() as $design_id}
        {$design_names[$design_id]}<br/>
      {/foreach}
      </p>
    </div>
    <div class="pageoverflow">
      <p class="pagetext"><label for="css_name">{$mod->Lang('prompt_description')}:</label></p>
      <p class="pageinput" style="max-height: 5em; overflow: auto;">{$css->get_description()|summarize}</p>
    </div>
  </div>{* column *}

  <div style="width: 49%; float: right;">
  {if $css->get_id()}
    <div class="pageoverflow">
      <p class="pagetext">{$mod->Lang('prompt_created')}:</p>
      <p class="pageinput">
        <input type="text" id="css_created" value="{$css->get_created()|date_format:'%x %X'}" readonly="readonly"/>
      </p>
    </div>
    <div class="pageoverflow">
      <p class="pagetext"><label for="css_modified">{$mod->Lang('prompt_modified')}:</label></p>
      <p class="pageinput">
        <input type="text" id="css_modified" value="{$css->get_modified()|date_format:'%x %X'}" readonly="readonly"/>
      </p>
    </div>
  {/if}
  </div>{* column *}
</fieldset>

<div class="information">{$mod->Lang('info_copy_css')}</div>

<fieldset>
  <legend>{$mod->Lang('prompt_dest_css')}:</legend>
  <div class="pageoverflow">
    <p class="pagetext"><label for="css_destname">*{$mod->Lang('prompt_name')}:</label></p>
    <p class="pageinput">
      <input type="text" id="css_destname" name="{$actionid}new_name" value="{$new_name|default:''}" size="50" maxlength="50"/>
    </p>
  </div>
</fieldset>

<div class="pageoverflow">
  <p class="pagetext"></p>
  <p class="pageinput">
     <input type="submit" name="{$actionid}submit" value="{$mod->Lang('submit')}"/>
     <input type="submit" name="{$actionid}cancel" value="{$mod->Lang('cancel')}"/>
     <input type="submit" name="{$actionid}submitandedit" value="{$mod->Lang('submitandedit')}"/>
  </p>
</div>
{form_end}
