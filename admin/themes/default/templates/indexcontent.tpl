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

{if $root_node->has_children()}


  <div class="MainMenu">

  {foreach from=$root_node->get_children() item=node name=node}

    {if $node->show_in_menu and !$smarty.foreach.node.first} {* Ignore the main menu item *}

      <div class="itemmenucontainer">
        <div class="itemoverflow">
          <p class="itemicon">
            <a href="{$node->url}"><img src="{$node->icon_url}" class="itemicon" alt="{$node->title}" title="{$node->title}" {if $node->target ne ''}rel="external"{/if} /></a>
          </p>
          <p class="itemtext">
            <a class="itemlink" href="{$node->url}">{$node->title}</a>
          
            {if $node->description ne ''}
              <br />{$node->description}
            {/if}
          
            {if $node->has_children()}

              {foreach from=$node->get_children() item=subnode name=subnode}
                {if $subnode->show_in_menu}
                
                  {if $smarty.foreach.subnode.first} {* Only show the Subitems text after he hit the first visible one *}
                    <br /> {$subitems}:
                  {/if}

                  <a class="itemsublink" href="{$subnode->url}">{$subnode->title}</a>{if !$smarty.foreach.subnode.last}, {/if}
                {/if}
              {/foreach}

            {/if}

          </p>
        </div>
      </div>

    {/if}

  {/foreach}
  
  </div>

{/if}
