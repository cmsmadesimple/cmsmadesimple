{* CSS classes used in this template:
.activeparent - The top level parent when a child is the active/current page
li.active0n h3 - n is the depth/level of the node. To style the active page for each level separately. The active page is not clickable.
.clearfix - Used for the unclickable h3 to use the entire width of the li, just like the anchors. See the Tools stylesheet in the default CMSMS installation.
li.sectionheader h3 - To style section header
li.separator - To style the ruler for the separator *} 

{if $count > 0}
  {* there are nodes available for display *}
  <ul>
  {foreach from=$nodelist item=node}
    {if $node->depth > $node->prevdepth}
      {* depth of menu has increased *}
      {repeat string="<ul>" times=$node->depth-$node->prevdepth}
    {elseif $node->depth < $node->prevdepth}
      {* depth of menu has decreased *}
      {repeat string="</li></ul>" times=$node->prevdepth-$node->depth}
    {/if}

     {if $node->parent == true or ($node->current == true and $node->haschildren == true)}
      {* display the active parent with children *}
      <li class="menuactive menuparent"><a class="menuactive menuparent" href="{$node->url}"{if $node->accesskey != ''} accesskey="{$node->accesskey}"{/if}{if $node->tabindex != ''} tabindex="{$node->tabindex}"{/if}{if $node->titleattribute != ''} title="{$node->titleattribute}"{/if}><dfn>{$node->hierarchy}:</dfn><span>{$node->menutext}</span></a></li>

     {elseif $node->haschildren == true}
      {* display the active parent *}
      <li class="parent"><a class="parent" href="{$node->url}"{if $node->accesskey != ''} accesskey="{$node->accesskey}"{/if}{if $node->tabindex != ''} tabindex="{$node->tabindex}"{/if}{if $node->titleattribute != ''} title="{$node->titleattribute}"{/if}><dfn>{$node->hierarchy}:</dfn><span>{$node->menutext}</span></a></li>

     {elseif $node->current == true}
      {* display the current page differently *}
      <li class="currentpage"><h3><dfn>Current page is {$node->hierarchy}:</dfn><span>{$node->menutext}</span></h3></li>

     {elseif $node->type == 'sectionheader'}
      {* display section headers *}
      <li class="sectionheader"><span>{$node->menutext}</span></li>

     {elseif $node->type == 'separator'}
      {* display separators *}
      <li class="separator" style="list-style-type: none;"> <hr />

     {else}
      {* normal menu items *}
      <li><a href="{$node->url}"{if $node->accesskey != ''} accesskey="{$node->accesskey}"{/if}{if $node->tabindex != ''} tabindex="{$node->tabindex}"{/if}{if $node->titleattribute != ''} title="{$node->titleattribute}"{/if}{if $node->target != ''} target="{$node->target}"{/if}><dfn>{$node->hierarchy}:</dfn><span>{$node->menutext}</span></a></li>

    {/if}

  {/foreach}

  {* close any remaining unordered lists *}
  {repeat string="</ul>" times=$node->depth-1}
  </ul>
{/if}
