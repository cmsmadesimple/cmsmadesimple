<h3>{tr}Action{/tr}:&nbsp;{tr}{$module_action}{/tr}</h3>
<h4>{tr}Group{/tr}:&nbsp;{$group->name}</h4>
<br/>
{mod_form action=$module_action}
  {mod_hidden name='gid' value=$group->id}
  <table class="pagetable" cellspacing="0">
    <thead>
      <tr>
        <th>{tr}Module{/tr}</th>
        <th>{tr}Extra{/tr}</th>
        <th>{tr}Name{/tr}</th>
        <th>&nbsp;</th>
      </tr>
    </thead>
    <tbody>
    {foreach from=$permissions item='oneperm'}
      {cycle values='row1,row2' assign='rowclass'}
      <tr class="{$rowclass}" onmouseover="{$rowclass}hover';" onmouseout="this.className='{$rowclass}';">
	<td>{$oneperm.module}</td>
        <td>{$oneperm.extra_attr}</td>
        <td>{$oneperm.name}</td>
        <td>
	  {assign var='tmp' value=$oneperm.id}
          {if $oneperm.selected}
            {mod_checkbox name="selected[$tmp]" selected='1'}
          {else}
            {mod_checkbox name="selected[$tmp]"}
	  {/if}
        </td>
      </tr>
    {/foreach}
    </tbody>
  </table>
  <br/>
  <p>
    {mod_submit name='submit' value='Submit' confirm_text='confirm_editgroupperms' class="positive"}
    {mod_submit name='cancel' value='Cancel' class="negative"}
  </p>
{/mod_form}