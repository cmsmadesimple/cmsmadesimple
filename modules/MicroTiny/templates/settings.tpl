<fieldset>
  <legend>{$mod->lang('prompt_profiles')}</legend>
  <table class="pagetable">
    <thead>
      <tr>
        <th>{$mod->Lang('prompt_name')}</th>
        <th class="pageicon">{* edit *}</th>
      </tr>
    </thead>
    <tbody>
      {foreach $profiles as $profile}
        {cms_action_url action='admin_editprofile' profile=$profile.name profile=$profile.name assign='edit_url'}
      <tr>
        <td><a href="{$edit_url}" title="{$mod->Lang('title_edit_profile')}">{$profile.label}</a></td>
        <td><a href="{$edit_url}">{admin_icon icon='edit.gif' alt=$mod->Lang('title_edit_profile')}</a></td>
      </tr>
      {/foreach}
    </tbody>
  </table>
</fieldset>
