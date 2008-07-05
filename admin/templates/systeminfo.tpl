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
  <p class="pageinput"><strong>{$cms_version}</strong></p>
</div>
<br />
<div class="pageoverflow">
<h4 class="h-inside">{si_lang a=installed_modules}</h4>
 </div>
{foreach from=$installed_modules item='module'}
  <div class="pageoverflow">
    <p class="pagetext">{$module.module_name}</p>
    <p class="pageinput"><strong>{$module.version}</strong></p>
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
	{if isset($test->secondvalue)}
		<p class="pageinput"><strong>{$test->value}</strong> ({$test->secondvalue}) <img class="icon-extra" src="themes/{$themename}/images/icons/extra/{$test->res}.gif" title="{$test->res_text}" alt="{$test->res_text}" /></p>
	{elseif isset($test->value)}
		<p class="pageinput"><strong>{$test->value}</strong></p>
    {/if}
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
	{if isset($test->secondvalue)}
		<p class="pageinput"><strong>{$test->value}</strong> ({$test->secondvalue}) <img class="icon-extra" src="themes/{$thememane}/images/icons/extra/{$test->res}.gif" title="{$test->res_text}" alt="{$test->res_text}" /></p>
	{elseif isset($test->value)}
		<p class="pageinput"><strong>{$test->value}</strong></p>
    {/if}
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
	{if isset($test->secondvalue)}
		<p class="pageinput"><strong>{$test->value}</strong> ({$test->secondvalue}) <img class="icon-extra" src="themes/{$thememane}/images/icons/extra/{$test->res}.gif" title="{$test->res_text}" alt="{$test->res_text}" /></p>
	{elseif isset($test->value)}
		<p class="pageinput"><strong>{$test->value}</strong></p>
    {/if}
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
	{if isset($test->secondvalue)}
		<p class="pageinput"><strong>{$test->value}</strong> ({$test->secondvalue}) <img class="icon-extra" src="themes/{$thememane}/images/icons/extra/{$test->res}.gif" title="{$test->res_text}" alt="{$test->res_text}" /></p>
	{elseif isset($test->value)}
		<p class="pageinput"><strong>{$test->value}</strong></p>
    {/if}
	</div>
  {/foreach}
{/foreach}
<br />

<!--
<div class="pageoverflow">
<h4 class="h-inside">{si_lang a=ids_information}</h4>
</div>
{foreach from=$ids_info key='view' item='tmp'}
  {foreach from=$tmp key='key' item='test'}
	<div class="pageoverflow">
		<p class="pagetext">{$test->title}:</p>
	{if isset($test->secondvalue)}
		<p class="pageinput"><strong>{$test->value}</strong> ({$test->secondvalue}) <img class="icon-extra" src="themes/{$thememane}/images/icons/extra/{$test->res}.gif" title="{$test->res_text}" alt="{$test->res_text}" /><br />
	  {if isset($test->opt)}
		{si_lang a=current_file_timestamp}: {$test->opt.current_file_timestamp|date_format:$test->opt.format_timestamp}<br />
		{si_lang a=current_db_timestamp}: {$test->opt.current_db_timestamp|date_format:$test->opt.format_timestamp}<br />
	  {/if}
		</p>
	{elseif isset($test->value)}
		<p class="pageinput"><strong>{$test->value}</strong></p>
    {/if}
	</div>
  {/foreach}
{/foreach}
<br />
-->

</fieldset>

<p class="pageback"><a class="pageback" href="{$backurl}">&#171; {si_lang a=back}</a></p>

</div>

</div>
