{if isset($catid)}
<h3>{$mod->Lang('editcategory')}</h3>
{else}
<h3>{$mod->Lang('addcategory')}</h3>
{/if}

{$startform}
	<div class="pageoverflow">
		<p class="pagetext">*{$mod->Lang('name')}:</p>
		<p class="pageinput">{$inputname}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">{$mod->Lang('parent')}:</p>
		<p class="pageinput">
                  <select name="{$actionid}parent">
                    {html_options options=$categories selected=$parent}
                  </select>
                </p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">&nbsp;</p>
		<p class="pageinput">
                  <input type="submit" name="{$actionid}submit" value="{$mod->Lang('submit')}"/>
                  <input type="submit" name="{$actionid}cancel" value="{$mod->Lang('cancel')}"/>
                </p>
	</div>
{$endform}
