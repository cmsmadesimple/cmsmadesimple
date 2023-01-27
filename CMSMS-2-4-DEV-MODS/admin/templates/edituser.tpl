<script type="text/javascript">
$(document).ready(function(){
    $(document).on('click', '#submit', function(){
        return confirm('{lang('confirm_edituser')|escape:'javascript'}');
    });

    //{if $manage_users == true}

    $('#copyusersettings').change(function () {
        var v = $(this).val();
        if (v == -1) {
             $('#clearusersettings').removeAttr('disabled');
         } else {
             $('#clearusersettings').attr('disabled', 'disabled');
         }
     });

     $('#clearusersettings').click(function () {
         $('#copyusersettings').val(-1);

         var v = $(this).attr('checked');
         if (v == 'checked') {
             $('#copyusersettings').attr('disabled', 'disabled');
         } else {
             $('#copyusersettings').removeAttr('disabled');
         }
    });
    //{/if}

});
</script>
<div class="pagecontainer">
    <h3>{lang('edituser')}{if $user != ''}&nbsp;{$user}{/if}</h3>

    {form_start url='edituser.php'}
        <input type="hidden" value="{$user_id}" name="user_id" />

        {tab_header name='user' label=lang('profile')}
        {if isset($groups)}
            {tab_header name='groups' label=lang('groups')}
        {/if}
        {if $manage_users == true}
            {tab_header name='settings' label=lang('settings')}
        {/if}

        <!-- user profile -->
        {tab_start name='user'}
        <div class="pageoverflow">
            <p class="pagetext">
                <label for="username">{lang('name')}:</label>&nbsp;{cms_help realm='admin' key='info_adduser_username' title=lang('name')}
            </p>
            <p class="pageinput">
                <input type="text" id="username" name="user" maxlength="25" value="{$user}" class="standard"/>
            </p>
        </div>
        <div class="pageoverflow">
            <p class="pagetext">
                <label for="password">{lang('password')}:</label>&nbsp;{cms_help realm='admin' key='info_edituser_password' title=lang('password')}
            </p>
            <p class="pageinput">
                <input type="password" id="password" name="password" maxlength="100" value="" class="standard"/>
                <br />
                {lang('info_edituser_password')}
            </p>
        </div>
        <div class="pageoverflow">
            <p class="pagetext">
                <label for="passwordagain">{lang('passwordagain')}:</label>&nbsp;{cms_help realm='admin' key='info_edituser_passwordagain' title=lang('passwordagain')}
            </p>
            <p class="pageinput">
                <input id="passwordagain" type="password" name="passwordagain" maxlength="100" value="" class="standard"/>
                <br />
                {lang('info_edituser_passwordagain')}
            </p>
        </div>
        <div class="pageoverflow">
            <p class="pagetext">
                <label for="firstname">{lang('firstname')}:</label>&nbsp;{cms_help key2='help_myaccount_firstname' title=lang('firstname')}
            </p>
            <p class="pageinput">
                <input id="firstname" type="text" name="firstname" maxlength="50" value="{$firstname}" class="standard"/>
            </p>
        </div>
        <div class="pageoverflow">
            <p class="pagetext">
                <label for="lastname">{lang('lastname')}:</label>&nbsp;{cms_help key2='help_myaccount_lastname' title=lang('lastname')}
            </p>
            <p class="pageinput">
                <input id="lastname" type="text" name="lastname" maxlength="50" value="{$lastname}" class="standard"/>
            </p>
        </div>
        <div class="pageoverflow">
            <p class="pagetext">
                <label for="email">{lang('email')}:</label>&nbsp;{cms_help key2='help_myaccount_email' title=lang('email')}
            </p>
            <p class="pageinput">
                <input id="email" type="text" name="email" maxlength="255" value="{$email}" class="standard"/>
            </p>
        </div>

        {if !$access_user}
        <div class="pageoverflow">
            <p class="pagetext">
                <label for="active">{lang('active')}:</label>&nbsp;{cms_help realm='admin' key='info_user_active' title=lang('active')}
            </p>
            <p class="pageinput">
	        <input type="hidden" name="active" value="0"/>
                <input id="active" type="checkbox" class="pagecheckbox" name="active" value="1"{if $active == 1} checked="checked"{/if}/>
                <br />
                {lang('info_user_active')}
            </p>
        </div>
        {/if}

        {if isset($groups)}
        <!-- group options -->
        {tab_start name='groups'}
        <div class="pageverflow">
            <input type="hidden" name="groups" value="1"/>
            <p class="pagetext">
                {lang('groups')}:
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
                                <input type="checkbox" name="g{$onegroup->id}" id="g{$onegroup->id}" value="1"{if in_array($onegroup->
                                id,$membergroups)} checked="checked"{/if}/> </td>
                                <td><label for="g{$onegroup->id}">{$onegroup->name}</label></td>
                                <td>{$onegroup->description}</td>
                            </tr>
                            {/foreach}
                        </tbody>
                    </table>
                </div>
                <br />
                <div class="information">{lang('info_membergroups')}</div>
            </div>
        </div>
        {/if}

        {if $manage_users == true}
        <!-- user settings -->
        {tab_start name='settings'}
        <div class="pageoverflow">
            <p class="pagetext">
                <label for="copyusersettings" title="{lang('info_copyusersettings')}">{lang('copyusersettings')}:</label>&nbsp;{cms_help realm='admin' key='info_copyusersettings' title=lang('copyusersettings')}
            </p>
            <p class="pageinput">
                <select id="copyusersettings" name="copyusersettings">
                    {html_options options=$users}
                </select>
            </p>
        </div>
        <div class="pageoverflow">
            <p class="pagetext">
                <label for="clearusersettings" title="{lang('info_clearusersettings')}">{lang('clearusersettings')}</label>
            </p>
            <p class="pageinput">
                <input type="checkbox" name="clearusersettings" value="1" id="clearusersettings" title="{lang('info_clearusersettings')}:">
            </p>
        </div>
        {/if}

        {tab_end}

        <div class="pageoverflow">
            <input type="submit" id="submit" name="submit" value="{lang('submit')}" />
            <input type="submit" name="cancel" value="{lang('cancel')}" />
        </div>
    {form_end}
</div>