<h3>{$mod->Lang('copy_template')}</h3>

{form_start tpl=$actionparams.tpl}
<fieldset>
  <legend>{$mod->Lang('prompt_source_template')}:</legend>
  <div style="width: 49%; float: left;">
  <div class="pageoverflow">
    <p class="pagetext"><label for="tpl_name">*{$mod->Lang('prompt_name')}:</label></p>
    <p class="pageinput">
      <input id="tpl_name" type="text" size="50" maxlength="50" value="{$tpl->get_name()}" readonly="readonly"/>
    </p>
  </div>

  {if isset($type_list)}
  <div class="pageoverflow">
    <p class="pagetext">{$mod->Lang('prompt_type')}:</p>
    <p class="pageinput">
      {$type_list[$tpl->get_type_id()]}
    </p>
  </div>
  {/if}

  {if isset($user_list)}
  <div class="pageoverflow">
    <p class="pagetext">{$mod->Lang('prompt_owner')}:</p>
    <p class="pageinput">
      {$user_list[$tpl->get_owner_id()]}
    </p>
  </div>
  {/if}

  {if isset($category_list)}
  <div class="pageoverflow">
    <p class="pagetext">{$mod->Lang('prompt_category')}:</p>
    <p class="pageinput">
      {$category_list[$tpl->get_category_id()|default:0]}
    </p>
  </div>
  {/if}

  </div>{* column *}

  <div style="width: 49%; float: right;">
  {if $tpl->get_id()}
    <div class="pageoverflow">
      <p class="pagetext"><label for="tpl_created">{$mod->Lang('prompt_created')}:</label></p>
      <p class="pageinput">
        <input type="text" id="tpl_created" value="{$tpl->get_created()|localedate_format:'%x %X'}" readonly="readonly"/>
      </p>
    </div>
    <div class="pageoverflow">
      <p class="pagetext"><label for="tpl_modified">{$mod->Lang('prompt_modified')}:</label></p>
      <p class="pageinput">
        <input type="text" id="tpl_modified" value="{$tpl->get_modified()|localedate_format:'%x %X'}" readonly="readonly"/>
      </p>
    </div>
  {/if}
  </div>{* column *}
</fieldset>

<fieldset>
  <legend>{$mod->Lang('prompt_dest_template')}</legend>
  <div class="pageoverflow">
    <p class="pagetext"><label for="tpl_destname">{$mod->Lang('prompt_name')}:</label></p>
    <p class="pageinput">
      <input type="text" id="tpl_destname" name="{$actionid}new_name" value="{$new_name|default:''}" size="50" maxlength="50"/>
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
