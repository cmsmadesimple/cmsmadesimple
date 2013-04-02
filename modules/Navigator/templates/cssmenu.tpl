{* cssmenu *}
{* this template uses recursion, but not a smarty function. *}
{* 
  variables:
  node: contains the current node.
  aclass: is used to build a string containing class names given to the a tag if one is used
  liclass: is used to build a string containing class names given to the li tag.
*}
{if !isset($depth)}{$depth=0}{/if}

{if $depth == 0}
<div id="menuwrapper">
<ul id="primary-nav">
{else}
<ul class="unli">
{/if}

{$depth=$depth+1}
{foreach $nodes as $node}
  {* setup classes for the anchor and list item *}
  {assign var='liclass' value=''}
  {assign var='aclass' value=''}

  {* the first child gets a special class *}
  {if $node@first && $node@total > 1}{assign var='liclass' value=$liclass|cat:' first_child'}{/if}

  {* the last child gets a special class *}
  {if $node@last && $node@total > 1}{assign var='liclass' value=$liclass|cat:' last_child'}{/if}

  {if $node->current}
    {* this is the current page *}
    {assign var='liclass' value=$liclass|cat:' menuactive'}
    {assign var='aclass' value=$liclass|cat:' menuactive'}
  {/if}
  {if $node->has_children}
    {* this is the current page *}
    {assign var='liclass' value=$liclass|cat:' menuparent'}
    {assign var='aclass' value=$aclass|cat:' menuparent'}
  {/if}
  {if $node->parent}
    {* this is a parent of the current page *}
    {assign var='liclass' value=$liclass|cat:' parent'}
    {assign var='aclass' value=$aclass|cat:' parent'}
  {/if}

  {* build the menu item node *}
  {if $node->type == 'sectionheader'}
    <li class='{$liclass}'><a{if $aclass != ''} class="{$aclass}"{/if}><span class="sectionheader">{$node->menutext}</span></a>
      {if isset($node->children)}
        {include file=$smarty.template nodes=$node->children}
      {/if}
    </li>
  {else if $node->type == 'separator'}
    <li style="list-style-type: none;"><hr class="menu_separator"/></li>
  {else}
    {* regular item *}{strip}
    <li class="menuitem{$liclass}">
      <a{if $aclass != ''} class="{$aclass}"{/if} href="{$node->url}"{if $node->target ne ""} target="{$node->target}"{/if}><span>{$node->menutext}</span></a>
      {if isset($node->children)}
        {include file=$smarty.template nodes=$node->children}
      {/if}
    </li>{/strip}
  {/if}
{/foreach}
{$depth=$depth-1}
</ul>

{if $depth == 0}
<div class="clearb"></div>
</div>{* menuwrapper *}
{/if}