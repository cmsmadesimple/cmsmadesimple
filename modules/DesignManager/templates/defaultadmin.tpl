<script type="text/javascript">
$(document).ready(function(){
  $('img.viewhelp').click(function(){
    var n = $(this).attr('name');
    $('#'+n).dialog();
  });
});
</script>

{tab_header name='templates' label=$mod->Lang('prompt_templates')}
{if $manage_templates}
{tab_header name='categories' label=$mod->Lang('prompt_categories')}
{tab_header name='types' label=$mod->Lang('prompt_templatetypes')}
{/if}
{if $manage_designs}
{tab_header name='designs' label=$mod->Lang('prompt_designs')}
{/if}

{tab_start name='templates'}
{include file='module_file_tpl:LayoutManager;admin_defaultadmin_templates.tpl' scope='root'}
{if $manage_templates}
{tab_start name='categories'}
{include file='module_file_tpl:LayoutManager;admin_defaultadmin_categories.tpl' scope='root'}
{tab_start name='types'}
{include file='module_file_tpl:LayoutManager;admin_defaultadmin_types.tpl' scope='root'}
{/if}

{if $manage_designs}
{tab_start name='designs'}
{include file='module_file_tpl:LayoutManager;admin_defaultadmin_designs.tpl' scope='root'}
{/if}

{tab_end}

{* begin help *}
<div style="display: none;">{strip}
  <div id="help_create" title="{$mod->Lang('prompt_help')}">
  {$mod->Lang('help_create_template')}
  </div>
{/strip}</div>