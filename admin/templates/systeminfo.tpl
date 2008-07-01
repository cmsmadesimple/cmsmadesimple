<div class="pagecontainer">
{if empty($smarty.get.cleanreport)}
	<p class="pageshowrows"><a href="systeminfo.php?cleanreport=1">{si_lang a=copy_paste_forum}</a></p>
{/if}

{$showheader}

<div class="pageoverflow">


<p class="pageoverflow">
{si_lang a=help_systeminformation}
</p><hr/>


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
	{if isset($test->secondvalue)}
	<div class="pageoverflow" style="color: {$test->res};">
		<p class="pagetext">{$test->title}:</p>
		<p class="pageinput"><b>{$test->secondvalue}</b> {if isset($test->value)}({$test->value}){/if} <img class="icon-extra" src="themes/NCleanGrey/images/icons/extra/{$test->res}.gif" title="{$test->res_text}" alt="{$test->res_text}" /></p>
	</div>
    {else}
	<div class="pageoverflow">
		<p class="pagetext">{$test->title}:</p>
		<p class="pageinput">{$test->value}</p>
	</div>
    {/if}
  {/foreach}
{/foreach}

</fieldset>



<fieldset>
<legend><strong>{si_lang a=php_information}</strong>: </legend>

{foreach from=$php_information key='view' item='tmp'}
  {foreach from=$tmp key='key' item='test'}
	{if isset($test->secondvalue)}
	<div class="pageoverflow" style="color: {$test->res};">
		<p class="pagetext">{si_lang a=$key} ({$key}):</p>
		<p class="pageinput"><b>{$test->secondvalue}</b> {if isset($test->value)}({$test->value}){/if} <img class="icon-extra" src="themes/NCleanGrey/images/icons/extra/{$test->res}.gif" title="{$test->res_text}" alt="{$test->res_text}" /></p>
	</div>
    {else}
	<div class="pageoverflow">
		<p class="pagetext">{si_lang a=$key} ({$key}):</p>
		<p class="pageinput">{$test->value}</p>
	</div>
    {/if}
  {/foreach}
{/foreach}

</fieldset>



<fieldset>
<legend><strong>{si_lang a=server_information}</strong>: </legend>

{foreach from=$server_info key='view' item='tmp'}
  {foreach from=$tmp key='key' item='test'}
	{if isset($test->secondvalue)}
	<div class="pageoverflow" style="color: {$test->res};">
		<p class="pagetext">{si_lang a=$key} ({$key}):</p>
		<p class="pageinput"><b>{$test->secondvalue}</b> {if isset($test->value)}({$test->value}){/if} <img class="icon-extra" src="themes/NCleanGrey/images/icons/extra/{$test->res}.gif" title="{$test->res_text}" alt="{$test->res_text}" /></p>
	</div>
    {else}
	<div class="pageoverflow">
		<p class="pagetext">{si_lang a=$key} ({$key}):</p>
		<p class="pageinput">{$test->value}</p>
	</div>
    {/if}
  {/foreach}
{/foreach}
<br />

<div class="pageoverflow">
<h4 class="h-inside">{si_lang a=permission_information}</h4>
</div>
{foreach from=$permission_info key='view' item='tmp'}
  {foreach from=$tmp key='key' item='test'}
	{if isset($test->secondvalue)}
	<div class="pageoverflow" style="color: {$test->res};">
		<p class="pagetext">{$key}:</p>
		<p class="pageinput"><b>{$test->secondvalue}</b> {if isset($test->value)}({$test->value}){/if} <img class="icon-extra" src="themes/NCleanGrey/images/icons/extra/{$test->res}.gif" title="{$test->res_text}" alt="{$test->res_text}" /></p>
	</div>
    {else}
	<div class="pageoverflow">
		<p class="pagetext">{$key}:</p>
		<p class="pageinput">{$test->value}</p>
	</div>
    {/if}
  {/foreach}
{/foreach}


</fieldset>

<p class="pageback"><a class="pageback" href="{$backurl}">&#171; {si_lang a=back}</a></p>

</div>

</div>
