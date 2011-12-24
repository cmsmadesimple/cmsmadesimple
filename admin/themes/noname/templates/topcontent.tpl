{* topcontent *}

{* do boomarks stuff *}
<div class="pagecontainer" style="width: 100%;">

  <div class="leftbookmarks" style="float:left; width: 40%;">
    <h2>{'bookmarks'|lang}</h2>
    {assign var='marks' value=$theme->get_bookmarks()}
    {if count($bookmarks)}
      <h3>{'user_created'|lang}</h3>
      <ul>
      {foreach from=$bookmarks item='mark'} 
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

  {* do a toplevel navigation list *}
  <div class="toplevelcontent" style="float: left; width: 58%">{strip}
  {foreach from=$nodes item='node'}
    {if $node.show_in_menu && $node.url && $node.title}
    <div class="topnavitem">
      <a href="{$node.url}"{if isset($node.target)} target="{$node.target}"{/if}{if $node.selected} class="selected"{/if}>{$node.title}</a>
      {if $node.description}<span class="description">{$node.description}</span>{/if}
    </div>
    {/if}
  {/foreach}
  {/strip}</div>

  {*<div style="clear:both;"></div>*}
</div>
