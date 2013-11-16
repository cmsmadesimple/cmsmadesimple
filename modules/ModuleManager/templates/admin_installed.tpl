<table class="pagetable">
  <thead>
    <tr>
      <th>{$mod->Lang('nametext')}</th>
      <th><span title="{$mod->Lang('title_moduleversion')}">{$mod->Lang('vertext')}</span></th>
      <th><span title="{$mod->Lang('title_modulestatus')}">{$mod->Lang('status')}</span></th>
      <th><span title="{$mod->Lang('title_moduleaction')}">{$mod->Lang('action')}</span></th>
      <th class="pageicon"><span title="{$mod->Lang('title_moduleactive')}">{$mod->Lang('active')}</span></th>
      <th class="pageicon"><span title="{$mod->Lang('title_modulehelp')}">{$mod->Lang('helptxt')}</span></th>
      <th class="pageicon"><span title="{$mod->Lang('title_moduleabout')}">{$mod->Lang('abouttxt')}</span></th>
      {if $allow_export}<th class="pageicon"><span title="{$mod->Lang('title_moduleexport')}">{$mod->Lang('export')}</span></th>{/if}
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
	  {if !$item.ver_compatible}<br/><span class="important">{$mod->Lang('notcompatible')}</span>{/if}
          {if !$item.writable}<br/>{$mod->Lang('cantremove')}{/if}
          {if isset($item.dependants)}<br/>{$mod->Lang('has_dependants')}: (<strong>{implode(',',$item.dependants)}</strong>){/if}
      </td>
      <td>
        {if !$item.installed}
          {if $item.ver_compatible}<a href="{cms_action_url action='local_install' mod=$item.name}" title="{$mod->Lang('title_install')}">{$mod->Lang('install')}</a>{/if}
          {if $item.writable}
            <a href="{cms_action_url action='local_remove' mod=$item.name}" title="{$mod->Lang('title_remove')}">{$mod->Lang('remove')}</a>
          {else}
            <a href="{cms_action_url action='local_chmod' mod=$item.name}" title="{$mod->Lang('title_chmod')}">{$mod->Lang('changeperms')}</a>
          {/if}
        {else}
	  {if !isset($item.dependants) & $item.can_uninstall}
            <a href="{cms_action_url action='local_uninstall' mod=$item.name}" title="{$mod->Lang('title_uninstall')}">{$mod->Lang('uninstall')}</a>
	  {/if}

          {if $item.status == 'need_upgrade'}
            <a href="{cms_action_url action='local_upgrade' mod=$item.name}" title="{$mod->Lang('title_upgrade')}">{$mod->Lang('upgrade')}</a>
          {/if}
        {/if}
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
