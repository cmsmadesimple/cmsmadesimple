<script type="text/javascript">
$(document).ready(function(){
    var do_locking = {if !empty($tpl_id) && $tpl_id > 0 && isset($lock_timeout) && $lock_timeout > 0}1{else}0{/if};
    $('#form_edittemplate').dirtyForm({
        beforeUnload: function(is_dirty) {
	    if( do_locking ) $('#form_edittemplate').lockManager('unlock');
        },
	unloadCancel: function() {
            if( do_locking ) $('#form_edittemplate').lockManager('relock');
	}
    });

    // initialize lock manager
    if( do_locking ) {
      $('#form_edittemplate').lockManager({
        type: 'template',
        oid: {$tpl_id|default:0},
        uid: {get_userid(FALSE)},
        lock_timeout: {$lock_timeout|default:0},
        lock_refresh: {$lock_refresh|default:0},
        error_handler: function(err) {
            cms_alert('Lock Error '+err.type+' // '+err.msg);
        },
        lostlock_handler: function(err) {
            // we lost the lock on this content... make sure we can't save anything.
            // and display a nice message.
            $('[name$=cancel]').fadeOut().attr('value','{$mod->Lang('cancel')}').fadeIn();
            $('#form_edittemplate').dirtyForm('option','dirty',false);
            $('#submitbtn, #applybtn').attr('disabled','disabled');
            $('#submitbtn, #applybtn').button({ 'disabled' : true });
            $('.lock-warning').removeClass('hidden-item');
            cms_alert('{$mod->Lang('msg_lostlock')|escape:'javascript'}');
        }
      });
    } // do_locking

    $(document).on('cmsms_textchange',function(event){
        // editor textchange, set the form dirty.
        $('#form_edittemplate').dirtyForm('option','dirty',true);
    });

    $('#form_edittemplate').on('click','[name$=apply],[name$=submit],[name$=cancel]',function(){
        // if we manually click on one of these buttons, the form is no longer considered dirty for the purposes of warnings.
        $('#form_edittemplate').dirtyForm('option','dirty',false);
    });

/*
    $(document).on('click', '#submitbtn, #cancelbtn, #importbtn, #exportbtn', function(ev){
       if( ! do_locking ) return;
       // unlock the item, and submit the form
       var self = this;
       ev.preventDefault();
       var form = $(this).closest('form');
       $('#form_edittemplate').lockManager('unlock').done(function(){
           console.debug('item unlocked');
           var el = $('<input type="hidden"/>');
           el.attr('name',$(self).attr('name')).val($(self).val()).appendTo(form);
           form.submit();
       });
    });
    */

    $(document).on('click', '#applybtn', function(e){
        e.preventDefault();
        var url = $('#form_edittemplate').attr('action')+'?showtemplate=false&m1_apply=1',
        data = $('#form_edittemplate').serializeArray();

        $.post(url, data, function(data,textStatus,jqXHR) {

            var $response = $('<aside/>').addClass('message');
            if (data.status === 'success') {

                $response.addClass('pagemcontainer')
                    .append($('<span>').text('Close').addClass('close-warning'))
                    .append($('<p/>').text(data.message));
            } else if (data.status === 'error') {

                $response.addClass('pageerrorcontainer')
                    .append($('<span>').text('Close').addClass('close-warning'))
                    .append($('<p/>').text(data.message));
            }

            $('body').append($response.hide());
            $response.slideDown(1000, function() {
                window.setTimeout(function() {
                    $response.slideUp();
                    $response.remove();
                }, 10000);
            });

            $('#cancelbtn').button('option','label','{$mod->Lang('cancel')}');
            $('#tpl_modified_cont').hide();
            $('#content').focus();
        });
    });

    $(document).on('click','#a_helptext',function(e){
        e.preventDefault();
	$('#helptext_dlg').dialog({ 'width': 'auto' });
    });
});
</script>

{$helptext=$type_obj->get_template_helptext($type_obj->get_name())}
{if !empty($helptext)}
  <div id="helptext_dlg" title="{$mod->Lang('prompt_template_help')}" style="display: none;">
  {$helptext}
  </div>
{/if}
{$get_lock = $template->get_lock()}

{capture assign='disable'}
    {if isset($get_lock) && ({get_userid(false)} != $get_lock.uid)}disabled="disabled"{/if}
{/capture}

