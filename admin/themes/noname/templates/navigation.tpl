{* navigation goes here *}
{if !isset($depth)}{assign var='depth' value=0}{/if}

<ul class="navdepth{$depth}">
{foreach from=$nav item='navitem'}
  <li><a href="{$navitem.url}" title="{$navitem.description}">{$navitem.title}</a>
  {if isset($navitem.children)}{include file=$smarty.template nav=$navitem.children depth=$depth+1}{/if}
  </li>
{/foreach}
</ul>
