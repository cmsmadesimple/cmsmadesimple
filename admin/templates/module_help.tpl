{* module help template *}
<div class="pageheader">
{'modulehelp'|lang:$module}
<span class="helptext">
  {if $current_language != 'en_US'}
  <a href="{$smarty.server.REQUEST_URI}&lang=en_US" title="{'display_english_help'|lang}">{'display_english_help'|lang}</a>
  {/if}
  {if isset($wiki_url)}
     &nbsp;
     <a href="{$wiki_url}" target="_blank">{$ext_help_image}</a>&nbsp;
     <a href="{$wiki_url}" target="_blank">{'help'|lang}</a>&nbsp;({'new_window'|lang})
  {/if}
</span>
</div>{* pageheader *}
{$module_help_output}
