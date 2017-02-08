{if isset($message)}
	<p class="pageheader">{$message}</p>
{/if}

<div class="information">{lang('info_changegroupperms')}{cms_help key2='help_group_permissions' title=lang('info_changegroupperms')}</div>

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
		</select>
		&nbsp;<input type="submit" name="filter" value="{$apply}"/>
	</form>
</div>

<br />

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
	
	<table class="pagetable scrollable" id="permtable">
		<thead>
			<tr>
				<th>{$title_permission}</th>
				{foreach from=$group_list item=thisgroup}
					{if $thisgroup->id != -1}<th class="g{$thisgroup->id}">{$thisgroup->name}</th>{/if}
				{/foreach}
			</tr>
		</thead>
		<tbody>
			{foreach $perms as $section => $list}
				<tr>
					<td colspan="{count($group_list)+1}"><h3>{$section|upper}</h3></td>
				</tr>
				{foreach $list as $perm}
					{cycle values='row1,row2' assign='currow'}
					<tr class="{$currow}">
						<td>
							&nbsp;&nbsp;&nbsp;<strong>{$perm->label}</strong>
							{if !empty($perm->description)}<div class="description">&nbsp;&nbsp;&nbsp;{$perm->description}</div>{/if}
						</td>
						{foreach from=$group_list item=thisgroup}
							{if $thisgroup->id != -1}
								{assign var="gid" value=$thisgroup->id}
								<td class="g{$thisgroup->id}"><input type="checkbox" name="pg_{$perm->id}_{$gid}" value="1"{if isset($perm->group[$gid]) || $gid == 1} checked="checked"{/if} {if $gid == 1} disabled="disabled"{/if} /></td>
							{/if}
						{/foreach}
					</tr>
				{/foreach}
			{/foreach}
		</tbody>
	</table>

	<div class="pageoverflow">
		<p class="pageoptions">
			{$hidden}
			{$submit} {$cancel}
		</p>
	</div>
{$form_end}
