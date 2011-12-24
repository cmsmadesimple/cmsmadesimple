{* display breadcrumbs *}
{if count($items)}{strip}
<p class="breadcrumbs">{'breadcrumbs'|lang}:&nbsp;
  {foreach from=$items item='one' name='breadcrumb'}
    <a href="{$one.url}" title="{$one.description}">{$one.title}</a>
    {if !$smarty.foreach.breadcrumb.last}&nbsp;&raquo;&nbsp;{/if}
  {/foreach}
</p>
{/strip}{/if}