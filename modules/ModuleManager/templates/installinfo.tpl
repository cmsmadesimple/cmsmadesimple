{if $is_upgrade}
  <h3>{$ModuleManager->Lang('upgrade_module')} {$module_name} <em>({$ModuleManager->Lang('vertext')} {$module_version})</em></h3>
{else}
  <h3>{$ModuleManager->Lang('install_module')} {$module_name} <em>({$ModuleManager->Lang('vertext')} {$module_version})</em></h3>
{/if}

<div class="warning">
  <h3>{$ModuleManager->Lang('notice')}:</h3>
  <p>{$ModuleManager->Lang('time_warning')}</p>
</div>
<div class="clearb"></div>

{if isset($dependencies)}
  {$has_custom=0}
  {foreach $dependencies as $name => $rec}
     {if $rec.has_custom}{$has_custom=1}{/if}
  {/foreach}
  {if $has_custom}
    <div class="warning">
      <h3>{$ModuleManager->Lang('warning')}</h3>
      <p>{$mod->Lang('warn_modulecustom')}</p>
      <ul>
        {foreach $dependencies as $name => $rec}
          {if $rec.has_custom}<li>{$name}</li>{/if}
	{/foreach}
      </ul>
    </div>
    <div class="clearb"></div>
  {/if}

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