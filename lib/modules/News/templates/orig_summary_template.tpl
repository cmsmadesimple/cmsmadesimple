<!-- Start News Display Template -->
{* This section shows a clickable list of your News categories. *}
<ul class="list1">
{foreach from=$cats item=node}
{if $node.depth > $node.prevdepth}
{repeat string="<ul>" times=$node.depth-$node.prevdepth}
{elseif $node.depth < $node.prevdepth}
{repeat string="</li></ul>" times=$node.prevdepth-$node.depth}
</li>
{elseif $node.index > 0}</li>
{/if}
<li{if $node.index == 0} class="firstnewscat"{/if}>
{if $node.count > 0}
	<a href="{$node.url}">{$node.news_category_name}</a>{else}<span>{$node.news_category_name} </span>{/if}
{/foreach}
{repeat string="</li></ul>" times=$node.depth-1}</li>
</ul>

{* this displays the category name if you're browsing by category *}
{if $category_name}
<h1>{$category_name}</h1>
{/if}

{* if you don't want category browsing on your summary page, remove this line and everything above it *}

{if $pagecount > 1}
  <p>
{if $pagenumber > 1}
{$firstpage}&nbsp;{$prevpage}&nbsp;
{/if}
{$pagetext}&nbsp;{$pagenumber}&nbsp;{$oftext}&nbsp;{$pagecount}
{if $pagenumber < $pagecount}
&nbsp;{$nextpage}&nbsp;{$lastpage}
{/if}
</p>
{/if}
{foreach from=$items item=entry}
<div class="NewsSummary">

{if $entry->postdate}
	<div class="NewsSummaryPostdate">
		{$entry->postdate|cms_date_format}
	</div>
{/if}

<div class="NewsSummaryLink">
<a href="{$entry->moreurl}" title="{$entry->title|cms_escape:htmlall}">{$entry->title|cms_escape}</a>
</div>

<div class="NewsSummaryCategory">
	{$category_label} {$entry->category}
</div>

{if $entry->author}
	<div class="NewsSummaryAuthor">
		{$author_label} {$entry->author}
	</div>
{/if}

{if $entry->summary}
        {* note, for security purposes, incase News articles can come from untrused sources, we do not pass the summary or content through smarty in the default templates *}
	<div class="NewsSummarySummary">
		{$entry->summary}
	</div>

	<div class="NewsSummaryMorelink">
		[{$entry->morelink}]
	</div>

{else if $entry->content}
        {* note, for security purposes, incase News articles can come from untrused sources, we do not pass the summary or content through smarty in the default templates *}
	<div class="NewsSummaryContent">
		{$entry->content}
	</div>
{/if}

{if isset($entry->extra)}
    <div class="NewsSummaryExtra">
        {$entry->extra}
	{* {cms_module module='Uploads' mode='simpleurl' upload_id=$entry->extravalue} *}
    </div>
{/if}
{if isset($entry->fields)}
  {foreach from=$entry->fields item='field'}
     <div class="NewsSummaryField">
        {if $field->type == 'file'}
          {if isset($field->value) && $field->value}
            <img src="{$entry->file_location}/{$field->value}"/>
          {/if}
        {elseif $field->type == 'linkedfile'}
          {* also assume it's an image... *}
          {if !empty($field->value)}
            <img src="{file_url file=$field->value}" alt="{$field->value}"/>
          {/if}
        {else}
          {$field->name}:&nbsp;{$field->value}
        {/if}
     </div>
  {/foreach}
{/if}

</div>
{/foreach}
<!-- End News Display Template -->
