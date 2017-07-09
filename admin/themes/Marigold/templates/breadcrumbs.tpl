{if count($items)}
{strip}
<div class="breadcrumbs" itemscope itemtype="http://data-vocabulary.org/Breadcrumb">
	<ul class="cf">
		<li class="home"><a href="{$config.admin_url}" title="{'home'|lang}">{'home'|lang}</a></li>
	{foreach from=$items item='one' name='breadcrumb'}
		<li{if $smarty.foreach.breadcrumb.last && !$smarty.foreach.breadcrumb.first} class="current"{/if}{if $smarty.foreach.breadcrumb.first} class="first" itemprop="parent"{else} itemprop="child"{/if}>
			{if !empty($one.url)}<a href="{$one.url}" title="{if !empty($one.description)}{$one.description}{else}{$one.title}{/if}" itemprop="url">{$one.title}</a>{else}{$one.title}{/if}
		</li>
	{/foreach}
	</ul>
</div>
{/strip}
{/if}