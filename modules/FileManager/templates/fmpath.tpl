<h3>{$mod->Lang('currentpath')}
   <span class="pathselector">
   {foreach $path_parts as $part}
     {if !empty($part->url)}
       <a href="{$part->url}">{$part->name}</a>
     {else}
       {$part->name}
     {/if}
     {if !$part@last}<span class="ds">/</span>{/if}
   {/foreach}
   </span>
</h3>