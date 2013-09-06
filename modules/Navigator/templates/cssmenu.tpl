{* cssmenu *}
{* this template uses recursion, but not a smarty function. *}
{* 
  variables:
  node: contains the current node.
  aclass: is used to build a string containing class names given to the a tag if one is used
  liclass: is used to build a string containing class names given to the li tag.
*}
{if !isset($depth)}{$depth=0}{/if}
{strip}

{if $depth == 0}
<div id="menuwrapper">
<ul id="primary-nav">
{else}
<ul class="unli">
{/if}

{$depth=$depth+1}
{foreach $nodes as $node}
  {* setup classes for the anchor and list item *}
  {$liclass=[]}
  {$aclass=[]}

  {* the first child gets a special class *}
  {if $node@first && $node@total > 1}{$liclass[]='first_child'}{/if}

  {* the last child gets a special class *}
  {if $node@last && $node@total > 1}{$liclass[]='last_child'}{/if}

  {if $node->current}
    {* this is the current page *}
    {$liclass[]='menuactive'}
    {$aclass[]='menuactive'}
  {/if}
  {if $node->has_children}
    {* this is a parent page *}
    {$liclass[]='menuparent'}
    {$aclass[]='menuparent'}
  {/if}
  {if $node->parent}
    {* this is a parent of the current page *}
    {$liclass[]='menuactive'}
    {$aclass[]='menuactive'}
  {/if}

  {* build the menu item from the node *}
  {if $node->type == 'sectionheader'}
    <li class='{implode(' ',$liclass)}'><a{if count($aclass) > 0} class="{implode(' ',$aclass)}"{/if}><span class="sectionheader">{$node->menutext}</span></a>
      {if isset($node->children)}
        {include file=$smarty.template nodes=$node->children}
      {/if}
    </li>
  {else if $node->type == 'separator'}
    <li style="list-style-type: none;"><hr class="menu_separator"/></li>
  {else}
    {* regular item *}
    <li class="{implode(' ',$liclass)}">
      <a{if count($aclass) > 0} class="{implode(' ',$aclass)}"{/if} href="{$node->url}"{if $node->target ne ""} target="{$node->target}"{/if}><span>{$node->menutext}</span></a>
      {if isset($node->children)}
        {include file=$smarty.template nodes=$node->children}
      {/if}
    </li>
  {/if}
{/foreach}
{$depth=$depth-1}
</ul>

{if $depth == 0}
<div class="clearb"></div>
</div>{* menuwrapper *}
{/if}
{/strip}