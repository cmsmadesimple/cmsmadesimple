{* module help template *}
<div class="pageheader">
{'modulehelp'|lang:$module}
{if isset($wiki_url)}
  <span class="helptext">
     <a href="{$wiki_url}" target="_blank">{$ext_help_image}</a>&nbsp;
     <a href="{$wiki_url}" target="_blank">{'help'|lang}</a>&nbsp;({'new_window'|lang})
  </span>
{/if}
</div>{* pageheader *}
{$module_help_output}
