{* minimal navigation *}
{*
  variables:
  node: contains the current node.
  aclass: is used to build a string containing class names given to the a tag if one is used
  liclass: is used to build a string containing class names given to the li tag.
*}
{* CSS classes used in this template:
.currentpage - The active/current page
.bullet_sectionheader - To style section header
hr.separator - To style the ruler for the separator *}

{if !isset($depth)}{$depth=0}{/if}

{if isset($nodes)}{strip}
<ul>
  {foreach $nodes as $node}
    {if $node->type == 'sectionheader'}
      {* section header *}
      <li class="sectionheader{if $node->parent} activeparent{/if}">
        {$node->menutext}
        {if isset($node->children)}
          {include file=$smarty.template nodes=$node->children depth=$depth+1}
        {/if}
      </li>
    {else if $node->type == 'separator'}
      <li style="list-style-type: none;"><hr class="separator"/></li>
    {else}
      {* regular item *}
      {$liclass=''}
      {$aclass=''}
      {if $node->current}
        {$liclass='currentpage'}
        {$aclass='currentpage'}
      {elseif $node->parent}
        {$liclass='activeparent'}
        {$aclass='activeparent'}
      {/if}
      <li{if $liclass != ''} class="{$liclass}"{/if}>
        <a{if $aclass !=''} class="{$aclass}"{/if} href="{$node->url}"{if $node->target ne ""} target="{$node->target}"{/if}>{$node->menutext}</a>
        {if isset($node->children)}
          {include file=$smarty.template nodes=$node->children depth=$depth+1}
        {/if}
      </li>
    {/if}
  {/foreach}
</ul>
{/strip}{/if}