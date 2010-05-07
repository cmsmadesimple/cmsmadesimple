<h3>{$searchresultsfor} &quot;{$phrase}&quot;</h3>
{if $itemcount > 0}
<ul>
  {foreach from=$results item=entry}
  <li>{$entry->title} - <a href="{$entry->url}">{$entry->urltxt}</a> ({$entry->weight}%)</li>
  {* 
     You can also instantiate custom behaviour on a module by module basis by looking at
     the $entry->module and $entry->modulerecord fields in $entry 
      ie: {if $entry->module == 'News'}{News action='detail' article_id=$entry->modulerecord detailpage='News'} 
  *}
  {/foreach}
</ul>

<p>{$timetaken}: {$timetook}</p>
{else}
  <p><strong>{$noresultsfound}</strong></p>
{/if}
