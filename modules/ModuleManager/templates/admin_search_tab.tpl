<script type="text/javascript">
$(document).ready(function(){
  {if !$advanced}$('#advhelp').hide();{/if}
  $('#advanced').click(function(){
    $('#advhelp').toggle();
  });
});
</script>

{function get_module_status_icon}
{strip}
{if $status == 'stale'}
{$stale_img}
{elseif $status == 'warn'}
{$warn_img}
{elseif $status == 'new'}
{$new_img}
{/if}
{/strip}
{/function}

{$formstart}
<fieldset>
<legend>{$ModuleManager->Lang('search_input')}:</legend>
<div class="pageoverflow">
  <p class="pagetext"><label for="searchterm">{$ModuleManager->Lang('searchterm')}:</label></p>
  <p class="pageinput">
    <input id="searchterm" type="text" name="{$actionid}term" size="50" value="{$term}" title="{$ModuleManager->Lang('title_searchterm')}" placeholder="{$ModuleManager->Lang('entersearchterm')}"/>&nbsp;
    <input type="hidden" name="{$actionid}advanced" value="0"/>
    <input type="checkbox" id="advanced" name="{$actionid}advanced" value="1" {if $advanced}checked="checked"{/if} title="{$ModuleManager->Lang('title_advancedsearch')}"/>&nbsp;<label for="advanced">{$ModuleManager->Lang('prompt_advancedsearch')}</label>
    <span id="advhelp" style="display: none;"><br/>{$ModuleManager->Lang('advancedsearch_help')}</span>
  </p>
</div>

<div class="pageoverflow">
  <p class="pagetext"></p>
  <p class="pageinput">
    <input type="submit" name="{$actionid}submit" value="{$ModuleManager->Lang('submit')}"/>
  </p>
</div>
</fieldset>
{$formend}

{if isset($search_data)}
<br/>
<fieldset>
<legend>{$ModuleManager->Lang('search_results')}:</legend>
<table class="pagetable sortable" cellspacing="0">
 <thead>
  <tr>
   <th></th>
   <th>{$ModuleManager->Lang('nametext')}</th>
   <th>{$ModuleManager->Lang('vertext')}</th>
   <th>{$ModuleManager->Lang('releasedate')}</th>
   <th>{$ModuleManager->Lang('downloads')}</th>
   <th>{$ModuleManager->Lang('sizetext')}</th>
   <th>{$statustext}</th>
   <th>&nbsp;</th>
   <th>&nbsp;</th>
   <th>&nbsp;</th>
  </tr>
 </thead>
 <tbody>
 {foreach from=$search_data item=entry}
   {cycle values='row1,row2' assign='rowclass'}
   <tr class="{$rowclass}" {if $entry->age=='new'}style="font-weight: bold;"{/if}>
     <td>{get_module_status_icon status=$entry->age}</td>
     <td><span title="{$entry->description|strip_tags|cms_escape}">{$entry->name}</span></td>
     <td>{$entry->version}</td>
     <td>{$entry->date|date_format:'%x'}</td>
     <td>{$entry->downloads}</td>
     <td>{$entry->size}</td>
     <td>{if isset($entry->status)}{$entry->status}{/if}</td>
     <td>{if isset($entry->dependslink)}{$entry->dependslink}{/if}</td>
     <td>{$entry->helplink}</td>
     <td>{if isset($entry->aboutlink)}{$entry->aboutlink}{/if}</td>
   </tr>
 {/foreach}
 </tbody>
</table>
</fieldset>
{/if}