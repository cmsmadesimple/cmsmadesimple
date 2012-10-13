<script type="text/javascript">
$(document).ready(function(){
  $('.helpicon').click(function(){
    var x = $(this).attr('name');
    $('#'+x).dialog();
  });
});
</script>
<h3>{$mod->Lang('view_type')}</h3>

<div>
  <div class="pagecontainer" style="width: 49%; float: left;">{* left container *}
    <div class="pageoverflow">
      <p class="pagetext">{$mod->Lang('prompt_originator')}:</p>
      <p class="pageinput">
        <input type="text" readonly="readonly" value="{$type->get_originator(TRUE)}"/>
      </p>
    </div>

    <div class="pageoverflow">
      <p class="pagetext">{$mod->Lang('prompt_name')}:</p>
      <p class="pageinput">
        <input type="text" readonly="readonly" value="{$type->get_name()}"/>
      </p>
    </div>

    <div class="pageoverflow">
      <p class="pagetext">{$mod->Lang('prompt_descriptive_name')}:</p>
      <p class="pageinput">
        <input type="text" readonly="readonly" value="{$type->get_langified_display_value()}"/>
      </p>
    </div>
  </div>{* left container *}

  {* right container *}
  <div>
    <div class="pagecontainer" style="width: 49%; float: right;">
    <div class="pageoverflow">
      <p class="pagetext">{$mod->Lang('prompt_has_dflt')}:&nbsp;{admin_icon icon='info.gif' class='helpicon' name=help_has_dflt}</p>
      <p class="pageinput">
        <input type="text" readonly="readonly" value="{if $type->get_dflt_flag()}{$mod->Lang('yes')}{else}{$mod->Lang('no')}{/if}"/>
      </p>
    </div>
    <div class="pageoverflow">
      <p class="pagetext">{$mod->Lang('prompt_created')}:</p>
      <p class="pageinput">
        <input type="text" readonly="readonly" value="{$type->get_create_date()|date_format:'%x %X'}"/>
      </p>
    </div>
    <div class="pageoverflow">
      <p class="pagetext">{$mod->Lang('prompt_modified')}:</p>
      <p class="pageinput">
        <input type="text" readonly="readonly" value="{$type->get_modified_date()|date_format:'%x %X'}"/>
      </p>
    </div>
  </div>{* right container *}
  <div style="clear: both;"></div>
</div>{* container *}

{form_start}<input type="hidden" name="{$actionid}type" value="{$type->get_id()}"/>

{if $type->get_content_callback() != ''}
{tab_header name='content' label=$mod->Lang('prompt_dflt_template')}
{/if}

{tab_header name='description' label=$mod->Lang('prompt_description')}

{if $type->get_content_callback() != ''}
{tab_start name='content'}
{syntax_area prefix=$actionid name=dflt_contents value=$type->get_dflt_contents()}
{admin_icon icon='info.gif' name='help_dflt_template' class='helpicon' title=$mod->Lang('whats_this')}
<div class="pagecontainer">
  <input type="submit" name="{$actionid}reset" value="{$mod->Lang('reset_factory')}" onclick="return confirm('{$mod->Lang('confirm_reset_type')}')"/>
</div>
{/if}

{tab_start name='description'}
<textarea name="{$actionid}description" rows="5" cols="80">{$type->get_description()}</textarea>
{tab_end}

<div class="pageoverflow">
  <p class="pagetext"></p>
  <p class="pageinput">
    <input type="submit" name="{$actionid}submit" value="{$mod->Lang('submit')}"/>
    <input type="submit" name="{$actionid}cancel" value="{$mod->Lang('cancel')}"/>
  </p>
</div>
{form_end}

<div style="display: none;">{strip}
  <div id="help_has_dflt" title="{$mod->Lang('prompt_help')}">{$mod->Lang('help_has_dflt')}</div>
  <div id="help_dflt_template" title="{$mod->Lang('prompt_help')}">{$mod->Lang('help_dflt_template')}</div>
{/strip}</div>