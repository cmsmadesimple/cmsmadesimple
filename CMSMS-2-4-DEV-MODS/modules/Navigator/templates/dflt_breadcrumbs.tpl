{* default breadcrumbs template *}
{strip}
<div class="breadcrumb">
  {if isset($starttext)}{$starttext}:&nbsp;{/if}
  {foreach $nodelist as $node}
    {$spanclass='breadcrumb'}
    {if $node->current}
      {$spanclass=$spanclass|cat:' current'}
    {/if}

    <span class="{$spanclass}">
      {if $node@last}
        {$node->menutext}
      {elseif $node->type == 'sectionheader'}
        {$node->menutext}&nbsp;
      {else}
        <a href="{$node->url}" title="{$node->menutext}">{$node->menutext}</a>
      {/if}
    </span>

    {if !$node@last}&raquo;&nbsp;{/if}
  {/foreach}
</div>
{/strip}