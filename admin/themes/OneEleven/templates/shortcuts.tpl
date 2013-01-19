{strip}
<div class="shortcuts">
	<ul class="cf">
		<li class="help">
		{if isset($module_help_url)}
			<a href="{$module_help_url}" title="{'module_help'|lang}">{'module_help'|lang}</a>
		{else}
			<a href="http://docs.cmsmadesimple.org/" rel="external" title="{'documentation'|lang}">{'documentation'|lang}</a>	
		{/if}
		</li>
                {if isset($myaccount)}
		<li class="settings">
			<a href="myaccount.php?{$secureparam}" title="{'myaccount'|lang}">{'myaccount'|lang}</a>
		</li>
                {/if}
		{if isset($marks)}
		<li class="favorites open">
			<a href="listbookmarks.php?{$secureparam}" title="{'bookmarks'|lang}">{'bookmarks'|lang}</a>
		</li>
		{/if}
		<li class="view-site">
			<a href="{root_url}/index.php" rel="external" target="_blank" title="{'viewsite'|lang}">{'viewsite'|lang}</a>
		</li>		
		<li class="logout">
			<a href="logout.php?{$secureparam}" title="{'logout'|lang}">{'logout'|lang}</a>
		</li>
	</ul>
</div>
{if isset($marks)}
<div class="dialog invisible" role="dialog" title="{'bookmarks'|lang}">
    {if count($marks)}
      <h3>{'user_created'|lang}</h3>
      <ul>
      {foreach from=$marks item='mark'}
         <li><a{if $mark->bookmark_id > 0} class="bookmark"{/if} href="{$mark->url}" title="{$mark->title}">{$mark->title}</a></li>
      {/foreach}
      </ul>
    {/if}
    <h3>{'help'|lang}</h3>
    <ul>
      <li><a rel="external" class="external" href="http://docs.cmsmadesimple.org" title="{'documentation'|lang}">{'documentation'|lang}</a></li>
      <li><a rel="external" class="external" href="http://forum.cmsmadesimple.org" title="{'forums'|lang}">{'forums'|lang}</a></li>
	  <li><a rel="external" class="external" href="http://cmsmadesimple.org/main/support/IRC">{'irc'|lang}</a></li>
    </ul>
</div> 
{/if}
{/strip}