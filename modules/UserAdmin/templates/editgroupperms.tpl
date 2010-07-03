<h3>{tr}Action{/tr}:&nbsp;{tr}{$module_action}{/tr}</h3>
<br/>
{mod_form action=$module_action}
  <table class="pagetable" cellspacing="0">
    <thead>
      <tr>
        <th>{tr}Module{/tr}</th>
        <th>{tr}Extra{/tr}</th>
        <th>{tr}Name{/tr}</th>
	{foreach from=$group item='onegroup'}
		<th>{mod_hidden name='groups[]' value=$onegroup->id}{$onegroup->name}</th>
	{/foreach}
      </tr>
    </thead>
    <tbody>
    {foreach from=$permissions item='oneperm'}
      {cycle values='row1,row2' assign='rowclass'}
      <tr class="{$rowclass}" onmouseover="{$rowclass}hover';" onmouseout="this.className='{$rowclass}';">
	<td>{$oneperm.module}</td>
        <td>{$oneperm.extra_attr}</td>
        <td>{tr module=$oneperm.module}{$oneperm.permission_name}{/tr}</td>
	{foreach from=$oneperm.selected key='group_name' item='group_access'}
		<td>
			{assign var='tmp' value=$oneperm.id}
			{mod_checkbox name="selected_$group_name[$tmp]" checked=$group_access}
		</td>
	{/foreach}
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