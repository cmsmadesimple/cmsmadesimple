<h3 id="NewsPostDetailTitle">{$entry->title}</h3>

<hr id="NewsPostDetailHorizRule">

{if $entry->category}

<div id="NewsPostDetailCategory">
{$entry->category}
</div>

{/if}

{if $entry->postdate}

<div id="NewsPostDetailDate">
Posted: {$entry->postdate|date_format}
</div>

{/if}

{if $entry->summary}

<div id="NewsPostDetailSummary">
<strong>
{$entry->summary}
</strong>
</div>

{/if}

<div id="NewsPostDetailContent">
{$entry->content}
</div>

<div>
{$entry->printlink}
</div>
