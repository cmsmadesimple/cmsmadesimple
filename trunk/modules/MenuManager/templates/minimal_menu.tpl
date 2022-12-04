{* CSS classes used in this template:
.currentpage - The active/current page
.bullet_sectionheader - To style section header
hr.separator - To style the ruler for the separator *} 
{if $count > 0}
<ul class="clearfix">
{foreach from=$nodelist item=node}
{if $node->depth > $node->prevdepth}
{repeat string="<ul>" times=$node->depth-$node->prevdepth}
{elseif $node->depth < $node->prevdepth}
{repeat string="</li></ul>" times=$node->prevdepth-$node->depth}
</li>
{elseif $node->index > 0}</li>
{/if}

{if $node->current == true}
<li><a href="{$node->url}" class="currentpage"{if $node->target ne ""} target="{$node->target}"{/if}> {$node->menutext} </a>

{elseif $node->parent == true && $node->depth == 1 and $node->type != 'sectionheader' and $node->type != 'separator'}
<li class="activeparent"> <a href="{$node->url}" class="activeparent"{if $node->target ne ""} target="{$node->target}"{/if}> {$node->menutext} </a>

{elseif $node->type == 'sectionheader'}
<li class="sectionheader">{$node->menutext}

{elseif $node->type == 'separator'}
<li style="list-style-type: none;"> <hr class="separator" />

{else}
<li><a href="{$node->url}"{if $node->target ne ""} target="{$node->target}"{/if}> {$node->menutext} </a>

{/if}

{/foreach}

{repeat string="</li></ul>" times=$node->depth-1}</li>
</ul>
{/if}
