{strip}

<!-- .news-summary wrapper -->
<article class='news-summary'>
<span class='heading'><span>News</span></span>
        <ul class='category-list cf'>
        {foreach from=$cats item='node'}
        {if $node.depth > $node.prevdepth}
            {repeat string='<ul>' times=$node.depth-$node.prevdepth}
        {elseif $node.depth < $node.prevdepth}
            {repeat string='</li></ul>' times=$node.prevdepth-$node.depth}
            </li>
            {elseif $node.index > 0}</li>
            {/if}
            <li{if $node.index == 0} class='first'{/if}>
        {if $node.count > 0}
                <a href='{$node.url}'>{$node.news_category_name}</a>{else}<span>{$node.news_category_name} </span>{/if}
        {/foreach}
        {repeat string='</li></ul>' times=$node.depth-1}</li>
        </ul>
    {foreach from=$items item='entry'}
    <!-- .news-article (wrapping each article) -->
    <section class='news-article'>
        <header>
            <h2><a href='{$entry->moreurl}' title='{$entry->title|cms_escape:htmlall}'>{$entry->title|cms_escape}</a></h2>
            <div class='meta cf'>
                <time class='date' datetime='{$entry->postdate|date_format:'%Y-%m-%d'}'>
                    <span class='day'> {$entry->postdate|date_format:'%d'} </span>
                    <span class='month'> {$entry->postdate|date_format:'%b'} </span>
                </time>
                <span class='author'> {$author_label} {$entry->author} </span>
                <span class='category'> {$category_label} {$entry->category}</span>
            </div>
        </header>
        {if $entry->summary}
            <p>{eval var=$entry->summary|strip_tags}</p>
            <span class='more'>{$entry->morelink} →</span>
        {else if $entry->content}
            <p>{eval var=$entry->content|strip_tags}</p>
        {/if}
    </section>
    <!-- .news-article //-->
    {/foreach}
        <!-- news pagination -->
        {if $pagecount > 1}
        <span class='paginate'>
            {if $pagenumber > 1}
                {$firstpage} {$prevpage}
            {/if}
                {$pagetext} {$pagenumber} {$oftext} {$pagecount}
            {if $pagenumber < $pagecount}
                {$nextpage} {$lastpage}
            {/if}
        </span>
        {/if}
</article>
<!-- .news-summary //-->

{/strip}