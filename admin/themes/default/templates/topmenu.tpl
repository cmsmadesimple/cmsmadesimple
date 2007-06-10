{if $root_node->has_children()}

  <div>
    <p class="logocontainer">
      <img src="themes/default/images/logo.gif" alt="" />
      <span class="logotext">{$adminpaneltitle}</span>
    </p>
  </div>

  <div class="topmenucontainer">
    <ul id="nav">

      {foreach from=$root_node->get_children() item=node name=node}
      
        <li><a href="{$node->url|escape:'html'}" class="{if $node->selected} selected{/if}"{if $node->target ne ''} rel="external"{/if}>{$node->title}</a>
        
          {if $node->has_children()}
            <ul>
              {foreach from=$node->get_children() item=subnode name=subnode}
                {if $subnode->show_in_menu}
                  <li><a href="{$subnode->url|escape:'html'}" class="{if $subnode->selected} selected{/if}{if $subnode->first_module} firstmodule{elseif $subnode->module} module{/if}"{if $subnode->target ne ''} rel="external"{/if}>{$subnode->title}</a></li>
                {/if}
              {/foreach}
            </ul>
          {/if}
          
        </li>
      
      {/foreach}
  
    </ul>
    
    <div class="clearb"></div>
  </div>
  
  <div class="breadcrumbs">
    <p class="breadcrumbs">
      {if count($breadcrumbs) gt 0}
        &#187;
        {foreach from=$breadcrumbs item=breadcrumb name=breadcrumb}
          {if $breadcrumb->url ne ''}
            <a class="breadcrumbs" href="{$breadcrumb->url|escape:'html'}">{$breadcrumb->title}</a>
          {else}
            {$breadcrumb->title}
          {/if}
        {/foreach}
      {/if}
    </p>
  </div>
  
  <div class="hstippled">&nbsp;</div>
    
{/if}
