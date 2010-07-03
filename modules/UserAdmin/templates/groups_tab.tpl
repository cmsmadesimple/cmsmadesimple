{if count($groups)}
	<div class="pageoverflow">
		{mod_form action="admin_editgroupperms"}
			<table class="pagetable" cellspacing="0">
				<thead>
					<tr>
						<th>{tr}Name{/tr}</td>
						<th class="pageicon">{tr}Active{/tr}</td>
						<th class="pageicon">&nbsp;</td>
						<th class="pageicon">&nbsp;</td>
						<th class="pageicon">&nbsp;</td>
						<th class="pageicon">&nbsp;</td>
					</tr>
				</thead>
				{foreach from=$groups item='onegroup'}
					{cycle values='row1,row2' assign='rowclass'}
					<tr class="{$rowclass}" onmouseover="this.className='{$rowclass}hover';" onmouseout="this.className='{$rowclass}';">
						<td>{$onegroup->name}{if $onegroup->id == 1}&nbsp;<i>({tr}Special Group{/tr}*){/if}</i></td>
						<td>{if $onegroup->id != 1}{if $onegroup->active}{mod_link gid=$onegroup->id action='admin_setgroupactive' value='Set Inactive' theme_image='icons/system/true.gif'}{else}{mod_link gid=$onegroup->id action='admin_setgroupactive' value='Set Active' theme_image='icons/system/false.gif'}{/if}{/if}</td>
						<td>{if $onegroup->id != 1}{mod_link action='admin_editgroupperms' gid=$onegroup->id value='Edit Permissions' theme_image='icons/system/permissions.gif'}{/if}</td>
						<td>{mod_link action='admin_editgroup' gid=$onegroup->id value='Edit Group' theme_image='icons/system/edit.gif'}</td>
						<td>{if $onegroup->id != 1}{mod_link action='admin_deletegroup' gid=$onegroup->id value='Delete Group' theme_image='icons/system/delete.gif' warn_message='confirm_deletegroup'}{/if}</td>
						<td>{if $onegroup->id != 1}{mod_checkbox name="groups[]" value=$onegroup->id}{/if}</td> 
					</tr>
				{/foreach}
			</table>
			<div style="float:right;">{mod_submit name="next" value="admin_editgroupperms"}</div>
		{/mod_form}
	</div>
	<br/>
{/if}
<div class="pageoverflow">
{mod_link action='admin_addgroup' value='Add Group' theme_image='icons/system/newobject.gif' showtext='1'}
</div>