<h3>{$mod->Lang('edit_type')}:</h3>

{form_start}
<fieldset>
  <div class="pagecontainer" style="width: 49%; float: left;">{* left container *}
    <div class="pageoverflow">
      <p class="pagetext"></p>
      <p class="pageinput">
        <input type="submit" name="{$actionid}submit" value="{$mod->Lang('submit')}"/>
        <input type="submit" name="{$actionid}cancel" value="{$mod->Lang('cancel')}"/>
      </p>
    </div>

    <div class="pageoverflow">
      <p class="pagetext"><label for="originator">{$mod->Lang('prompt_originator')}:</label>&nbsp;{cms_help key2='help_type_originator' title=$mod->Lang('prompt_originator')}</p>
      <p class="pageinput">{$type->get_originator(TRUE)}</p>
    </div>

    <div class="pageoverflow">
      <p class="pagetext"><label for="name">{$mod->Lang('prompt_name')}:</label>&nbsp;{cms_help key2='help_type_name' title=$mod->Lang('prompt_name')}</p>
      <p class="pageinput">{$type->get_name()}</p>
    </div>

    <div class="pageoverflow">
      <p class="pagetext"><label for="descname">{$mod->Lang('prompt_descriptive_name')}:</label>&nbsp;{cms_help key2='help_type_descriptive_name' title=$mod->Lang('prompt_descriptive_name')}</p>
      <p class="pageinput">{$type->get_langified_display_value()}</p>
    </div>

  </div>{* left container *}

  {* right container *}
  <div style="width: 45%; float: right;">
    <div class="pageoverflow">
      <p class="pagetext"><label for="hasdflt">{$mod->Lang('prompt_has_dflt')}:</label>&nbsp;{cms_help key2=help_has_dflt title=$mod->Lang('prompt_has_dflt')}</p>
      <p class="pageinput">{if $type->get_dflt_flag()}{$mod->Lang('yes')}{else}{$mod->Lang('no')}{/if}</p>
    </div>

    <div class="pageoverflow">
      <p class="pagetext"><label for="rcb">{$mod->Lang('prompt_requires_content_blocks')}:</label>&nbsp;{cms_help key2='help_type_reqcontentblocks' title=$mod->Lang('prompt_requires_content_blocks')}</p>
      <p class="pageinput">{if $type->get_content_block_flag()}{$mod->Lang('yes')}{else}{$mod->Lang('no')}{/if}</p>
    </div>

    <div class="pageoverflow">
      <p class="pagetext"><label for="created">{$mod->Lang('prompt_created')}:</label>&nbsp;{cms_help key2='help_type_createdate' title=$mod->Lang('prompt_created')}</p>
      <p class="pageinput">{$type->get_create_date()|date_format:'%x %X'}</p>
    </div>
    <div class="pageoverflow">
      <p class="pagetext"><label for="modified">{$mod->Lang('prompt_modified')}:</label>&nbsp;{cms_help key2='help_type_modifieddate' title=$mod->Lang('prompt_modified')}</p>
      <p class="pageinput">{$type->get_modified_date()|date_format:'%x %X'}</p>
    </div>
  </div>{* right container *}
  <div style="clear: both;"></div>
</fieldset>{* container *}

<input type="hidden" name="{$actionid}type" value="{$type->get_id()}"/>

{if $type->get_content_callback() != ''}
{tab_header name='content' label=$mod->Lang('prompt_proto_template')}
{/if}

{tab_header name='description' label=$mod->Lang('prompt_description')}

{if $type->get_content_callback() != ''}
{tab_start name='content'}
<div class="pageoverflow">
  <p class="pagetext">
    <label for="type_dflt_contents">{$mod->Lang('prompt_proto_template')}:</label>&nbsp;{cms_help key2='help_proto_template' title=$mod->Lang('prompt_proto_template')}
  </p>
  <p class="pageinput">
    {cms_textarea id=type_dflt_contents prefix=$actionid name=dflt_contents value=$type->get_dflt_contents() type='smarty' rows=20 cols=80}
  </p>

  <div class="pagecontainer">
    <input type="submit" name="{$actionid}reset" value="{$mod->Lang('reset_factory')}" onclick="return confirm('{$mod->Lang('confirm_reset_type')|escape:'javascript'}')"/>
  </div>
</div>
{/if}

{tab_start name='description'}
<div class="pageoverflow">
  <p class="pagetext"><label for="type_description">{$mod->Lang('prompt_description')}:</label>&nbsp;{cms_help key2='help_type_description' title=$mod->Lang('prompt_description')}
  <p class="pageinput">
    <textarea id="type_description" name="{$actionid}description" rows="5" cols="80">{$type->get_description()}</textarea>
  </p>
</div>
{tab_end}

{form_end}