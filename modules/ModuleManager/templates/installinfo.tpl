<h3>{$mod->Lang('title_installation')}:</h3>
<div class="pagewarning"><h3>{$mod->Lang('notice')}:</h3>
  <p>{$time_warning}</p>
</div>

{if isset($message)}
  <div class="pagemessage"><p>{$message}</p></div>
{else}
<div class="pageoverflow">
<p class="pagetext">{$mod->Lang('notice_depends',$installmodule)}:</p>
<ul class="pageinput">
{foreach from=$dependencies item='one'}
  {if $one.status == 'i'}
    <li>{$mod->Lang('depend_install',$one.name,$one.version)}</li>
  {/if}
  {if $one.status == 'u'}
    <li>{$mod->Lang('depend_upgrade',$one.name,$one.version)}</li>
  {/if}
  {if $one.status == 'a'}
    <li>{$mod->Lang('depend_activate',$one.name)}</li>
  {/if}
{/foreach}
</ul>
</div>
{/if}

{if isset($form_start)}
<br />
{$form_start}
<div class="pageoverflow">
  <p class="pagetext"></p>
  <p class="pageinput">{$cancel} {$submit}</p>
</div>
<br /><br />
{$formend}
{/if}
<p class="pageoptions">{$link_back}</p>