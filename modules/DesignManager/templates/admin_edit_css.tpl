<script type="text/javascript">
$(document).ready(function(){
  $(document).on('click', '#applybtn', function(e){
    // serialize the form
    e.preventDefault();
    var url = $('#form_editcss').attr('action')+'?showtemplate=false&m1_apply=1';
    var data = $('#form_editcss').serializeArray();
    $.post(url,data,function(data,textStatus,jqXHR){
      $('#cancelbtn').attr('value','{$mod->Lang('close')}');
    });
  });
});
</script>

{if !$css->get_id()}
<h3>{$mod->Lang('create_stylesheet')}</h3>
{else}
<h3>{$mod->Lang('edit_stylesheet')}: {$css->get_name()} ({$css->get_id()})</h3>
{/if}

{form_start id='form_editcss' extraparms=$extraparms}
<fieldset>
  <div style="width: 49%; float: left;">
    <div class="pageoverflow">
      <p class="pagetext"></p>
      <p class="pageinput">
        <input type="submit" name="{$actionid}submit" value="{$mod->Lang('submit')}"/>
        {if $css->get_id()}
        <input type="submit" id="applybtn" name="{$actionid}apply" value="{$mod->Lang('apply')}"/>
        {/if}
        <input type="submit" id="cancelbtn" name="{$actionid}cancel" value="{$mod->Lang('cancel')}"/>
      </p>
    </div>

    <div class="pageoverflow">
      <p class="pagetext"><label for="css_name">*{$mod->Lang('prompt_name')}:</label>&nbsp;{cms_help key2=help_stylesheet_name}</p>
      <p class="pageinput">
        <input id="css_name" type="text" name="{$actionid}name" size="50" maxlength="50" value="{$css->get_name()}"/>
      </p>
    </div>
  </div>{* column *}

  <div style="width: 49%; float: right;">
    {if $css->get_id()}
    <div class="pageoverflow">
      <p class="pagetext"><label for="css_created">{$mod->Lang('prompt_created')}:</label>&nbsp;{cms_help key2=help_stylesheet_created}</p>
      <p class="pageinput">
        <input type="text" id="css_created" value="{$css->get_created()|date_format:'%x %X'}" readonly="readonly"/>
      </p>
    </div>
    <div class="pageoverflow">
      <p class="pagetext"><label for="css_modified">{$mod->Lang('prompt_modified')}:</label>&nbsp;{cms_help key2=help_stylesheet_modified}</p>
      <p class="pageinput">
        <input type="text" id="css_modified" value="{$css->get_modified()|date_format:'%x %X'}" readonly="readonly"/>
      </p>
    </div>
    {/if}
  </div>{* column *}
</fieldset>

{tab_header name='content' label=$mod->Lang('prompt_stylesheet')}
{tab_header name='media_type' label=$mod->Lang('prompt_media_type')}
{tab_header name='media_query' label=$mod->Lang('prompt_media_query')}
{tab_header name='description' label=$mod->Lang('prompt_description')}
{if $has_designs_right}
{tab_header name='designs' label=$mod->Lang('prompt_designs')}
{/if}

{tab_start name='content'}
<div class="pageoverflow">
  <p class="pagetext"><label for="stylesheet">{$mod->Lang('prompt_stylesheet')}:</label>&nbsp;{cms_help key2=help_stylesheet_content}</p>
  <p class="pageinput">
    {syntax_area id='stylesheet' prefix=$actionid name=content value=$css->get_content() type=css}
  </p>
</div>

{tab_start name='media_type'}
<div class="pagewarning">{$mod->Lang('info_editcss_mediatype_tab')}</div>
<div class="pageoverflow">
  <p class="patetext">{$mod->Lang('prompt_media_type')}:</p>
  {assign var='tmp' value='all,aural,speech,braille,embossed,handheld,print,projection,screen,tty,tv'}
  {assign var='all_types' value=explode(',',$tmp)}
  <p class="pageinput">
    {foreach from=$all_types item='type' name='media_type'}{strip}
      <input id="media_type_{$type}" type="checkbox" name="{$actionid}media_type[]" value="{$type}" {if $css->has_media_type($type)}checked="checked"{/if}/>
      &nbsp;
      {assign var='tmp' value='media_type_'|cat:$type}
      <label for="media_type_{$type}">{$mod->Lang($tmp)}</label>
      {if !$smarty.foreach.media_type.last}<br/>{/if}
    {/strip}{/foreach}
  </p>
</div>

{tab_start name='media_query'}
<div class="pagewarning">{$mod->Lang('info_editcss_mediaquery_tab')}</div>
<div class="pageoverflow">
  <p class="pagetext"><label for="mediaquery">{$mod->Lang('prompt_media_query')}:</label>&nbsp;{cms_help key2=help_css_mediaquery}</p>
  <p class="pageinput">
    <textarea id="mediaquery" name="{$actionid}media_query" rows="10" columns="80">{$css->get_media_query()}</textarea>
  </p>
</div>

{tab_start name='description'}
<div class="pageoverflow">
  <p class="pagetext"><label for="description">{$mod->Lang('prompt_description')}:</label>&nbsp;{cms_help key2=help_css_description}</p>
  <p class="pageinput">
    <textarea id="description" name="{$actionid}description" rows="10" columns="80">{$css->get_description()}</textarea>
  </p>
</div>

{if $has_designs_right}
{tab_start name='designs'}
<div class="pageoverflow">
  <p class="pagetext"><label for="designlist">{$mod->Lang('prompt_designs')}:</label>&nbsp;{cms_help key2=help_css_designs}</p>
  <p class="pageinput">
    <select name="{$actionid}design_list[]" multiple="multiple" size="5">
    {html_options options=$design_list selected=$css->get_designs()}
    </select>
  </p>
</div>
{/if}

{tab_end}

{form_end}