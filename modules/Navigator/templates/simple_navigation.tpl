{* simple navigation *}
{* note, function can only be defined once *}
{* 
  variables:
  node: contains the current node.
  aclass: is used to build a string containing class names given to the a tag if one is used
  liclass: is used to build a string containing class names given to the li tag.
*}

{function name=Nav_menu depth=1}{strip}
<ul>
  {foreach $data as $node}
    {* setup classes for the anchor and list item *}
    {assign var='liclass' value=' depth'|cat:$depth}
    {assign var='aclass' value=''}

    {* the first child gets a special class *}
    {if $node@first && $node@total > 1}{assign var='liclass' value=$liclass|cat:' first_child'}{/if}

    {* the last child gets a special class *}
    {if $node@last && $node@total > 1}{assign var='liclass' value=$liclass|cat:' last_child'}{/if}

    {if $node->current}
      {* this is the current page *}
      {assign var='liclass' value=$liclass|cat:' currentpage'}
      {assign var='aclass' value=$liclass|cat:' currentpage'}
    {/if}
    {if $node->parent}
      {* this is a parent of the current page *}
      {assign var='liclass' value=$liclass|cat:' activeparent'}
      {assign var='aclass' value=$aclass|cat:' activeparent'}
    {/if}

    {* build the menu item node *}
    {if $node->type == 'sectionheader'}
      <li class='sectionheader {$liclass}'>{$node->menutext}
        {if isset($node->children)}
          {Nav_menu data=$node->children depth=$depth+1}
        {/if}
      </li>
    {else if $node->type == 'separator'}
      <li class='separator {$liclass}'><hr class='separator'/></li>
    {else}
      {* regular item *}
      <li class="menuitem{$liclass}'>
        <a class="{$aclass}" href="{$node->url}"{if $node->target ne ""} target="{$node->target}"{/if}>{$node->menutext}</a>
        {if isset($node->children)}
          {Nav_menu data=$node->children depth=$depth+1}
        {/if}
      </li>
    {/if}
  {/foreach}
</ul>
{/strip}{/function}

{if isset($nodes)}
<h3>Nodes:</h3>
<div id="menu">
  {Nav_menu data=$nodes depth=0}
</div>
{/if}