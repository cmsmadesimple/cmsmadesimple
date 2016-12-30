{tab_header name='profiles' label=$mod->Lang('tab_profiles') active=$active_tab}
{tab_header name='defaults' label=$mod->Lang('tab_defaults') active=$active_tab}
{tab_header name='preferences' label=$mod->Lang('tab_preferences') active=$active_tab}
{$mod->Lang('')}
{tab_start name='profiles'}
	<p>
		<a href="{cms_action_url action=edit_profile}" class="pageoptions">{admin_icon alt="{$mod->Lang('add_profile')}" title="{$mod->Lang('add_profile')}" icon='newobject.gif'}</a>&nbsp;<a href="{cms_action_url action=edit_profile}" class="pageoptions">{$mod->Lang('add_profile')}</a>
	</p>
	{if !empty($profiles)}
		<table class="pagetable">
			<thead>
				<tr>
					<th>#</th>
					<th>{$mod->Lang('th_id')}</th>
					<th>{lang('name')}</th>
					<th>{$mod->Lang('th_tag')}</th>
					<th>{$mod->Lang('th_created')}</th>
					<th>{$mod->Lang('th_last_edited')}</th>
					<th class="pageicon">&nbsp;</th>
					<th class="pageicon">&nbsp;</th>
				</tr>
			</thead>
			<tbody>
				{foreach $profiles as $profile}
					<tr>
						<td>{$profile@iteration}</td>
						<td>{$profile->id}</td>
						<td>{$profile->name}</td>
						<td>{ldelim}FilePicker profile='{$profile->name}'{rdelim}</td>
						<td>{$profile->create_date|cms_date_format}</td>
						<td>{$profile->modified_date|cms_date_format}</td>
						<td><a href="{cms_action_url action=edit_profile _id=$profile->id}" class="pageoptions">{admin_icon alt="{$mod->Lang('edit_profile')}" title="{$mod->Lang('edit_profile')}" icon='edit.gif'}</a></td>
						<td><a href="{cms_action_url action=delete_profile _id=$profile->id}" class="pageoptions">{admin_icon alt="{$mod->Lang('delete_profile')}" title="{$mod->Lang('delete_profile')}" icon='delete.gif'}</a></td>
					</tr>
				{/foreach}
			</tbody>
		</table>
	{else}
		<p class="information">{$mod->Lang('no_profiles')}</p>
	{/if}
	
	{tab_start name='defaults'}
		{form_start action='save_defaults'}
			<div class="pageoverflow">
				<p class="pageinput">
					{*<input type="submit" id="submit" name="{$actionid}submit" value="{lang('submit')}"/>*}
					<input type="submit" id="apply" name="{$actionid}apply" value="{lang('apply')}"/>
					{*<input type="submit" id="cancel" name="{$actionid}cancel" value="{lang('cancel')}"/>*}
				</p>
			</div>
			
			{foreach $prefs as $param}
				<div class="pageoverflow">
					<p class="pagetext">{$mod->Lang("Profile_{$param.name}")}:&nbsp;{cms_help key2="HelpPopup_{$param.name}" title=$mod->Lang("HelpPopupTitle_{$param.name}")}</p>
					<p class="pageinput">
						{if $param.type == 0} {* Text Input *}
							<input type="text" id="{$param.name}" name="{$actionid}{$param.name}" value="{$param.value}"/>
						{/if} 
						{if $param.type == 1} {* Text Area *}
							<input type="text" id="{$param.name}" name="{$actionid}{$param.name}" value="{$param.value}"/>
						{/if} 
						{if $param.type == 2} {* Dropdown *}
							{html_options name="{$actionid}{$param.name}" options=$param.options selected=$param.value}
						{/if} 
						{if $param.type == 3} {* Multiselect *}
							<select name="{$actionid}{$param.name}[]" size="{$param.options|count}" multiple>
								{html_options options=$param.options selected=explode(',', $param.value)}
							</select>
						{/if} 
						{if $param.type == 4} {* Checkbox *}
							<input type="checkbox" name="{$actionid}{$param.name}" {if $param.value} checked="checked"{/if} />
						{/if} 
					</p>
				</div>
			{/foreach}
			
			<div class="pageoverflow">
				<p class="pageinput">
					{*<input type="submit" id="submit" name="{$actionid}submit" value="{lang('submit')}"/>*}
					<input type="submit" id="apply" name="{$actionid}apply" value="{lang('apply')}"/>
					{*<input type="submit" id="cancel" name="{$actionid}cancel" value="{lang('cancel')}"/>*}
				</p>
			</div>
		{form_end}
		
	{tab_start name='preferences'}
		preferences
{tab_end}