<!-- Start News Display Template -->
{foreach from=$items item=entry}

<p>

{$entry->titlelink}

{if $entry->summary}

<br />{$entry->summary}
<br />[{$morelink}]

{else if $enty->content}

<br />{$entry->content}

{/if}

</p>

{/foreach}
<!-- End News Display Template -->
