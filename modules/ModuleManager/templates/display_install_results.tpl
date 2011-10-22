<h3>{$mod->Lang('operation_results')}</h3>

{foreach from=$queue_results key='module_name' item='item'}
  <div class="pageoverflow">
    {if $item[0]}
      {* success *}
      <p class="pagetext" style="color: blue;">{$module_name}</p>
      <p class="pageinput">{$item[1]}</p>
    {else}
      {* error *}
      <p class="pagetext" style="color: red;">{$module_name}</p>
      <br/>
      <p class="pageinput" style="color: red;">{$item[1]}</p>
    {/if}
  </div>
{/foreach}

<div class="pageoverflow">
  <p class="pagetext"></p>
  <p class="pageinput">{$return_link}</p>
</div>