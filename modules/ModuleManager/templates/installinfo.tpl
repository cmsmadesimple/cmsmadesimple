{if $is_upgrade}
  <h3>{$ModuleManager->Lang('upgrade_module')} {$module_name} <em>({$ModuleManager->Lang('vertext')} {$module_version})</em></h3>
{else}
  <h3>{$ModuleManager->Lang('install_module')} {$module_name} <em>({$ModuleManager->Lang('vertext')} {$module_version})</em></h3>
{/if}

<div class="pagewarning">
  <h3>{$ModuleManager->Lang('notice')}:</h3>
  <p>{$ModuleManager->Lang('time_warning')}</p>
</div>

{if isset($dependencies)}
  {if count($dependencies) > 1}
    <div class="warning">
      <h3>{$ModuleManager->Lang('warning')}</h3>
      <p>{$ModuleManager->Lang('warn_dependencies')}</p>
    </div>

    <ul>
    {foreach $dependencies as $name => $rec}
      <li>
        {if $rec.action == 'i'}{$ModuleManager->Lang('depend_install',$rec.name,$rec.version)}
        {elseif $rec.action == 'u'}{$ModuleManager->Lang('depend_upgrade',$rec.name,$rec.version)}
        {elseif $rec.action == 'a'}{$ModuleManager->Lang('depend_activate',$rec.name)}{/if}
      </li>
    {/foreach}
    </ul>
  {/if}
{/if}

{if isset($form_start)}
<br />
{$form_start}
<div class="pageoverflow">
  <p class="pagetext"></p>
  <p class="pageinput">
    {if count($dependencies) > 1}
      <input type="submit" name="{$actionid}submit" value="{$ModuleManager->Lang('install_procede')}"/>
    {else}
      <input type="submit" name="{$actionid}submit" value="{$ModuleManager->Lang('install_submit')}"/>
    {/if}
    <input type="submit" name="{$actionid}cancel" value="{$ModuleManager->Lang('cancel')}"/>
  </p>
</div>
{$formend}
{/if}
