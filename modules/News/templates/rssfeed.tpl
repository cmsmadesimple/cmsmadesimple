<?xml version='1.0'?>
<rss version='2.0'
	xmlns:content="http://purl.org/rss/1.0/modules/content/">
	<channel>
		<title>CMS Made Simple News Feed</title>
		<link>{$root_url}</link>
		<description>Current News entries</description>
		{foreach from=$items item=entry}
		<item>
			<title>{$entry->title}</title>
			<pubDate>{$entry->gmdate}</pubDate>
			<category>{$entry->category}</category>
			<description>{$entry->strippedsummary}
			{$entry->strippedcontent}</description>
			<content:encoded><![CDATA[{$entry->summary}
			{$entry->content}]]></content:encoded>
		</item>
		{/foreach}
	</channel>
</rss> 
