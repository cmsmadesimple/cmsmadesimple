<!-- Start News Display Template -->
{foreach from=$items item=entry}

<p>

{$entry->titlelink}

<br />{$entry->category}

{if $entry->postdate}

<br />{$entry->postdate|date_format}

{/if}

{if $entry->summary}

<br />{$entry->summary}
<br />[{$entry->morelink}]

{else if $entry->content}

<br />{$entry->content}

{/if}

</p>

{/foreach}
<!-- End News Display Template -->
