<div class="pagecontainer">
    <h3 class="invisible">{lang('adduser')}</h3>

    {form_start url='adduser.php'}
    {tab_header name='user' label=lang('profile')}
    {if isset($groups)}
        {tab_header name='groups' label=lang('groups')}
    {/if}
    {tab_header name='settings' label=lang('settings')}

    {tab_start name='user'}
    <div class="c_full cf">
        <label for="username" class="grid_3">*{lang('name')}:&nbsp;{cms_help realm='admin' key='info_adduser_username' title=lang('name')}</label>
        <input id="username" type="text" name="user" maxlength="255" value="{$user}" class="standard grid_8" required autocomplete="off"/>
    </div>
    <div class="c_full cf">
        <label for="password" class="grid_3">*{lang('password')}:&nbsp;{cms_help realm='admin' key='info_edituser_password' title=lang('password')}</label>
        <input type="password" id="password" name="password" maxlength="100" value="{$password}" class="standard grid_8" autocomplete="off"/>
    </div>
    <div class="c_full cf">
        <label for="passwordagain" class="grid_3">*{lang('passwordagain')}:&nbsp;{cms_help realm='admin' key='info_edituser_passwordagain' title=lang('passwordagain')}</label>
        <input type="password" id="passwordagain" name="passwordagain" maxlength="100" value="{$passwordagain}" class="standard grid_8" autocomplete="off"/>
    </div>
    <div class="c_full cf">
        <label for="firstname" class="grid_3">{lang('firstname')}:&nbsp;{cms_help key2='help_myaccount_firstname' title=lang('firstname')}</label>
        <input type="text" id="firstname" name="firstname" maxlength="50" value="{$firstname}" class="standard grid_8"/>
    </div>
    <div class="c_full cf">
        <label for="lastname" class="grid_3">{lang('lastname')}:&nbsp;{cms_help key2='help_myaccount_lastname' title=lang('lastname')}</label>
        <input type="text" id="lastname" name="lastname" maxlength="50" value="{$lastname}" class="standard grid_8"/>
    </div>
    <div class="c_full cf">
        <label for="email" class="grid_3">{lang('email')}:&nbsp;{cms_help key2='help_myaccount_email' title=lang('email')}</label>
        <input type="text" id="email" name="email" maxlength="255" value="{$email}" class="standard grid_8"/>
    </div>
    <div class="c_full cf">
        <label for="active" class="grid_3">{lang('active')}:&nbsp;{cms_help realm='admin' key='info_user_active' title=lang('active')}</label>
	<div class="grid_8">
            <input type="checkbox" class="pagecheckbox" name="active" value="1"{if $active == 1} checked="checked"{/if}/>
	</div>
    </div>

    {if isset($groups)}
    <!-- groups -->
    {tab_start name='groups'}
    <div class="pageverflow">
        <p class="pagetext">
            {lang('groups')}:&nbsp;{cms_help realm='admin' key='info_membergroups' title=lang('groups')}
        </p>
        <div class="pageinput">
            <div class="group_memberships clear">
                <table class="pagetable">
                    <thead>
                        <tr>
                            <th class="pageicon"></th>
                            <th>{lang('name')}</th>
                            <th>{lang('description')}</th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach from=$groups item='onegroup'}
                        <tr>
                            <td>
                            <input type="checkbox" name="sel_groups[]" id="g{$onegroup->id}" value="{$onegroup->id}" {if in_array($onegroup->
                            id,$sel_groups)}checked="checked"{/if}/> </td>
                            <td><label for="g{$onegroup->id}">{$onegroup->name}</label></td>
                            <td>{$onegroup->description}</td>
                        </tr>
                        {/foreach}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    {/if}

    <!-- settings -->
    {tab_start name='settings'}
    <div class="c_full cf">
        <label for="copyusersettings" class="grid_3">
            {lang('copyusersettings')}:&nbsp;{cms_help realm='admin' key='info_copyusersettings' title=lang('copyusersettings')}
        </label>
        <select name="copyusersettings" id="copyusersettings" class="grid_8">
            {html_options options=$users}
        </select>
    </div>
    {tab_end}

    <div class="c_full cf">
        <input type="submit" id="submit" name="submit" value="{lang('submit')}"/>
        <input type="submit" name="cancel" value="{lang('cancel')}"/>
    </div>
    {form_end}
</div>