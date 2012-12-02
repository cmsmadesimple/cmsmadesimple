<script type="text/javascript">
$(document).ready(function(){
  $('.helpicon').click(function(){
    var x = $(this).attr('name');
    $('#'+x).dialog();
  });
  $('#template_list').accordion({ldelim}autoHeight: false{rdelim});
  $('.template_view').click(function(){
    var x = $(this).attr('rel');
    $('#'+x).dialog();
  });
  $('#stylesheet_list').accordion({ldelim}autoHeight: false{rdelim});
  $('.stylesheet_view').click(function(){
    var x = $(this).attr('rel');
    $('#'+x).dialog();
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
        {$design_info.generated|date_format:'%x %X'}
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
{tab_header name='copyright' label=$mod->Lang('prompt_copyrightlicense')}
{tab_header name='templates' label=$mod->Lang('prompt_templates')}
{tab_header name='stylesheets' label=$mod->Lang('prompt_stylesheets')}
{tab_start name='description'}
<textarea>{$design_info.description}</textarea>
{tab_start name='copyright'}
TODO
{tab_start name='templates'}
<div id="template_list">
  {foreach from=$templates item='one' name='tpl'}
   {assign var='typename' value=$one.type_originator|cat:'::'|cat:$one.type_name}
   {assign var='type_obj' value=CmsLayoutTemplateType::load($typename)}
  <h3><a href="#" rel="tpl_{$smarty.foreach.tpl.index}" class="template_view">{$one.name}</a></h3>
  <div>
    <table class="pagetable">
      <tr>
         <th>{$mod->Lang('prompt_templatetype')}:</th>
         <td>{$type_obj->get_langified_display_value()}</td>
      </tr>
      <tr>
         <th>{$mod->Lang('prompt_description')}:</th>
         <td>{$one.desc|default:$mod->Lang('info_nodescription')}}</td>
      </tr>
    </table>
    <div id="tpl_{$smarty.foreach.tpl.index}" title="{$one.name}" style="display: none;"><textarea>{$one.data}</textarea></div>
  </div>
  {/foreach}
</div>
{tab_start name='stylesheets'}
<div id="stylesheet_list">
  {foreach from=$stylesheets item='one' name='css'}
  <h3><a href="#" rel="css_{$smarty.foreach.css.index}" class="stylesheet_view">{$one.name}</a></h3>
  <div><p>{$one.desc|default:$mod->Lang('info_nodescription')}}</p>
    <div id="css_{$smarty.foreach.css.index}" title="{$one.name}" style="display: none;"><textarea>{$one.data|cms_escape}</textarea></div>
  </div>
  {/foreach}
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