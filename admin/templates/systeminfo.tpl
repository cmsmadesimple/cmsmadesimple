<p class="pageoverflow">
{si_lang a=help_systeminformation}
</p><hr/>

<fieldset>
<legend><strong>{si_lang a=cms_install_information}</strong>: </legend>
<div class="pageoverflow">
  <p class="pagetext">{si_lang a=cms_version}</p>
  <p class="pageinput">{$cms_version}</p>
</div>
<h4>{si_lang a=installed_modules}</h4>
{foreach from=$installed_modules item='module'}
  <div class="pageoverflow">
    <p class="pagetext">{$module.module_name}</p>
    <p class="pageinput">{$module.version}</p>
  </div>
{/foreach}
</fieldset>

<fieldset>
<legend><strong>{si_lang a=php_information}</strong>: </legend>
{foreach from=$php_information key='key' item='one'}
  {if is_array($one)}
    {if empty($one[1])}
    <div class="pageoverflow">
      <p class="pagetext">{si_lang a=$key} ({$key}):</p>
      <p class="pageinput">{$one[0]}</p>
    </div>
    {else}
    <div class="pageoverflow" style="color: {$one[1]};">
      <p class="pagetext">{$key}: {si_lang a=$key}</p>
      <p class="pageinput">{$one[0]} <img src="{$one[1]}.png" alt="{$one[1]}" /></p>
    </div>
    {/if}
  {else}
  <div class="pageoverflow">
    <p class="pagetext">{si_lang a=$key} ({$key}):</p>
    <p class="pageinput">{$one}</p>
  </div>
  {/if}
{/foreach}
</fieldset>

<fieldset>
<legend><strong>{si_lang a=server_information}</strong>: </legend>
{foreach from=$server_info key='key' item='one'}
  <div class="pageoverflow">
    <p class="pagetext">{si_lang a=$key}:</p>
    <p class="pageinput">{$one}</p>
  </div>
{/foreach}
</fieldset>

<fieldset>
<legend><strong>{si_lang a=permission_information}</strong>: </legend>
{foreach from=$permission_info key='key' item='one'}
  <div class="pageoverflow">
    <p class="pagetext">{$key}:</p>
    <p class="pageinput">{$one}</p>
  </div>
{/foreach}
</fieldset>

<fieldset>
<legend><strong>{si_lang a=config_information}</strong>: </legend>
{foreach from=$config_info key='key' item='one'}
  <div class="pageoverflow">
    <p class="pagetext">{$key}:</p>
    <p class="pageinput">{$one}</p>
  </div>
{/foreach}
</fieldset>
