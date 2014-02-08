{strip}{$tpltype=$list_all_types[$type_id]}
<strong>{$mod->Lang('prompt_id')}:</strong>&nbsp;{$type_id}<br/>
<strong>{$mod->Lang('prompt_name')}:</strong>&nbsp;{$tpltype->get_name()}<br/>
{$org=$tpltype->get_originator()}{if $org == $coretypename}{$org='Core'}{/if}
<strong>{$mod->Lang('prompt_originator')}:</strong>&nbsp;{$org}<br/>
{$tmp=$tpltype->get_description()}{if $tmp != ''}
<strong>{$mod->Lang('prompt_description')}:</strong>&nbsp;{$tpltype->get_description()|summarize}
{/if}
{strip}