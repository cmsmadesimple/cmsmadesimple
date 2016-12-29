{tab_header name='profiles' label=$mod->Lang('tab_profiles') active=$active_tab}
{tab_header name='defaults' label=$mod->Lang('tab_defaults') active=$active_tab}
{tab_header name='preferences' label=$mod->Lang('tab_preferences') active=$active_tab}

{tab_start name='profiles'}
<a href="{cms_action_url action=edit_profile}" class="pageoptions">{admin_icon alt="{$mod->Lang('add_profile')}" title="{$mod->Lang('add_profile')}" icon='newobject.gif'}</a>&nbsp;<a href="{cms_action_url action=edit_profile}" class="pageoptions">{$mod->Lang('add_profile')}</a>
  
<table class="pagetable">
    <thead>
      <tr>
        <th>#</th>
        <th>ID</th>
        <th>Name</th>
        <th>Tag</th>
        <th>Created</th>
        <th>Last Edited</th>
        <th class="pageicon">&nbsp;</th>
        <th class="pageicon">&nbsp;</th>
      </tr>
    </thead>
    <tbody class="content" width="100%">
    </tbody>
    {foreach $profiles as $profile}
      <tr>
        <td>{$profile@iteration}</th>
        <td>{$profile->id}</th>
        <td>{$profile->name}</th>
        <td>{ldelim}cms_filepicker profile='{$profile->name}'{rdelim}</th>
        <td>{$profile->create_date|cms_date_format}</th>
        <td>{$profile->modified_date|cms_date_format}</th>
        <td><a href="{cms_action_url action=edit_profile _id=$profile->id}" class="pageoptions">{admin_icon alt="{$mod->Lang('edit_profile')}" title="{$mod->Lang('edit_profile')}" icon='edit.gif'}</a></th>
        <td><a href="{cms_action_url action=delete_profile _id=$profile->id}" class="pageoptions">{admin_icon alt="{$mod->Lang('delete_profile')}" title="{$mod->Lang('delete_profile')}" icon='delete.gif'}</a></th>
      </tr>
      {foreachelse}
        <tr>
          <td>--</th>
          <td>--</th>
          <td><a href="{cms_action_url action=edit_profile}" class="pageoptions">{admin_icon alt="{$mod->Lang('add_profile')}" title="{$mod->Lang('add_profile')}" icon='newobject.gif'}</a>&nbsp;<a href="{cms_action_url action=edit_profile}" class="pageoptions">{$mod->Lang('add_profile')}</a></th>
          <td>--</th>
          <td>--</th>
          <td></th>
          <td></th>
          <td></th>
        </tr>
      {/foreach}
    </table>
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