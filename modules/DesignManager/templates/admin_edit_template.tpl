<script type="text/javascript">
$(document).ready(function(){
    $('#form_edittemplate').dirtyForm({
        beforeUnload: function() {
            $('#form_edittemplate').lockManager('unlock');
        }
    });

    $(document).on('cmsms_textchange',function(event){
        // editor textchange, set the form dirty.
        $('#form_edittemplate').dirtyForm('option','dirty',true);
    });

    // initialize lock manager {if isset($tpl_id)}

    $('#form_edittemplate').lockManager({
        type: 'template',
        oid: {$tpl_id},
        uid: {get_userid(FALSE)},
        lock_timeout: {$lock_timeout},
        lock_refresh: {$lock_refresh},
        error_handler: function(err) {
            alert('got error '+err.type+' // '+err.msg);
        },
        lostlock_handler: function(err) {
            // we lost the lock on this content... make sure we can't save anything.
            // and display a nice message.
            $('[name$=cancel]').fadeOut().attr('value','{$mod->Lang('cancel')}').fadeIn();
            $('#form_edittemplate').dirtyForm('option','dirty',false);
            alert('{$mod->Lang('msg_lostlock')|escape:'javascript'}');
        }
    });
    // {/if}

    $('#form_edittemplate').on('click','[name$=apply],[name$=submit]',function(){
        $('#form_edittemplate').dirtyForm('option','dirty',false);
    });

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

            $('body').append($response).slideDown(1000, function() {
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
});
</script>

{$get_lock = $template->get_lock()}
{capture assign='disable'}
    {if isset($get_lock) && ({get_userid(false)} != $get_lock.uid)}disabled="disabled"{/if}
{/capture}

{if !$template->get_id()}
    <h3>{$mod->Lang('create_template')}</h3>
{else}
    <h3>{$mod->Lang('edit_template')}: {$template->get_name()} ({$template->get_id()})</h3>
{/if}

{if isset($get_lock) && ({get_userid(false)} != $get_lock.uid)}
<div class="warning">
    {$mod->Lang('lock_warning')}
</div>
{/if}

{form_start id="form_edittemplate" extraparms=$extraparms}
<fieldset class="c_full cf">
    <div class="grid_6">
        <div class="pageoverflow">
            <p class="pageinput">
                <input type="submit" name="{$actionid}submit" value="{$mod->Lang('submit')}" />
                <input type="submit" id="cancelbtn" name="{$actionid}cancel" value="{$mod->Lang('cancel')}" >
                {if $template->get_id()}
                <input type="submit" id="applybtn" name="{$actionid}apply" value="{$mod->Lang('apply')}" />
                {/if}
            </p>
        </div>

        <div class="pageoverflow">
            <p class="pagetext"><label for="tpl_name">*{$mod->Lang('prompt_name')}:</label>&nbsp;{cms_help key2=help_template_name title=$mod->Lang('prompt_name')}</p>
            <p class="pageinput">
                <input id="tpl_name" type="text" name="{$actionid}name" size="50" maxlength="90" value="{$template->get_name()}" {if !$has_manage_right}readonly="readonly"{/if} placeholder="{$mod->Lang('new_template')}"/>
            </p>
        </div>
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
<!-- tmeplate -->
<div class="pageoverflow">
    <p class="pagetext"><label for="contents">{$mod->Lang('prompt_template')}:</label>&nbsp;{cms_help key2=help_template_contents title=$mod->Lang('prompt_template')}</p>
    <p class="pageinput">
        {cms_textarea id='content' prefix=$actionid name=contents value=$template->get_content() type='smarty' rows=20 cols=80}
    </p>
</div>

{tab_start name='description'}
<!-- description -->
<div class="pageoverflow">
    <p class="pagetext"><label for="description">{$mod->Lang('prompt_description')}:</label>&nbsp;{cms_help key2=help_template_description title=$mod->Lang('prompt_description')}</p>
    <p class="pageinput">
        <textarea id="description" name="{$actionid}description" {if !$has_manage_right}readonly="readonly"{/if}>{$template->get_description()}</textarea>
    </p>
</div>

{if $has_themes_right}
    {tab_start name='designs'}
    <!-- designs -->
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
    <!-- advanced -->
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
                    <input type="hidden" name="{$actionid}default" value="{if $template->get_type_dflt()}1{else}0{/if}"/>
                    <input id="tpl_dflt" type="checkbox" name="{$actionid}default" value="1" {if $template->get_type_dflt()}checked="checked" disabled="disabled"{/if}/>
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
{/if}

{if $template->get_owner_id() == get_userid() or $has_manage_right}
    {tab_start name='permissions'}
    <!-- permissions -->
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