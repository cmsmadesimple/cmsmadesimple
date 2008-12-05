{if $show_admin_shortcuts eq '1'} 

{* navt_bookmarks  *}
 
        <div class="navt_menu">
			<div id="navt_display" class="navt_show" onclick="change('navt_display', 'navt_hide', 'navt_show'); change('navt_container', 'invisible', 'visible');"></div>
			<div id="navt_container" class="invisible">
				<div id="navt_tabs">
					<div id="navt_bookmarks">{tr}Shortcuts{/tr}</div>
				</div>

				<div style="clear: both;"></div>
				<div id="navt_content">
					<div id="navt_bookmarks_c">
                    <a href="makebookmark.php?title={$admin_theme->title|escape:"url"}">Add Shortcut</a><br />
						
                    {if count($marks) gt 0}   
                     <a href="listbookmarks.php">Manage Shortcuts</a><br /><br />
                    {foreach from=$marks item=mark name=mark}
                    <a href="{$mark->url}">{$mark->title}</a><br />
                     {/foreach}
                   
                     {/if}
					</div>
				</div>
			</div>
			<div style="clear: both;"></div>
		</div><!--end navt_menu-->




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
			<li><a rel="external" href="http://forum.cmsmadesimple.org/">{tr}forums{/tr}</a></li>
			<li><a rel="external" href="http://wiki.cmsmadesimple.org/">{tr}wiki{/tr}</a></li>
			<li><a rel="external" href="http://cmsmadesimple.org/main/support/IRC">{tr}irc{/tr}</a></li>
			<li><a rel="external" href="http://wiki.cmsmadesimple.org/index.php/User_Handbook/Admin_Panel/Extensions/Modules">{tr}module_help{/tr}</a></li>
			</ul>
			</div>
			</div>
{/if}