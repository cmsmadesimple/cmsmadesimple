<script type="text/javascript">
$(document).ready(function() {

    $('#sel_all').cmsms_checkall();

    $('.switchuser').click(function(ev){
	ev.preventDefault();
	var _href = $(this).attr('href');
	cms_confirm('{lang('confirm_switchuser')|escape:'javascript'}').done(function(){
	    window.location.href = _href;
	});
    });

    $('.toggleactive').click(function(ev){
        ev.preventDefault();
	var _href = $(this).attr('href');
        cms_confirm('{lang('confirm_toggleuseractive')|escape:'javascript'}').done(function(){
	    window.location.href = _href;
	});
    });

    $(document).on('click', '.js-delete', function(ev){
        ev.preventDefault();
	var _href = $(this).attr('href');
        cms_confirm('{lang('confirm_delete_user')|escape:'javascript'}').done(function(){
	    window.location.href = _href;
	});
    });

    $('#withselected, #bulksubmit').attr('disabled','disabled');
    $('#bulksubmit').button({ 'disabled' : true });
    $('#sel_all, .multiselect').on('click',function(){
        if( !$(this).is(':checked') ) {
            $('#withselected').attr('disabled','disabled');
            $('#bulksubmit').attr('disabled','disabled');
            $('#bulksubmit').button({ 'disabled' : true });
        } else {
            $('#withselected').removeAttr('disabled');
            $('#bulksubmit').removeAttr('disabled');
            $('#bulksubmit').button({ 'disabled' : false });
        }
    });

    $('#listusers').submit(function(ev){
        ev.preventDefault();
        var v = $('#withselected').val();
	if( v === 'delete' ) {
	    cms_confirm('{lang('confirm_delete_user')|escape:'javascript'}').done(function(){
	        $('#listusers').unbind('submit');
            $('#bulksubmit').click();
	    }).fail(function() {
	        return false;
	    });
	} else {
            cms_confirm('{lang('confirm_bulkuserop')|escape:'javascript'}').done(function(){
            $('#listusers').unbind('submit');
            $('#bulksubmit').click();
	        return true;
	    });
	}
    });

    $('#withselected').change(function(){
        var v = $(this).val();

        if (v === 'copyoptions') {
            $('#userlist').show();
        } else {
            $('#userlist').hide();
        }
    });
});
</script>
{strip}

<h3 class="invisible">{lang('currentusers')}</h3>

{form_start url='listusers.php' id="listusers"}

    <div class="pageoptions">
        <a href="adduser.php{$urlext}" title="{lang('info_adduser')}">{admin_icon icon='newobject.gif' class='systemicon'}&nbsp;{lang('adduser')}</a>
    </div>

    <table class="pagetable">
        <thead>
            <tr>
                <th>{lang('username')}</th>
                <th style="text-align: center;">{lang('active')}</th>
		{if $is_admin}<th class="pageicon"></th>{/if}
                <th class="pageicon"></th>
                <th class="pageicon"></th>
                <th class="pageicon"><input type="checkbox" id="sel_all" value="1" title="{lang('selectall')}"/></th>
            </tr>
        </thead>
        <tbody>
        {foreach from=$users item='user'}
            <tr class="{cycle values='row1,row2'}">
                {$can_edit=1}
                {if !$user->access_to_user }
                    {$can_edit=0}
                {/if}
                <td>
                    {if $can_edit}
                        <a href="edituser.php{$urlext}&amp;user_id={$user->id}" title="{lang('edituser')}">{$user->username}</a>
                    {else}
                        <span title="{lang('info_noedituser')}">{$user->username}</span>
                    {/if}
                </td>

                <td style="text-align: center;">
                    {if $can_edit && $user->id != $my_userid}
                        <a href="listusers.php{$urlext}&amp;toggleactive={$user->id}" title="{lang('info_user_active2')}" class="toggleactive">
                            {if $user->active}{admin_icon icon='true.gif'}{else}{admin_icon icon='false.gif'}{/if}
                        </a>
                    {/if}
                </td>

		{if $is_admin}
		<td>
		  {if $user->active && $user->id != $my_userid}
		  <a href="listusers.php{$urlext}&amp;switchuser={$user->id}" title="{lang('info_user_switch')}" class="switchuser">
		     {admin_icon icon='run.gif'}
		  </a>
		  {/if}
		</td>
		{/if}

                <td>
                    {if $can_edit}
                        <a href="edituser.php{$urlext}&amp;user_id={$user->id}" title="{lang('edituser')}">{admin_icon icon='edit.gif'}</a>
                    {/if}
                </td>
                <td>
                    {if $can_edit && $user->id != $my_userid}
                        <a class="js-delete" href="deleteuser.php{$urlext}&amp;user_id={$user->id}" title="{lang('deleteuser')}">{admin_icon icon='delete.gif'}</a>
                    {/if}
                </td>
                <td>
                    {if $can_edit && $user->id != $my_userid}
                        <input class="multiselect" type="checkbox" name="multiselect[]" value="{$user->id}" title="{lang('info_selectuser')}"/>
                    {/if}
                </td>
            </tr>
        {/foreach}
        </tbody>
    </table>

    <div class="pageoptions">
        <div style="width: 40%; float: left;">
            <a href="adduser.php{$urlext}" title="{lang('info_adduser')}">{admin_icon icon='newobject.gif'}&nbsp;{lang('adduser')}</a>
        </div>
        <div style="width: 40%; float: right; text-align: right;">
            <label for="withselected">{lang('selecteditems')}:</label>&nbsp;
            <select name="bulkaction" id="withselected">
                <option value="delete">{lang('delete')}</option>
                <option value="clearoptions">{lang('clearusersettings')}</option>
                <option value="copyoptions">{lang('copyusersettings2')}</option>
                <option value="disable">{lang('disable')}</option>
                <option value="enable">{lang('enable')}</option>
            </select>&nbsp;
            <div id="userlist" style="display: none;">
                <label for="userlist_sub">{lang('copyfromuser')}:</label>&nbsp;
                <select name="userlist" id="userlist_sub">
                    {html_options options=$userlist}
                </select>&nbsp;
            </div>

            <input type="submit" id="bulksubmit" name="bulk" value="{lang('submit')}"/>
        </div>
    </div>
{form_end}

{/strip}
