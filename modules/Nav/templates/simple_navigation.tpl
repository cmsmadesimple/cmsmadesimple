{* simple navigation *}
{* note, function can only be defined once *}
{function name=Nav_menu depth=1}
<ul>
  {foreach from=$data item='node' name='nodelist'}
    {assign var='liclass' value=' depth'|cat:$depth}
    {assign var='aclass' value=''}

    {if $smarty.foreach.nodelist.first}{assign var='liclass' value=$liclass|cat:' first_child'}{/if}
    {if $smarty.foreach.nodelist.last}{assign var='liclass' value=$liclass|cat:' last_child'}{/if}
    {if $node->current}
       {assign var='liclass' value=$liclass|cat:' currentpage'}
       {assign var='aclass' value=$liclass|cat:' currentpage'}
    {/if}
    {if $node->parent}
      {assign var='liclass' value=$liclass|cat:' activeparent'}
      {assign var='aclass' value=$aclass|cat:' activeparent'}
    {/if}

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
{/function}

{if isset($nodes)}
<h3>Nodes:</h3>
<div id="menu">
  {Nav_menu data=$nodes depth=0}
</div>
{/if}