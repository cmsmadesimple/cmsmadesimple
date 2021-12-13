{strip}{if $template->locked()}
  {$lock=$template->get_lock()}
  {if $template->lock_expired()}<strong style="color: red;">{$mod->Lang('msg_steal_lock')}</strong><br/>{/if}
  <strong>{$mod->Lang('prompt_lockedby')}:</strong> {cms_admin_user uid=$lock.uid}<br/>
  <strong>{$mod->Lang('prompt_lockedsince')}:</strong> {$lock.created|localedate_format:'%x H:i'}<br/>
  {if $lock.expires < $smarty.now}
    <strong>{$mod->Lang('prompt_lockexpired')}:</strong> <span style="color: red;">{$lock.expires|relative_time}</span>
  {else}
    <strong>{$mod->Lang('prompt_lockexpires')}:</strong> {$lock.expires|relative_time}
  {/if}
{else}
  <strong>{$mod->Lang('prompt_name')}:</strong> {$template->get_name()} <em>({$template->get_id()})</em><br/>
  <strong>{$mod->Lang('prompt_owner')}:</strong> {cms_admin_user uid=$template->get_owner_id()}<br/>
  <strong>{$mod->Lang('prompt_created')}:</strong> {$template->get_created()|cms_date_format|cms_escape}<br/>
  <strong>{$mod->Lang('prompt_modified')}:</strong> {$template->get_modified()|relative_time}
  {$tmp=$template->get_description()}
  {if $tmp != ''}<br/><strong>{$mod->Lang('prompt_description')}:</strong> {$tmp|strip_tags|cms_escape|summarize}{/if}
{/if}{/strip}
