<script type="text/javascript">
{literal}
function showhide_advanced()
{
  if( document.getElementById('advanced').checked )
  {
    document.getElementById('advhelp').style.display = 'inline';
  }
  else
  {
    document.getElementById('advhelp').style.display = 'none';
  }
}
{/literal}
</script>

{$formstart}
<fieldset>
<legend>{$mod->Lang('search_input')}:</legend>
<div class="pageoverflow">
  <p class="pagetext">{$mod->Lang('searchterm')}</p>
  <p class="pageinput">
    <input type="text" name="{$actionid}term" size="50"/>
  </p>
</div>

<div class="pageoverflow">
  <p class="pagetext">{$mod->Lang('prompt_advancedsearch')}</p>
  <p class="pageinput">
    <input type="hidden" name="{$actionid}advanced" value="0"/>
    <input type="checkbox" id="advanced" name="{$actionid}advanced" value="1" onclick="showhide_advanced();"/>
    <span id="advhelp" style="display: none;"><br/>{$mod->Lang('advancedsearch_help')}</span>
  </p>
</div>

<div class="pageoverflow">
  <p class="pagetext"></p>
  <p class="pageinput">
    <input type="submit" name="{$actionid}submit" value="{$mod->Lang('submit')}"/>
  </p>
</div>
</fieldset>
{$formend}

{if isset($search_data)}
<br/>
<fieldset>
<legend>{$mod->Lang('search_results')}:</legend>
<table class="pagetable" cellspacing="0">
 <thead>
  <tr>
   <th width="20%">{$mod->Lang('nametext')}</th>
   <th>{$mod->Lang('vertext')}</th>
   <th>{$mod->Lang('sizetext')}</th>
   <th>&nbsp;</th>
   <th>&nbsp;</th>
   <th>&nbsp;</th>
  </tr>
 </thead>
 <tbody>
 {foreach from=$search_data item=entry}
   {cycle values='row1,row2' assign='rowclass'}
   <tr class="{$rowclass}" onmouseover="this.className='{$rowclass}hover';" onmouseout="this.className='{$rowclass}';">
     <td>{$entry->name}</td>
     <td>{$entry->version}</td>
     <td>{$entry->size}</td>
     <td>{if isset($entry->status)}{$entry->status}{/if}</td>
     <td>{if isset($entry->dependslink)}{$entry->dependslink}{/if}</td>
     <td>{$entry->helplink}</td>
     <td>{if isset($entry->aboutlink)}{$entry->aboutlink}{/if}</td>
   </tr>
   {if isset($entry->description)}
   <tr class="{$rowclass}" onmouseover="this.className='{$rowclass}hover';" onmouseout="this.className='{$rowclass}';">
     <td>&nbsp;</td>
     <td colspan="6">{$entry->description}</td>
   </tr>
   {/if}
 {/foreach}
 </tbody>
</table>
</fieldset>
{/if}