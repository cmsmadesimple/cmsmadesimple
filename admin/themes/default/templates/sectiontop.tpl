{foreach from=$topnode->get_children() item=node}

  {if $node->show_in_menu}

    {if $node->first_module}
      <hr width="90%"/>
    {/if}

    <div class="itemmenucontainer">
      <div class="itemoverflow">
        <p class="itemicon">
          {if $node->icon_url != ''}
            <a href="listcontent.php">
              <img src="{$node->icon_url}" class="itemicon" alt="{$node->title}" title="{$node->title}" />
            </a>
          {/if}
        </p>
        <p class="itemtext">
          <a class="itemlink" href="listcontent.php">{$node->title}</a><br />{$node->description}<br />
        </p>
      </div>
    </div>

  {/if}

{/foreach}
