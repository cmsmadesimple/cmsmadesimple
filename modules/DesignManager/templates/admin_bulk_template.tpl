<h3>{$mod->Lang($bulk_op)}</h3>

{if isset($templates)}
<table class="pagetable" cellspacing="0">
  <thead>
   <tr>
     <th>{$mod->Lang('prompt_id')}</th>
     <th>{$mod->Lang('prompt_name')}</th>
     <th>{$mod->Lang('prompt_type')}</th>
     <th>{$mod->Lang('prompt_owner')}</th>
     <th>{$mod->Lang('prompt_modified')}</th>
   </tr>
  </thead>
  <tbody>
  {foreach from=$templates item='tpl'}
    <tr>
      <td>{$tpl->get_id()}</td>
      <td>{$tpl->get_name()}</td>
      <td>{$tpl->get_type_id()}</td>
      <td>{$tpl->get_owner_id()}</td>
      <td>{$tpl->get_modified()|date_format:'%x %X'}</td>
    </tr>
  {/foreach}
  </tbody>
</table>
{/if}