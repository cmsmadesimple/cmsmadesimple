<h3>{$mod->Lang($bulk_op)}</h3>

{if isset($templates)}
<table class="pagetable">
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
      <td>{$tpl->get_modified()|localedate_format:'%x %X'}</td>
    </tr>
  {/foreach}
  </tbody>
</table>
{/if}

{form_start allparms=$allparms}
{if $bulk_op == 'bulk_action_delete'}
  <div class="pagewarning">{$mod->Lang('warn_bulk_delete_templates')}</div>
  <div class="pageoverflow">
    <p class="pagetext"></p>
    <p class="pageinput">
      <input id="check1" type="checkbox" name="{$actionid}check1" value="1"/>&nbsp;<label for="check1">{$mod->Lang('confirm_bulk_template_1')}</label><br/>
      <input id="check2" type="checkbox" name="{$actionid}check2" value="1"/>&nbsp;<label for="check2">{$mod->Lang('confirm_bulk_template_2')}</label>
    </p>
  </div>
{/if}

<div class="pageoverflow">
  <p class="pagetext"></p>
  <p class="pageinput">
    <input type="submit" name="{$actionid}submit" value="{$mod->Lang('submit')}"/>
    <input type="submit" name="{$actionid}cancel" value="{$mod->Lang('cancel')}"/>
  </p>
</div>
{form_end}
