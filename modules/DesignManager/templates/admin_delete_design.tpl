{form_start design=$design->get_id()}
<h3>{$mod->Lang('delete_design')}: {$design->get_name()} ({$design->get_id()})</h3>

<div class="pagewarning">{$mod->Lang('warning_deletedesign')}</div>

{if $design->has_templates() && $tpl_permission}
<div class="pagewarning">{$mod->Lang('warning_deletetemplate_attachments')}</div>
<div class="pageoverflow">
  <p class="pagetext"><label for-"opt_rm_tpl">{$mod->Lang('delete_attached_templates')}:</label></p>
  <p class="pageinput">
    <input type="checkbox" id="opt_rm_tpl" value="yes" name="{$actionid}delete_templates"/>&nbsp;
    {admin_icon class='helpicon' name='help_rm_tpl' icon='info.gif'}
  </p>
</div>
{/if}

{if $design->has_stylesheets() && $css_permission}
<div class="pagewarning">{$mod->Lang('warning_deletestylesheet_attachments')}</div>
<div class="pageoverflow">
  <p class="pagetext"><label for-"opt_rm_css">{$mod->Lang('delete_attached_stylesheets')}:</label></p>
  <p class="pageinput">
    <input type="checkbox" id="opt_rm_css" value="yes" name="{$actionid}delete_stylesheets"/>&nbsp;
    {admin_icon class='helpicon' name='help_rm_css' icon='info.gif'}
  </p>
</div>
{/if}

<div class="pageoverflow">
  <p class="pagetext">{$mod->Lang('confirm_delete_1')}:</p>
  <p class="pageinput">
    <input type="checkbox" id="opt_delete1" value="yes" name="{$actionid}confirm_delete1"/>&nbsp;
<label for="opt_delete1">{$mod->Lang('confirm_delete_2a')}:</label><br/>
    <input type="checkbox" id="opt_delete2" value="yes" name="{$actionid}confirm_delete2"/>&nbsp;
<label for="opt_delete2">{$mod->Lang('confirm_delete_2b')}:</label>
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

<div style="display: none;">
  <div id="help_rm_tpl" title="{$mod->Lang('prompt_help')}">{$mod->Lang('help_rm_tpl')}</div>
  <div id="help_rm_css" title="{$mod->Lang('prompt_help')}">{$mod->Lang('help_rm_css')}</div>
</div>

<script type="text/javascript">
$(document).ready(function(){
  $('.helpicon').click(function(){
    var x = $(this).attr('name');
    $('#'+x).dialog();
  });
});
</script>