{if isset($get_lock)}
	<div class="warning lock-warning">
		{$mod->Lang('lock_warning')}
	</div>
{/if}

{form_start id="form_edittemplate" extraparms=$extraparms}
<fieldset class="cf">
    <div class="grid_6">
        <div class="pageoverflow">
            <p class="pageinput">
                <input type="submit" id="submitbtn" name="{$actionid}submit" value="{$mod->Lang('submit')}"{$disable|strip} />
                <input type="submit" id="cancelbtn" name="{$actionid}cancel" value="{$mod->Lang('cancel')}" >
                {if $template->get_id()}
                <input type="submit" id="applybtn" name="{$actionid}apply" value="{$mod->Lang('apply')}"{$disable|strip} />
                {/if}
            </p>
        </div>

        <div class="pageoverflow">
            <p class="pagetext"><label for="tpl_name">*{$mod->Lang('prompt_name')}:</label>&nbsp;{cms_help key2=help_template_name title=$mod->Lang('prompt_name')}</p>
            <p class="pageinput">
                <input id="tpl_name" type="text" name="{$actionid}name" size="50" maxlength="90" value="{$template->get_name()}" {if !$has_manage_right}readonly="readonly"{/if} placeholder="{$mod->Lang('new_template')}"/>
            </p>
        </div>

	{$usage_str=$template->get_usage_string()}
	{if !empty($usage_str)}
        <div class="pageoverflow">
            <p class="pagetext"><label>{$mod->Lang('prompt_usage')}:</label>&nbsp;{cms_help key2='help_tpl_usage' title=$mod->Lang('prompt_usage')}</p>
            <p class="pageinput">
                {$usage_str}
            </p>
        </div>
	{/if}

    </div>{* column *}
    <div class="grid_6">
    {if $template->get_id()}
        <div class="pageoverflow">
            <p class="pagetext"><label for="tpl_created">{$mod->Lang('prompt_created')}:</label>&nbsp;{cms_help key2='help_tpl_created' title=$mod->Lang('prompt_created')}</p>
            <p class="pageinput">
                {$template->get_created()|cms_date_format}
            </p>
        </div>
        <div class="pageoverflow" id="tpl_modified_cont">
            <p class="pagetext"><label for="tpl_modified">{$mod->Lang('prompt_modified')}:</label>&nbsp;{cms_help key2='help_tpl_modified' title=$mod->Lang('prompt_modified')}</p>
            <p class="pageinput">
                {$template->get_modified()|cms_date_format}
            </p>
        </div>
    {/if}
    </div>{* column *}
</fieldset>


{tab_header name='template' label=$mod->Lang('prompt_template')}
{tab_header name='description' label=$mod->Lang('prompt_description')}
{if $has_themes_right}
    {tab_header name='designs' label=$mod->Lang('prompt_designs')}
{/if}
{if $has_manage_right}
    {tab_header name='advanced' label=$mod->Lang('prompt_advanced')}
{/if}
{if $template->get_owner_id() == get_userid() or $has_manage_right}
    {tab_header name='permissions' label=$mod->Lang('prompt_permissions')}
{/if}

{tab_start name='template'}
<div class="pageoverflow">
    <p class="pagetext">
      <label for="contents">{$mod->Lang('prompt_template_content')}:</label>&nbsp;{cms_help key2=help_template_contents title=$mod->Lang('prompt_template_content')}
      {if !empty($helptext)}
        <a id="a_helptext" href="#" style="float: right;">{$mod->Lang('prompt_template_help')}</a>
      {/if}
    </p>
    {if $template->has_content_file()}
      <div class="information">{$mod->Lang('info_template_content_file',$template->get_content_filename())}</div>
    {else}
    <p class="pageinput">
        {cms_textarea id='content' prefix=$actionid name=contents value=$template->get_content() type='smarty' rows=20}
    </p>
    {/if}
</div>

{tab_start name='description'}
<div class="pageoverflow">
    <p class="pagetext"><label for="description">{$mod->Lang('prompt_description')}:</label>&nbsp;{cms_help key2=help_template_description title=$mod->Lang('prompt_description')}</p>
    <p class="pageinput">
        <textarea id="description" name="{$actionid}description" {if !$has_manage_right}readonly="readonly"{/if}>{$template->get_description()}</textarea>
    </p>
</div>

