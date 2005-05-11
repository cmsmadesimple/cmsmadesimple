<pre>
Categories: {$entry->category}
      Date: {$entry->postdate|date_format}
     Title: {$entry->title}
</pre>

<p>
{if $entry->summary}

{$entry->summary}
<br />
<br />

{/if}

{$entry->content}
</p>
