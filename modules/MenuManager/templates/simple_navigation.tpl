{* CSS classes used in this template:
.activeparent - The top level parent when a child is the active/current page
li.active0n h3 - n is the depth/level of the node. To style the active page for each level separately. The active page is not clickable.
.clearfix - Used for the unclickable h3 to use the entire width of the li, just like the anchors. See the Tools stylesheet in the default CMSMS installation.
li.sectionheader h3 - To style section header
li.separator - To style the ruler for the separator *} 

{assign var='number_of_levels' value=10000}
{if isset($menuparams.number_of_levels)}
  {assign var='number_of_levels' value=$menuparams.number_of_levels}
{/if}

{if $count > 0}
<ul>
{foreach from=$nodelist item=node}
{if $node->depth > $node->prevdepth}
{repeat string="<ul>" times=$node->depth-$node->prevdepth}
{elseif $node->depth < $node->prevdepth}
{repeat string="</li></ul>" times=$node->prevdepth-$node->depth}
</li>
{elseif $node->index > 0}</li>
{/if}

{if $node->parent == true or $node->current == true}
  {assign var='classes' value='menuactive'}
  {if $node->parent == true}
    {assign var='classes' value='menuactive menuparent'}
  {/if}
  {if $node->children_exist == true and $node->depth < $number_of_levels}
    {assign var='classes' value=$classes|cat:' parent'}
  {/if}
  <li class="{$classes}"><a class="{$classes}" href="{$node->url}"><span>{$node->menutext}</span></a>

{elseif $node->children_exist == true and $node->depth < $number_of_levels and $node->type != 'sectionheader' and $node->type != 'separator'}
<li class="parent"><a class="parent" href="{$node->url}"><span>{$node->menutext}</span></a>

{elseif $node->current == true}
<li class="currentpage"><h3><span>{$node->menutext}</span></h3>

{elseif $node->type == 'sectionheader'}
<li class="sectionheader"><span>{$node->menutext}</span>

{elseif $node->type == 'separator'}
<li class="separator" style="list-style-type: none;"> <hr />

{else}
<li><a href="{$node->url}"><span>{$node->menutext}</span></a>

{/if}

{/foreach}
{repeat string="</li></ul>" times=$node->depth-1}</li>
</ul>
{/if}
