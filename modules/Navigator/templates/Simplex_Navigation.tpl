{strip}

{function do_class}
{if count($classes) > 0} class="{implode(' ',$classes)}"{/if}
{/function}

{function name=Simplex_menu depth=1}{strip}
<ul{if isset($ulclass) && $ulclass != ''} class="{$ulclass}"{/if}>{$ulclass=''}
  {foreach $data as $node}
    {* setup classes for the anchor and list item *}
    {$liclass=[]}
    {$aclass=[]}

    {if $node->current || $node->parent}
      {* this is the current page *}
      {$liclass[]='current'}
      {$aclass[]='current'}
    {/if}

    {if $node->children_exist}
      {$liclass[]='parent'}
    {/if}

    {* build the menu item node *}
    {if $node->type == 'sectionheader'}
      {$liclass[]='sectionheader'}
      <li{do_class classes=$liclass}><span>{$node->menutext}</span>
        {if isset($node->children)}
          {Simplex_menu data=$node->children depth=$depth+1}
        {/if}
      </li>
    {else if $node->type == 'separator'}
      {$liclass[]='separator'}
      <li{do_class classes=$liclass}'><hr class='separator'/></li>
    {else}
      {* regular item *}
      <li{do_class classes=$liclass}>
        <a{do_class classes=$aclass} href="{$node->url}"{if $node->target ne ""} target="{$node->target}"{/if}><span>{$node->menutext}</span></a>
        {if isset($node->children)}
          {Simplex_menu data=$node->children depth=$depth+1}
        {/if}
      </li>
    {/if}
  {/foreach}
</ul>
{/strip}{/function}

{if isset($nodes)}
{Simplex_menu data=$nodes depth=0 ulclass='cf'}
{/if}

{/strip}