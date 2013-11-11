<table class="pagetable">
  <thead>
    <tr>
      <th>{$mod->Lang('nametext')}</th>
      <th>{$mod->Lang('vertext')}</th>
      <th>{$mod->Lang('status')}</th>
      <th class="pageicon">{$mod->Lang('active')}</th>
      <th class="pageicon">{$mod->Lang('helptxt')}</th>
      <th class="pageicon">{$mod->Lang('abouttxt')}</th>
      {if $allow_export}<th class="pageicon">{$mod->Lang('export')}</th>{/if}
    </tr>
  </thead>
  <tbody>
    {foreach $module_info as $item}
    {cycle values="row1,row2" assign='rowclass'}
    <tr class="{$rowclass}" {if !$item.ver_compatible}style="background-color: yellow;"{/if}>
      <td>{$item.name}</td>
      <td>{$item.installed_version}</td>
      <td>
          {$tmp='status_'|cat:$item.status}<span title="{$mod->Lang($tmp)}">{$mod->Lang($item.status)}</span>
          {if isset($item.dependants)}<br/>{$mod->Lang('has_dependants')}: (<strong>{implode(',',$item.dependants)}</strong>){/if}
      </td>
      <td>
           {if !isset($item.dependants) && $item.can_deactivate}
             {if $item.active}
               <a href="{cms_action_url action='local_active' mod=$item.name state=0}" title="{$mod->Lang('toggle_inactive')}">{admin_icon icon='true.gif'}</a>
             {else}
               <a href="{cms_action_url action='local_active' mod=$item.name state=1}" title="{$mod->Lang('toggle_active')}">{admin_icon icon='false.gif'}</a>
             {/if}
           {/if}
      </td>
      <td><a href="{cms_action_url action='local_help' mod=$item.name}" title="{$mod->Lang('title_modulehelp')}">{$mod->Lang('helptxt')}</a></td>
      <td><a href="{cms_action_url action='local_about' mod=$item.name}" title="{$mod->Lang('title_moduleabout')}">{$mod->Lang('abouttxt')}</a></td>
      {if $allow_export}<td>
        {if $item.active}
          <a href="{cms_action_url action='local_export' mod=$item.name}" title="{$mod->Lang('title_moduleexport')}">{admin_icon icon='xml_rss.gif'}</a>
        {/if}
      </td>{/if}
    </tr>
    {/foreach}
  </tbody>  
</table>
