<div class="pagecontainer">
{if empty($smarty.get.cleanreport)}
	<p class="pageshowrows"><a href="{$systeminfo_cleanreport}">{si_lang a=copy_paste_forum}</a></p>
{/if}
{$showheader}

<div class="pageoverflow">
	<div class="pageoverflow">
		<p>{si_lang a=help_systeminformation}</p>
	</div>
<hr/>
<table class="pagetable" cellspacing="0" summary="{si_lang a=cms_install_information}">
	<caption>{si_lang a=cms_install_information}:</caption>
  		<tr class="{cycle values='row1,row2'}">
  			<td width="45%">{si_lang a=cms_version}</td>
  			<td width="50%">{$cms_version}</td>
  			<td width="5%"></td>
  		</tr>
  		<tr>
  			<td><h4>{si_lang a=installed_modules}</4></td>
  		</tr>
  		{foreach from=$installed_modules item='module'}
  		<tr class="{cycle values='row1,row2'}">
    		<td width="45%">{$module.module_name}</td>
    		<td width="50%">{$module.version}</td>
			<td width="5%"></td>
  		</tr>
  		{/foreach}
  		<tr>
  			<td><h4>{si_lang a=config_information}</h4></td>
  		</tr>	
  	{foreach from=$config_info key='view' item='tmp'}
  		{foreach from=$tmp key='key' item='test'}
		<tr class="{cycle values='row1,row2'}">
    		<td width="45%">{$test->title}:</td>
			<td width="50%">
	{if isset($test->value)}{$test->value|default:"&nbsp;"}{/if}
	{if isset($test->secondvalue)}({$test->secondvalue|default:"&nbsp;"}){/if}
	{if isset($test->error_fragment)}<a class="external" rel="external" href="{$cms_install_help_url}#{$test->error_fragment}"><img src="themes/{$themename}/images/icons/system/info-external.gif" title="?" alt="?" /></a>{/if}
	{if isset($test->message)}<br />{$test->message}{/if}
			</td>
			<td width="5%">{if isset($test->res)}<img class="systemicon" src="themes/{$themename}/images/icons/extra/{$test->res}.gif" title="{$test->res_text}" alt="{$test->res_text}" />{/if}</td>
		</tr>
  		{/foreach}
	{/foreach}
</table>

<table class="pagetable" cellspacing="0" summary="{si_lang a=php_information}">
	<caption>{si_lang a=php_information}:</caption>
	{foreach from=$php_information key='view' item='tmp'}
  		{foreach from=$tmp key='key' item='test'}
		<tr class="{cycle values='row1,row2'}">
    		<td width="45%">{si_lang a=$key} ({$key}):</td>
			<td width="50%">
	{if isset($test->value) && $test->display_value != 0}&nbsp;{$test->value}{/if}
	{if isset($test->secondvalue)}({$test->secondvalue}){/if}
	{if isset($test->error_fragment)}<a class="external" rel="external" href="{$cms_install_help_url}#{$test->error_fragment}"><img src="themes/{$themename}/images/icons/system/info-external.gif" title="?" alt="?" /></a>{/if}
	{if isset($test->message)}<br />{$test->message}{/if}
	{if isset($test->opt)}
		{foreach from=$test->opt key='key' item='opt'}
			<br />{$key}: {$opt.message} <img class="systemicon" src="themes/{$themename}/images/icons/extra/{$opt.res}.gif" alt="{$opt.res_text}" title="{$opt.res_text}" />
		{/foreach}
	{/if}
			</td>
			<td width="5%">{if isset($test->res)}<img class="systemicon" src="themes/{$themename}/images/icons/extra/{$test->res}.gif" title="{$test->res_text}" alt="{$test->res_text}" />{/if}</td>
		</tr>
  		{/foreach}
	{/foreach}
</table>

<table class="pagetable" cellspacing="0" summary="{si_lang a=server_information}">
	<caption>{si_lang a=server_information}:</caption>
	{foreach from=$server_info key='view' item='tmp'}
  		{foreach from=$tmp key='key' item='test'}
		<tr class="{cycle values='row1,row2'}">
			<td width="45%">{si_lang a=$key} ({$key}):</td>
			<td width="50%">
			{if isset($test->value)}{$test->value|lower}{/if}
			{if isset($test->secondvalue)}({$test->secondvalue}){/if}
			{if isset($test->message)}<br />{$test->message}{/if}
			</td>
			<td width="5%">{if isset($test->res)}<img class="systemicon" src="themes/{$themename}/images/icons/extra/{$test->res|default:"space"}.gif" title="{$test->res_text|default:""}" alt="{$test->res_text|default:""}" />{/if}</td>
		</tr>
  		{/foreach}
	{/foreach}
	<tr>
		<td width="45%"><h4>{si_lang a=permission_information}</h4></td>
	</tr>
	{foreach from=$permission_info key='view' item='tmp'}
  		{foreach from=$tmp key='key' item='test'}
	<tr class="{cycle values='row1,row2'}">
		<td width="45%">{$key}:</td>
		<td width="50%">
		{if isset($test->value)}{$test->value}{/if}
		{if isset($test->secondvalue)}({$test->secondvalue}){/if}
		{if isset($test->message)}<br />{$test->message}{/if}
		</td>
		<td width="5%">{if isset($test->res)}<img class="systemicon" src="themes/{$themename}/images/icons/extra/{$test->res}.gif" title="{$test->res_text}" alt="{$test->res_text}" />{/if}</td>
	</tr>
  		{/foreach}
	{/foreach}
</table>

<p class="pageback"><a class="pageback" href="{$backurl}">&#171; {si_lang a=back}</a></p>
	</div>
</div>
