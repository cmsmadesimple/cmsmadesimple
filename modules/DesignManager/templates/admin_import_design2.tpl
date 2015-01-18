<script type="text/javascript">
$(document).ready(function(){
  $('.helpicon').click(function(){
    var x = $(this).attr('name');
    $('#'+x).dialog({ width: 'auto' });
  });
  $('.template_view').click(function(){
    var row = $(this).closest('tr');
    $('.template_content',row).dialog({
      width: 'auto',
      close: function( ev, ui ) {
         $(this).dialog('destroy');
      }
    });
    return false;
  });
  $('.stylesheet_view').click(function(){
    var row = $(this).closest('tr');
    $('.stylesheet_content',row).dialog({
      width: 'auto',
      close: function( ev, ui ) {
         $(this).dialog('destroy');
      }
    });
    return false;
  });
});
</script>

<h3>{$mod->Lang('import_design_step2')}</h3>

{form_start step=2 tmpfile=$tmpfile}
<div class="pageinfo">{$mod->Lang('info_import_xml_step2')}</div>

<fieldset>
  <div style="width: 49%; float: left;">
    <div class="pageoverflow">
      <p class="pagetext"><label for="import_newname">{$mod->Lang('prompt_name')}:</label></p>
      <p class="pageinput">
        <input id="import_newname" type="text" name="{$actionid}newname" value="{$new_name}" size="50" maxlength="50"/>
        &nbsp;{admin_icon name='help_import_newname' icon='info.gif' class='helpicon'}
        <br/>
        {$mod->Lang('prompt_orig_name')}: {$design_info.name}
      </p>
    </div>

    <div class="pageoverflow">
      <p class="pagetext">{$mod->Lang('prompt_created')}:</p>
      <p class="pageinput">
        {$tmp=$design_info.generated|date_format:'%x %X'}{if $tmp == ''}{$tmp=$mod->Lang('unknown')}{/if}
        <span style="color: red;">{$tmp}</span>&nbsp;{cms_help key2='help_import_created' title=''}
      </p>
    </div>
  </div>

  <div style="width: 49%; float: right;">
    <div class="pageoverflow">
      <p class="pagetext">{$mod->Lang('prompt_cmsversion')}:</p>
      <p class="pageinput">
        {if version_compare($design_info.cmsversion,$cms_version) < 0}
          <span style="color: red;">{$design_info.cmsversion}</span>&nbsp;{admin_icon name='help_import_cmsversion' icon='info.gif' class='helpicon'}
        {else}
          {$design_info.cmsversion}
        {/if}
      </p>
    </div>
  </div>
</fieldset>

{tab_header name='description' label=$mod->Lang('prompt_description')}
{* tab_header name='copyright' label=$mod->Lang('prompt_copyrightlicense') *}
{tab_header name='templates' label=$mod->Lang('prompt_templates')}
{tab_header name='stylesheets' label=$mod->Lang('prompt_stylesheets')}

{tab_start name='description'}
{* no name set on this field... *}
<textarea rows="5" cols="80">{$design_info.description}</textarea>

{* tab_start name='copyright' *}

{tab_start name='templates'}
<table class="pagetable">
  <thead>
    <tr>
      <th>{$mod->Lang('name')}</th>
      <th>{$mod->Lang('type')}</th>
      <th>{$mod->Lang('prompt_description')}</th>
      <th class="pageicon"></th>
    </tr>
  </thead>
  <tbody>
  {foreach $templates as $one}
   {$typename=$one.type_originator|cat:'::'|cat:$one.type_name}
   {$type_obj=CmsLayoutTemplateType::load($typename)}
   <tr class="{cycle values='row1,row2'}">
    <td>
      <h3 data-idx="{$one@index}" class="template_view pointer">{$one.name}</h3>
    </td>
    <td>{$type_obj->get_langified_display_value()}</td>
    <td>{$one.desc|default:$mod->Lang('info_nodescription')|summarize:80}
      <div id="tpl_{$one@index}" class="template_content" title="{$one.name}" style="display: none;"><textarea rows="10" cols="80">{$one.data}</textarea></div>
    </td>
    <td>
      {admin_icon class="template_view pointer" icon='view.gif' alt=lang('view')}
    </td>
  </tr>
  {/foreach}
  </tbody>
</table>


{tab_start name='stylesheets'}
<div id="stylesheet_list">
  <table class="pagetable">
    <thead>
      <tr>
        <th>{$mod->Lang('name')}</th>
	<th>{$mod->Lang('prompt_media_type')}</th>
        <th>{$mod->Lang('prompt_description')}</th>
	<th class="pageicon"></th>
      </tr>
    </thead>
    <tbody>
      {foreach from=$stylesheets item='one' name='css'}
      <tr>
        <td>
	  <h3 class="stylesheet_view pointer">{$one.name}</h3>
	</td>
	<td>{$one.mediatype}</td>
        <td>{$one.desc|default:$mod->Lang('info_nodescription')}
           <div class="stylesheet_content" title="{$one.name}" style="display: none;">
	     <textarea rows="10" cols="80">{$one.data}</textarea>
	   </div>
	</td>
	<td>
          {admin_icon class="stylesheet_view pointer" icon='view.gif' alt=lang('view')}
	</td>
      </tr>
      {/foreach}
    </tbody>
  </table>
</div>
{tab_end}

<div class="pageoverflow">
  <p class="pagetext">*{$mod->Lang('confirm_import')}:</p>
  <p class="pageinput">
    <input type="checkbox" name="{$actionid}check1" value="1" id="check1">&nbsp;<label for="check1">{$mod->Lang('confirm_import_1')}</label>
  </p>
</div>
<div class="pageoverflow">
  <p class="pagetext"></p>
  <p class="pageinput">
    <input type="submit" name="{$actionid}next2" value="{$mod->Lang('next')}"/>
    <input type="submit" name="{$actionid}cancel" value="{$mod->Lang('cancel')}"/>
  </p>
</div>
{form_end}

<div style="display: none;">{strip}
  <div id="help_import_xml_file" title="{$mod->Lang('prompt_help')}">{$mod->Lang('help_import_xml_file')}</div>
  <div id="help_import_newname" title="{$mod->Lang('prompt_help')}">{$mod->Lang('help_import_newname')}</div>
  <div id="help_import_cmsversion" title="{$mod->Lang('prompt_help')}">{$mod->Lang('help_import_cmsversion')}</div>
{/strip}</div>