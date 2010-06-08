{*
<div id="admin_group_warning" style="display:none">
{$admin_group_warning}
</div>
*}

{if isset($message)}
<p class="pageheader">{$message}</p>
{/if}


<div class="pageoverflow">
<form method="post" action="{$filter_action}">
<div class="hidden">
  <input type="hidden" name="{$cms_secure_param_name}" value="{$cms_user_key}" />
</div>
	<b>{$selectgroup}:</b>&nbsp;
        <select name="groupsel" id="groupsel">
	{foreach from=$allgroups item=thisgroup}
           {if $thisgroup->id == $disp_group}
                <option value="{$thisgroup->id}" selected="selected">{$thisgroup->name}</option>
           {else}
		<option value="{$thisgroup->id}">{$thisgroup->name}</option>
           {/if}
	{/foreach}
	</select>&nbsp;
        <input type="submit" name="filter" value="{$apply}"/>
</form>
</div><br />

{$form_start}
<div class="hidden">
  <input type="hidden" name="{$cms_secure_param_name}" value="{$cms_user_key}" />
</div>
<div class="pageoverflow">
  <p class="pageoptions">
    {$hidden}{$hidden2}
    {$submit} {$cancel}
  </p>
</div>
<table cellspacing="0" class="pagetable" id="permtable">
  <thead>
  <tr>
    <th>{$title_permission}</th>
	{foreach from=$group_list item=thisgroup}
		{if $thisgroup->id != -1}<th class="g{$thisgroup->id}">{$thisgroup->name}</th>{/if}
	{/foreach}
 </tr>
  </thead>
  <tbody>
  {foreach from=$perms item=perm}
    {cycle values='row1,row2' assign='currow'}
    <tr class="{$currow}" onmouseover="this.className='{$currow}hover';" onmouseout="this.className='{$currow}';">
 		<td>{$perm->name}</td>
		{foreach from=$group_list item=thisgroup}
			{if $thisgroup->id != -1}
			{assign var="gid" value=`$thisgroup->id`}
			<td class="g{$thisgroup->id}"><input type="checkbox" name="pg_{$perm->id}_{$gid}" value="1"{if isset($perm->group[$gid]) || $gid == 1} checked="checked"{/if} {if $gid == 1} disabled="disabled"{/if} /></td>
			{/if}
		{/foreach}
    </tr>
  {/foreach}
  </tbody>
</table>

<div class="pageoverflow">
  <p class="pageoptions">
    {$hidden}
    {$submit} {$cancel}
  </p>
</div>
</form>
