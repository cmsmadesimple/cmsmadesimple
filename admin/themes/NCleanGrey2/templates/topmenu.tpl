<div id="logocontainer"><img src="themes/NCleanGrey2/images/logoCMS.png" alt="{$adminpaneltitle}" title="{$adminpaneltitle}" /><div class="logotext">{$adminpaneltitle}</div></div>


{if $root_node->has_children()}

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
      {if count($breadcrumbs) gt 0}
        {foreach from=$breadcrumbs item=breadcrumb name=breadcrumb}
          {if $breadcrumb.url ne ''}
            <a class="breadcrumbs" href="{$breadcrumb.url|escape:'html'}">{$breadcrumb.title}</a>
          {else}
            {$breadcrumb.title}
          {/if}
          {if !$smarty.foreach.breadcrumb.last}
            &#187;
          {/if}
        {/foreach}
      {/if}
  </div>
  
{/if}
