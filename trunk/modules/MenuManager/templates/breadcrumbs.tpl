{if isset($nodelist)}
{strip}
<div class="breadcrumb">
{$starttext}:&nbsp;
{foreach from=$nodelist item='node'}
  {assign var='spanclass' value='crumb'}
  {assign var='extraspanclass' value=''}
  {if $node->current == true}
    {assign var='extraspanclass' value=' current'}
  {/if}

  <span class="{$spanclass|cat:$extraspanclass}">
    {if $node->current == true}
       {$node->menutext}&nbsp;
    {elseif ($node->url == '' or $node->url == '#') && $node->type != 'sectionheader'}
       &raquo;&nbsp;
    {elseif $node->type == 'sectionheader'}   
       {$node->menutext}&nbsp;
    {else}
       <a href="{$node->url}" title="{$node->menutext}">{$node->menutext}</a>&nbsp;
    {/if}
  </span>
{/foreach}
</div>
{/strip}
{/if}