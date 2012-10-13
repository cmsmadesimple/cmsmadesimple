<script type="text/javascript">
$(document).ready(function(){
  $('.helpicon').click(function(){
    var x = $(this).attr('name');
    $('#'+x).dialog();
  });
});
</script>
{if $category->get_id() == '' }
<h3>{$mod->Lang('create_category')}</h3>
{else}
<h3>{$mod->Lang('edit_category')}: {$category->get_name()} ({$category->get_id()})</h3>
{/if}

{form_start}
{if $category->get_id() != ''}
  <input type="hidden" name="{$actionid}cat" value="{$category->get_id()}"/>
{/if}
<div class="pageoverflow">
  <p class="pagetext"><label for="cat_name">*{$mod->Lang('prompt_name')}:&nbsp;{admin_icon icon='info.gif' name='help_category_name' class='helpicon'}</label></p>
  <p class="pageinput">
    <input type="text" id="cat_name" name="{$actionid}name" value="{$category->get_name()}" size="50" maxlength="50"/>
  </p>
</div>
<div class="pageoverflow">
  <p class="pagetext"><label for="cat_description">{$mod->Lang('prompt_description')}:&nbsp;{admin_icon icon='info.gif' name='help_category_desc' class='helpicon'}</label></p>
  <p class="pageinput">
    <textarea id="cat_description" name="{$actionid}description" rows="5" cols="80">{$category->get_description()}</textarea>
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

<div style="display: none;">{strip}
<div id="help_category_name" title="{$mod->Lang('prompt_help')}">
  {$mod->Lang('help_category_name')}
</div>
<div id="help_category_desc" title="{$mod->Lang('prompt_help')}">
  {$mod->Lang('help_category_desc')}
</div>
{/strip}</div>