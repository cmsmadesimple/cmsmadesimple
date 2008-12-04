<div class="pagecontainer">
	
{* page header *}
<div class="pageoverflow">
    {$header_name} 
</div><!-- pageoverflow -->
	
{* display errors *}
{if isset($errors) || isset($error_msg)}
  <div class="pageerrorcontainer">
  <ul>
    {if isset($errors)}
      {foreach from=$errors item='one'}
        <li>{$one}</li>
      {/foreach}
    {/if}
    {if isset($error_msg)}
      <li>{$error_msg}</li>
    {/if}
  </ul>
  </div>
{/if}

{* display messages *}
{if $message}
<div class="pagemcontainer">
  <p>{$message}</p>
</div>
{/if}

{include file=$template_name}

</div>{* pagecontainer *}
<p class="pageback"><a class="pageback" href="{$back_url}">&#171; {lang string='back'}</a></p>
{literal}<script type="text/javascript">$('#page_tabs').tabs({fxAutoHeight: false});</script>{/literal}