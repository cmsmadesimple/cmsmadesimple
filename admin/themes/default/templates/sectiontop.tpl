{*  Bookmarks BOX  *}

{if $show_admin_shortcuts eq '1'}
<div class="itemmenucontainer shortcuts" style="float:left;">
		<div class="itemoverflow">
			<h2>{tr}bookmarks{/tr}</h2>
			<p><a href="listbookmarks.php">{tr}managebookmarks{/tr}</a></p>
			{if count($marks) gt 0}  
                <h3 style="margin:0">{tr}user_created{/tr}</h3>
				<ul style="margin:0">
               {foreach from=$marks item=mark name=mark}
					<li><a href="{$mark->url}">{$mark->title}</a></li>
                 {/foreach}
				</ul>
			{/if}
			<h3 style="margin:0;">{tr}help{/tr}</h3>
			<ul style="margin:0;">
			<li><a href="http://forum.cmsmadesimple.org/">{tr}forums{/tr}</a></li>
			<li><a href="http://wiki.cmsmadesimple.org/">{tr}wiki{/tr}</a></li>
			<li><a href="http://cmsmadesimple.org/main/support/IRC">{tr}irc{/tr}</a></li>
			<li><a href="http://wiki.cmsmadesimple.org/index.php/User_Handbook/Admin_Panel/Extensions/Modules">{tr}module_help{/tr}</a></li>
			</ul>
			</div>
			</div>
{/if}
{* end *} 
 
{if $top_node->has_children()}
<div class="MainMenu">

  {foreach from=$top_node->get_children() item=node}

    {if $node->show_in_menu}

      {if $node->first_module}
        <hr width="90%"/>
      {/if}

      <div class="itemmenucontainer">
        <div class="itemoverflow">
          <p class="itemicon">
            {if $node->icon_url != ''}
              <a href="listcontent.php">
                <img src="{$node->icon_url}" class="itemicon" alt="{$node->title}" title="{$node->title}" {if $node->target ne ''}rel="external"{/if} />
              </a>
            {/if}
          </p>
          <p class="itemtext">
            <a class="itemlink" href="{$node->url}">{$node->title}</a><br />{$node->description}<br />
          </p>
        </div>
      </div>

    {/if}

  {/foreach}

  </div>

{/if}
