<div id="logocontainer"><img src="themes/default/images/logoCMS.png" alt="{tr}CMS Made Simple Admin Panel{/tr}" title="{tr}CMS Made Simple Admin Panel{/tr}" /><div class="logotext">{tr}CMS Made Simple Admin Panel{/tr}

<br />{tr}Welcome to{/tr}:  {$its_me} 


</div></div>


{if $root_node->has_children()}

  <div class="topmenucontainer">
    <ul id="nav">

      {foreach from=$root_node->get_children() item=node name=node}
        <li><a href="{$node->url|escape:'html'}" class="{if $node->selected} selected{/if}"{if $node->target ne ''} rel="external"{/if}>{lang string=$node->title}</a>
        
          {if $node->has_children()}
            <ul>
              {foreach from=$node->get_children() item=subnode name=subnode}
                {if $subnode->show_in_menu}
                  <li><a href="{$subnode->url|escape:'html'}" class="{if $subnode->selected} selected{/if}{if $subnode->first_module} firstmodule{elseif $subnode->module} module{/if}"{if $subnode->target ne ''} rel="external"{/if}>{lang string=$subnode->title}</a></li>
                {/if}
              {/foreach}
            </ul>
          {/if}
          
        </li>
      
      {/foreach}
  
    </ul>
    <!-- ICONS-->
    <div id="nav-icons_all">
    <ul id="nav-icons">
	<li class="viewsite-icon"><a  rel="external" title="{tr}View Site{/tr}"  href="../">{tr}View Site{/tr}</a></li>
	<li class="logout-icon"><a  title="{tr}Logout{/tr}"  href="logout.php">{tr}Logout{/tr}</a></li>
	</ul>
    </div><!--end nav-icons_all-->
        
    <div class="clearb"></div>
  </div>
  
  <div class="breadcrumbs">
      {if count($breadcrumbs) gt 0}
        {foreach from=$breadcrumbs item=breadcrumb name=breadcrumb}
          {if $breadcrumb.url ne ''}
            <a class="breadcrumbs" href="{$breadcrumb.url|escape:'html'}">{lang string=$breadcrumb.title}</a>
          {else}
            {lang string=$breadcrumb.title}
          {/if}
          {if !$smarty.foreach.breadcrumb.last}
            &#187;
          {/if}
        {/foreach}
      {/if}
  </div>
  
{/if}
