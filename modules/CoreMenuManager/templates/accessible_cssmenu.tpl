{* CSS classes used in this template:
#menuwrapper - The id for the <div> that the menu is wrapped in. Sets the width, background etc. for the menu.
#primary-nav - The id for the <ul>
.menuparent - The class for each <li> that has children.
.menuactive - The class for each <li> that is active or is a parent (on any level) of a child that is active. *}
{if $count > 0}
<div id="menuwrapper">
<ul id="primary-nav">
{foreach from=$nodelist item=node}
{if $node->depth > $node->prevdepth}

	{repeat string="<ul>" times=$node->depth-$node->prevdepth}
{elseif $node->depth < $node->prevdepth}

	{repeat string="</li></ul>" times=$node->prevdepth-$node->depth}
	</li>
{elseif $node->index > 0}</li>
{/if}
{if $node->parent == true or ($node->current == true and $node->haschildren == true)}
	<li class="menuactive menuparent"><a class="menuactive menuparent" 
{elseif $node->current == true}
	<li class="menuactive"><a class="menuactive" 
{elseif $node->haschildren == true}
	<li class="menuparent"><a class="menuparent" 
{elseif $node->type == 'sectionheader'}
        <li class="sectionheader"><span>{$node->menutext}</span>
{elseif $node->type == 'separator'}
        <li style="list-style-type: none;"> <hr class="separator" />
{else}
	<li><a 
{/if}
{if $node->type != 'sectionheader' and $node->type != 'separator'}
href="{$node->url}" {if $node->accesskey != ''}accesskey="{$node->accesskey}" {/if}{if $node->tabindex != ''}tabindex="{$node->tabindex}" {/if}{if $node->titleattribute != ''}title="{$node->titleattribute}"{/if}{if $node->target ne ""} target="{$node->target}"{/if}><dfn>{$node->hierarchy}: </dfn><span>{$node->menutext}</span></a>
{elseif $node->type == 'sectionheader'}
><dfn>{$node->hierarchy}: </dfn><span>{$node->menutext}</span></a>
{/if}

{/foreach}

	{repeat string="</li></ul>" times=$node->depth-1}		</li>
	</ul>
<div class="clearb"></div>
</div>
{/if}
