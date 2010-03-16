<div class="pagecontainer">
{if empty($smarty.get.cleanreport)}
	<p class="pageshowrows"><a href="{$systeminfo_cleanreport}">{si_lang a=copy_paste_forum}</a></p>
{/if}

{$showheader}

<div class="pageoverflow">


<div class="pageoverflow">
{si_lang a=help_systeminformation}
</div><hr/>


<fieldset>
<legend><strong>{si_lang a=cms_install_information}</strong>: </legend>

<div class="pageoverflow">
  <p class="pagetext">{si_lang a=cms_version}</p>
  <p class="pageinput">{$cms_version}</p>
</div>
<br />
<div class="pageoverflow">
<h4 class="h-inside">{si_lang a=installed_modules}</h4>
 </div>
{foreach from=$installed_modules item='module'}
  <div class="pageoverflow">
    <p class="pagetext">{$module.module_name}</p>
    <p class="pageinput">{$module.version}</p>
  </div>
{/foreach}

<br />

<div class="pageoverflow">
<h4 class="h-inside">{si_lang a=config_information}</h4>
</div>
{foreach from=$config_info key='view' item='tmp'}
  {foreach from=$tmp key='key' item='test'}
	<div class="pageoverflow">
		<p class="pagetext">{$test->title}:</p>
		<p class="pageinput">         
	{if isset($test->value)}{$test->value|default:"&nbsp;"}{/if}
	{if isset($test->secondvalue)}({$test->secondvalue|default:"&nbsp;"}){/if}
	{if isset($test->res)}<img class="icon-extra" src="themes/{$themename}/images/icons/extra/{$test->res}.gif" title="{$test->res_text}" alt="{$test->res_text}" />{/if}
	{if isset($test->error_fragment)}<a class="external" rel="external" href="{$cms_install_help_url}#{$test->error_fragment}"><img src="themes/{$themename}/images/icons/system/info-external.gif" title="?" alt="?" /></a>{/if}
	{if isset($test->message)}<br />{$test->message}{/if}
		</p>
	</div>
  {/foreach}
{/foreach}

</fieldset>



<fieldset>
<legend><strong>{si_lang a=php_information}</strong>: </legend>

{foreach from=$php_information key='view' item='tmp'}
  {foreach from=$tmp key='key' item='test'}
	<div class="pageoverflow">
		<p class="pagetext">{si_lang a=$key} ({$key}):</p>
		<p class="pageinput">
	{if isset($test->value) && $test->display_value != 0}&nbsp;{$test->value}{/if}
	{if isset($test->secondvalue)}({$test->secondvalue}){/if}
	{if isset($test->res)}<img class="icon-extra" src="themes/{$themename}/images/icons/extra/{$test->res}.gif" title="{$test->res_text}" alt="{$test->res_text}" />{/if}
	{if isset($test->error_fragment)}<a class="external" rel="external" href="{$cms_install_help_url}#{$test->error_fragment}"><img src="themes/{$themename}/images/icons/system/info-external.gif" title="?" alt="?" /></a>{/if}
	{if isset($test->message)}<br />{$test->message}{/if}
	{if isset($test->opt)}
		{foreach from=$test->opt key='key' item='opt'}
			<br />{$key}: {$opt.message} <img class="icon-extra" src="themes/{$themename}/images/icons/extra/{$opt.res}.gif" alt="{$opt.res_text}" title="{$opt.res_text}" />
		{/foreach}
	{/if}
		</p>
	</div>
  {/foreach}
{/foreach}

</fieldset>



<fieldset>
<legend><strong>{si_lang a=server_information}</strong>: </legend>

{foreach from=$server_info key='view' item='tmp'}
  {foreach from=$tmp key='key' item='test'}
	<div class="pageoverflow">
		<p class="pagetext">{si_lang a=$key} ({$key}):</p>
		<p class="pageinput">
	{if isset($test->value)}{$test->value}{/if}
	{if isset($test->secondvalue)}({$test->secondvalue}){/if}
	{if isset($test->res)}<img class="icon-extra" src="themes/{$themename}/images/icons/extra/{$test->res|default:"space"}.gif" title="{$test->res_text|default:""}" alt="{$test->res_text|default:""}" />{/if}
	{if isset($test->message)}<br />{$test->message}{/if}
		</p>
	</div>
  {/foreach}
{/foreach}
<br />

<div class="pageoverflow">
<h4 class="h-inside">{si_lang a=permission_information}</h4>
</div>
{foreach from=$permission_info key='view' item='tmp'}
  {foreach from=$tmp key='key' item='test'}
	<div class="pageoverflow">
		<p class="pagetext">{$key}:</p>
		<p class="pageinput">
	{if isset($test->value)}{$test->value}{/if}
	{if isset($test->secondvalue)}({$test->secondvalue}){/if}
	{if isset($test->res)}<img class="icon-extra" src="themes/{$themename}/images/icons/extra/{$test->res}.gif" title="{$test->res_text}" alt="{$test->res_text}" />{/if}
	{if isset($test->message)}<br />{$test->message}{/if}
		</p>
	</div>
  {/foreach}
{/foreach}
<br />


</fieldset>

<p class="pageback"><a class="pageback" href="{$backurl}">&#171; {si_lang a=back}</a></p>

</div>

</div>
