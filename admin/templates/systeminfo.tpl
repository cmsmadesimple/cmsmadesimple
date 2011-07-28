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

<table width="100%">
  <tr>
  <td width="45%"><p style="margin-left: 10px;">{si_lang a=cms_version}</p></td>
  <td width="50%"><p>{$cms_version}</p></td>
  <td width="5%"></td>
  </tr>

  <tr>
  <td width="45%"><h4>{si_lang a=installed_modules}</h4></td></tr>
  {foreach from=$installed_modules item='module'}
  <tr>
    <td width="45%"><p style="margin-left: 10px;">{$module.module_name}</p></td>
    <td width="50%"><p>{$module.version}</p></td>
	<td width="5%"></td>
	</tr>
  {/foreach}

  <tr><td width="45%"><h4>{si_lang a=config_information}</h4></td></tr>

  {foreach from=$config_info key='view' item='tmp'}
  {foreach from=$tmp key='key' item='test'}
	<tr>
    <td width="45%"><p style="margin-left: 10px;">{$test->title}:</p></td>
	<td width="50%"><p>
	{if isset($test->value)}{$test->value|default:"&nbsp;"}{/if}
	{if isset($test->secondvalue)}({$test->secondvalue|default:"&nbsp;"}){/if}
	{if isset($test->error_fragment)}<a class="external" rel="external" href="{$cms_install_help_url}#{$test->error_fragment}"><img src="themes/{$themename}/images/icons/system/info-external.gif" title="?" alt="?" /></a>{/if}
	{if isset($test->message)}<br />{$test->message}{/if}
	</p></td>
	<td width="5%">{if isset($test->res)}<img class="icon-extra" src="themes/{$themename}/images/icons/extra/{$test->res}.gif" title="{$test->res_text}" alt="{$test->res_text}" />{/if}</td>
	</tr>
  {/foreach}
{/foreach}

</table>

</fieldset>

<fieldset>
<legend><strong>{si_lang a=php_information}</strong>: </legend>

<table width="100%">
{foreach from=$php_information key='view' item='tmp'}
  {foreach from=$tmp key='key' item='test'}
	<tr>
    <td width="45%"><p style="margin-left: 10px;">{si_lang a=$key} ({$key}):</p></td>
	<td width="50%"><p>
	{if isset($test->value) && $test->display_value != 0}&nbsp;{$test->value}{/if}
	{if isset($test->secondvalue)}({$test->secondvalue}){/if}
	{if isset($test->error_fragment)}<a class="external" rel="external" href="{$cms_install_help_url}#{$test->error_fragment}"><img src="themes/{$themename}/images/icons/system/info-external.gif" title="?" alt="?" /></a>{/if}
	{if isset($test->message)}<br />{$test->message}{/if}
	{if isset($test->opt)}
		{foreach from=$test->opt key='key' item='opt'}
			<br />{$key}: {$opt.message} <img class="icon-extra" src="themes/{$themename}/images/icons/extra/{$opt.res}.gif" alt="{$opt.res_text}" title="{$opt.res_text}" />
		{/foreach}
	{/if}
	</p></td>
	<td width="5%">{if isset($test->res)}<img class="icon-extra" src="themes/{$themename}/images/icons/extra/{$test->res}.gif" title="{$test->res_text}" alt="{$test->res_text}" />{/if}</td>
	</tr>
  {/foreach}
{/foreach}
</table>

</fieldset>

<fieldset>
<legend><strong>{si_lang a=server_information}</strong>: </legend>

<table width="100%">
{foreach from=$server_info key='view' item='tmp'}
  {foreach from=$tmp key='key' item='test'}
	<tr>
	<td width="45%"><p style="margin-left: 10px;">{si_lang a=$key} ({$key}):</p></td>
	<td width="50%"><p>
	{if isset($test->value)}{$test->value}{/if}
	{if isset($test->secondvalue)}({$test->secondvalue}){/if}
	{if isset($test->message)}<br />{$test->message}{/if}
	</p></td>
	<td width="5%">{if isset($test->res)}<img class="icon-extra" src="themes/{$themename}/images/icons/extra/{$test->res|default:"space"}.gif" title="{$test->res_text|default:""}" alt="{$test->res_text|default:""}" />{/if}</td>
	</tr>
  {/foreach}
{/foreach}

<tr><td width="45%"><h4>{si_lang a=permission_information}</h4></td></tr>
{foreach from=$permission_info key='view' item='tmp'}
  {foreach from=$tmp key='key' item='test'}
	<tr>
	<td width="45%"><p style="margin-left: 10px;">{$key}:</p></td>
	<td width="50%"><p>
	{if isset($test->value)}{$test->value}{/if}
	{if isset($test->secondvalue)}({$test->secondvalue}){/if}
	{if isset($test->message)}<br />{$test->message}{/if}
	</p></td>
	<td width="5%">{if isset($test->res)}<img class="icon-extra" src="themes/{$themename}/images/icons/extra/{$test->res}.gif" title="{$test->res_text}" alt="{$test->res_text}" />{/if}</td>
	</tr>
  {/foreach}
{/foreach}
</table>

</fieldset>

<p class="pageback"><a class="pageback" href="{$backurl}">&#171; {si_lang a=back}</a></p>

</div>

</div>
