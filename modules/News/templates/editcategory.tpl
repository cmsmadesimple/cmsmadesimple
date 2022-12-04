{if isset($catid)}
<h3>{$mod->Lang('editcategory')}</h3>
{else}
<h3>{$mod->Lang('addcategory')}</h3>
{/if}
<div class="information">{$mod->Lang('info_categories')}</div>

<script type="text/javascript">
$(document).ready(function(){
  $('#{$actionid}cancel').click(function(){
    $(this).closest('form').attr('novalidate','novalidate');
  });
});
</script>

{$startform}
	<div class="pageoverflow">
		<p class="pagetext"><label for="{$actionid}name">*{$mod->Lang('name')}:</label> {cms_help key='help_category_name' title=$mod->Lang('name')}</p>
		<p class="pageinput">
		  <input type="text" id="{$actionid}name" name="{$actionid}name" value="{$name|cms_escape|default:''}"/ required>
		</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext"><label for="{$actionid}parent">{$mod->Lang('parent')}:</label> {cms_help key='help_category_parent' title=$mod->Lang('parent')}</p>
		<p class="pageinput">
                  <select id="{$actionid}parent" name="{$actionid}parent">
                    {html_options options=$categories selected=$parent}
                  </select>
                </p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">&nbsp;</p>
		<p class="pageinput">
                  <input type="submit" name="{$actionid}submit" value="{$mod->Lang('submit')}"/>
                  <input type="submit" id="{$actionid}cancel" name="{$actionid}cancel" value="{$mod->Lang('cancel')}"/>
                </p>
	</div>
{$endform}