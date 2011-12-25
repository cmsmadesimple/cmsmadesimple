{strip}

<div class="shortcuts">
	<ul class="cf">
		{if isset($module_help_url) or isset($wiki_url)}
		<li class="help">
			{if isset($module_help_url)}
			<a href="{$module_help_url}" title="{'module_help'|lang}">{'module_help'|lang}</a>
			{/if}
			{if isset($wiki_url)}
			<a href="{$wiki_url}" target="_blank" title="{'help'|lang} - {'new_window'|lang}">{'help'|lang}</a>
			{/if}
		</li>
		{/if}
		<li class="settings">
			<a href="editprefs.php?{$secureparam}" title="{'userprefs'|lang}">{'userprefs'|lang}</a>
		</li>
		<li class="favorites open">
			<a href="listbookmarks.php?{$secureparam}" title="{'bookmarks'|lang}">{'bookmarks'|lang}</a>
		</li>
		<li class="logout">
			<a href="logout.php?{$secureparam}" title="{'logout'|lang}">{'logout'|lang}</a>
		</li>
		<li class="view-site">
			<a href="{root_url}" rel="external" target="_blank" title="{'viewsite'|lang}">{'viewsite'|lang}</a>
		</li>
	</ul>
</div>
<div class="dialog" role="dialog" title="{'bookmarks'|lang}">
    <h2>{'bookmarks'|lang}</h2>
    {assign var='marks' value=$theme->get_bookmarks()}
    {if count($marks)}
      <h3>{'user_created'|lang}</h3>
      <ul>
      {foreach from=$marks item='mark'} 
         <li><a href="{$mark->url}" title="{$mark->title}">{$mark->title}</a></li>
      {/foreach}
      </ul>
    {/if}
    <h3>{'help'|lang}</h3>
    <ul>
      <li><a ref="external" href="http://forum.cmsmadesimple.org" title="{'forums'|lang}">{'forums'|lang}</a></li>
      <li><a ref="external" href="http://wiki.cmsmadesimple.org" title="{'wiki'|lang}">{'wiki'|lang}</a></li>
      <li><a rel="external" href="http://cmsmadesimple.org/main/support/IRC">{'irc'|lang}</a></li>
      <li><a rel="external" href="http://wiki.cmsmadesimple.org/index.php/User_Handbook/Admin_Panel/Extensions/Modules">{'module_help'|lang}</a></li>
    </ul>
</div> 
{/strip}
