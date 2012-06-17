<div class="pagecontainer">
  <div class="pageoverflow">{$header}</div>
  {if isset($subheader)}
    <div class="pageheader">{$subheader}
    {if isset($wiki_url) && isset($image_help_external)}
       <span class="helptext">
         <a class='helpicon' href="{$wiki_url}" target="_blank">{$image_help_external}</a><a href="{$wiki_url}" target="_blank">{'help'|lang}</a> ({'new_window'|lang})
       </span>
    {/if}
    </div>
  {/if}
  
  {if isset($content)}
    {$content}
  {elseif isset($error)}
    <div class="pageerrorcontainer"><div class="pageoverflow"><ul class="pageerror"><li>{$error}</li></ul></div></div>
  {elseif isset($plugins)}
    <table cellspacing="0" class="pagetable">
      <thead> 
       <tr>
         <th>{'name'|lang}</th>
         <th>{'type'|lang}</th>
         <th class="pagew10">{'cachable'|lang}</th>
         <th class="pagew10">{'help'|lang}</th>
         <th class="pagew10">{'about'|lang}</th>
       </tr>
      </thead> 
      <tbody>
      {foreach from=$plugins item='one'}
	{cycle values="row1,row2" assign='rowclass'}
	<tr class="{$rowclass}">
         <td>
           {if isset($one.help_url)}
             <a href="{$one.help_url}">{$one.name}</a>
           {else}
             {$one.name}
           {/if}
         </td>
         <td>
            {$one.type}
         </td>
         <td>{if isset($one.cachable) && $one.cachable != ''}{$one.cachable|lang}{/if}</td>
         <td>
           {if isset($one.help_url)}
             <a href="{$one.help_url}">{'help'|lang}</a>
           {/if}
         </td>
         <td>
           {if isset($one.about_url)}
             <a href="{$one.about_url}">{'about'|lang}</a>
           {/if}
         </td>
       </tr>
      {/foreach}
      </tbody>
    </table>
  {/if}
</div>
<p class="pageback"><a class="pageback" href="{$back_url}">&#171; {'back'|lang}</a></p>