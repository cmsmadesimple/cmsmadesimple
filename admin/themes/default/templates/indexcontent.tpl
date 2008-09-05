{*  Bookmarks BOX  *}
 {$admin_bookmarks}
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
