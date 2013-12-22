<h3>{$mod->Lang('install_module')} {$module_name} <em>({$mod->Lang('vertext')} {$module_version})</em></h3>
<div class="pagewarning">
  <h3>{$mod->Lang('notice')}:</h3>
  <p>{$mod->Lang('time_warning')}</p>
</div>

{if isset($dependencies)}
  <div class="warning">
    <h3>{$mod->Lang('warning')}</h3>
    <p>{$mod->Lang('warn_dependencies')}</p>
  </div>
  <ul>
  {foreach $dependencies as $name => $rec}
    <li>
    {if $rec.action == 'i'}{$mod->Lang('depend_install',$rec.name,$rec.version)}
    {elseif $rec.action == 'u'}{$mod->Lang('depend_upgrade',$rec.name,$rec.version)}
    {elseif $rec.action == 'a'}{$mod->Lang('depend_activate',$rec.name)}{/if}
    </li>
  {/foreach}
  </ul>
{/if}

{if isset($form_start)}
<br />
{$form_start}
<div class="pageoverflow">
  <p class="pagetext"></p>
  <p class="pageinput">
    <input type="submit" name="{$actionid}submit" value="{$mod->Lang('install_submit')}"/>
    <input type="submit" name="{$actionid}cancel" value="{$mod->Lang('cancel')}"/>
  </p>
</div>
{$formend}
{/if}
