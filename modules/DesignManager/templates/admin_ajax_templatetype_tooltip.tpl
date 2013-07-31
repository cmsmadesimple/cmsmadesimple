{strip}
<strong>{$mod->Lang('prompt_name')}:</strong> {$tpltype->get_name()} <em>({$tpltype->get_id()})</em><br/>
<strong>{$mod->Lang('prompt_originator')}:</strong> {$tpltype->get_originator()}<br/>
<strong>{$mod->Lang('prompt_owner')}:</strong> {cms_admin_user uid=$tpltype->get_owner()}<br/>
<strong>{$mod->Lang('prompt_created')}:</strong> {$tpltype->get_create_date()|cms_date_format}<br/>
{$tmp=$tpltype->get_description()}
{if $tmp != ''}<br/><strong>{$mod->Lang('prompt_description')}:</strong> {$tmp|strip_tags|cms_escape|summarize}{/if}
{/strip}