{if $has_themes_right}
    {tab_start name='designs'}
    <div class="pageoverflow">
        <p class="pagetext"><label for="designlist">{$mod->Lang('prompt_designs')}:</label>&nbsp;{cms_help key2=help_template_designlist title=$mod->Lang('prompt_designs')}</p>
        <p class="pageinput">
            <select id="designlist" name="{$actionid}design_list[]" multiple="multiple" size="5">
                {html_options options=$design_list selected=$template->get_designs()}
            </select>
        </p>
    </div>
{/if}

{if $has_manage_right}
    {tab_start name='advanced'}
        <div class="pageoverflow">
            <p class="pagetext"><label for="tpl_listable">{$mod->Lang('prompt_listable')}:</label>&nbsp;{cms_help key2=help_template_listable title=$mod->Lang('prompt_listable')}</p>
            <p class="pageinput">
                <select id="tpl_listable" name="{$actionid}listable"{if $type_is_readonly} readonly="readonly"{/if}>
		    {cms_yesno selected=$template->get_listable()}
                </select>
            </p>
        </div>
        {if isset($type_list)}
            <div class="pageoverflow">
                <p class="pagetext"><label for="tpl_type">{$mod->Lang('prompt_type')}:</label>&nbsp;{cms_help key2=help_template_type title=$mod->Lang('prompt_type')}</p>
                <p class="pageinput">
                    <select id="tpl_type" name="{$actionid}type"{if $type_is_readonly} readonly="readonly"{/if}>
                        {html_options options=$type_list selected=$template->get_type_id()}
                    </select>
                </p>
            </div>
            {if $type_obj->get_dflt_flag()}
            <div class="pageoverflow">
                <p class="pagetext"><label for="tpl_dflt">{$mod->Lang('prompt_default')}:</label>&nbsp;{cms_help key2=help_template_dflt title=$mod->Lang('prompt_default')}</p>
                <p class="pageinput">
		    <select id="tpl_dflt" name="{$actionid}default" {if $template->get_type_dflt()}disabled{/if}>
		      {cms_yesno selected=$template->get_type_dflt()}
		    </select>
                </p>
            </div>
            {/if}
        {/if}
        {if isset($category_list)}
        <div class="pageoverflow">
            <p class="pagetext"><label for="tpl_category">{$mod->Lang('prompt_category')}:</label>&nbsp;{cms_help key2=help_template_category title=$mod->Lang('prompt_category')}</p>
            <p class="pageinput">
                <select id="tpl_category" name="{$actionid}category_id">
                    {html_options options=$category_list selected=$template->get_category_id()}
                </select>
            </p>
        </div>
        {/if}
	{if $template->get_id() > 0}
        <div class="pageoverflow">
			<p class="pagetext">{$mod->Lang('prompt_filetemplate')}:</p>
			<p class="pageinput">
			{if $template->has_content_file()}
				<input type="submit" id="importbtn" name="{$actionid}import" value="{$mod->Lang('import')}"/>
			{elseif $template->get_id() > 0}
				<input type="submit" id="exportbtn" name="{$actionid}export" value="{$mod->Lang('export')}"/>
			{/if}
		   </p>
        </div>
	{/if}
{/if}

{if $template->get_owner_id() == get_userid() or $has_manage_right}
    {tab_start name='permissions'}
    {if isset($user_list)}
    <div class="pageoverflow">
        <p class="pagetext"><label for="tpl_owner">{$mod->Lang('prompt_owner')}:</label>&nbsp;{cms_help key2=help_template_owner title=$mod->Lang('prompt_owner')}</p>
        <p class="pageinput">
            <select id="tpl_owner" name="{$actionid}owner_id">
                {html_options options=$user_list selected=$template->get_owner_id()}
            </select>
        </p>
    </div>
    {/if}
    {if isset($addt_editor_list)}
    <div class="pageoverflow">
        <p class="pagetext"><label for="tpl_addeditor">{$mod->Lang('additional_editors')}:</label>&nbsp;{cms_help key2=help_template_addteditors title=$mod->Lang('additional_editors')}</p>
        <p class="pageinput">
            <select id="tpl_addeditor" name="{$actionid}addt_editors[]" multiple="multiple" size="5">
                {html_options options=$addt_editor_list selected=$template->get_additional_editors()}
            </select>
        </p>
    </div>
    {/if}
{/if}
{tab_end}

{form_end}