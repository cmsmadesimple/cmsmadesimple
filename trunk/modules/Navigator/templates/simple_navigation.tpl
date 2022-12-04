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
    {assign var='liclass' value='menudepth'|cat:$depth}
    {assign var='aclass' value=''}

    {* the first child gets a special class *}
    {if $node@first && $node@total > 1}{assign var='liclass' value=$liclass|cat:' first_child'}{/if}

    {* the last child gets a special class *}
    {if $node@last && $node@total > 1}{assign var='liclass' value=$liclass|cat:' last_child'}{/if}

    {if $node->current}
      {* this is the current page *}
      {assign var='liclass' value=$liclass|cat:' menuactive'}
      {assign var='aclass' value=$aclass|cat:' menuactive'}
    {/if}

    {if $node->parent}
      {* this is a parent of the current page *}
      {assign var='liclass' value=$liclass|cat:' menuactive menuparent'}
      {assign var='aclass' value=$aclass|cat:' menuactive menuparent'}
    {/if}

    {if $node->children}
      {assign var='liclass' value=$liclass|cat:' parent'}
      {assign var='aclass' value=$aclass|cat:' parent'}
    {/if}

    {* build the menu item node *}
    {if $node->type == 'sectionheader'}
      <li class='sectionheader {$liclass}'><span>{$node->menutext}</span>
        {if isset($node->children)}
          {Nav_menu data=$node->children depth=$depth+1}
        {/if}
      </li>
    {else if $node->type == 'separator'}
      <li class='separator {$liclass}'><hr class='separator'/></li>
    {else}
      {* regular item *}
      <li class="{$liclass}">
        <a class="{$aclass}" href="{$node->url}"{if $node->target ne ""} target="{$node->target}"{/if}><span>{$node->menutext}</span></a>
        {if isset($node->children)}
          {Nav_menu data=$node->children depth=$depth+1}
        {/if}
      </li>
    {/if}
  {/foreach}
</ul>
{/strip}{/function}

{if isset($nodes)}
{Nav_menu data=$nodes depth=0}
{/if}
