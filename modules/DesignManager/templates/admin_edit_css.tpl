<script type="text/javascript">
$(document).ready(function(){
    var do_locking = {if $css_id|default:0 > 0 && isset($lock_timeout) && $lock_timeout > 0}1{else}0{/if};
    $('#form_editcss').dirtyForm({
        beforeUnload: function() {
            if( do_locking )$('#form_editcss').lockManager('unlock');
        },
        unloadCancel: function() {
            if( do_locking )$('#form_editcss').lockManager('relock');
        }
    });

    // initialize lock manager
    if( do_locking ) {
      $('#form_editcss').lockManager({
        type: 'stylesheet',
        oid: {$css_id|default:0},
        uid: {get_userid(FALSE)},
        lock_timeout: {$lock_timeout|default:0},
        lock_refresh: {$lock_refresh|default:0},
        error_handler: function(err) {
            cms_alert('got error '+err.type+' // '+err.msg);
        },
        lostlock_handler: function(err) {
            // we lost the lock on this stylesheet... make sure we can't save anything.
            // and display a nice message.
        console.debug('lost lock handler');
            $('[name$=cancel]').fadeOut().attr('value','{$mod->Lang('cancel')}').fadeIn();
            $('#form_editcss').dirtyForm('option','dirty',false);
            $('#submitbtn, #applybtn').attr('disabled','disabled');
            $('#submitbtn, #applybtn').button({ 'disabled' : true });
            $('.lock-warning').removeClass('hidden-item');
            cms_alert('{$mod->Lang('msg_lostlock')|escape:'javascript'}');
        }
      });
    }

    $(document).on('cmsms_textchange',function(event){
        // editor textchange, set the form dirty.
        $('#form_editcss').dirtyForm('option','dirty',true);
    });

    $(document).on('click','[name$=apply],[name$=submit]',function(){
        $('#form_editcss').dirtyForm('option','dirty',false);
    });

    $(document).on('click', '#submitbtn, #cancelbtn, #importbtn, #exportbtn', function(ev){
       if( ! do_locking ) return;

       // unlock the item, and submit the form
       var self = this;
       ev.preventDefault();
       var form = $(this).closest('form');
       $('#form_editcss').lockManager('unlock').done(function(){
           var el = $('<input type="hidden"/>');
           el.attr('name',$(self).attr('name')).val($(self).val()).appendTo(form);
           form.submit();
       });
    });

    $(document).on('click', '#applybtn', function(e){
        e.preventDefault();

        var url = $('#form_editcss').attr('action')+'?showtemplate=false&m1_apply=1',
            data = $('#form_editcss').serializeArray();

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

    // disabling Media Type checkboxes if Media query is in use
    if ($('#mediaquery').val() !== '') {
        $('.media-type :checkbox').attr({
            disabled: 'disabled',
            checked: false
        });
    }

    $('#mediaquery').keyup(function(e) {
        if ($('#mediaquery').val() !== '') {
            $('.media-type :checkbox').attr({
                disabled: 'disabled',
                checked: false
            });
        } else {
            $('.media-type:checkbox').removeAttr('disabled');
        }
    });
});
</script>


{$get_lock = $css->get_lock()}
{capture assign='disable'}
    {if isset($get_lock) && ({get_userid(false)} != $get_lock.uid)}disabled="disabled"{/if}
{/capture}

{*
{if !$css->get_id()}
    <h3>{$mod->Lang('create_stylesheet')}</h3>
{else}
    <h3>{$mod->Lang('edit_stylesheet')}: {$css->get_name()} ({$css->get_id()})</h3>
{/if}
*}

{if isset($get_lock)}
    <div class="warning lock-warning">
        {$mod->Lang('lock_warning')}
    </div>
{/if}

{form_start id='form_editcss' extraparms=$extraparms}
<fieldset class="cf">
    <div class="grid_6">
        <div class="pageoverflow">
            <p class="pageinput">
                <input type="submit" id="submitbtn" name="{$actionid}submit" value="{$mod->Lang('submit')}"{$disable|strip}/>
                <input type="submit" id="cancelbtn" name="{$actionid}cancel" value="{$mod->Lang('cancel')}"/>
                {if $css->get_id()}
                <input type="submit" id="applybtn" name="{$actionid}apply" value="{$mod->Lang('apply')}"{$disable|strip}/>
                {/if}
            </p>
        </div>
        <div class="pageoverflow">
            <p class="pagetext"><label for="css_name">*{$mod->Lang('prompt_name')}:</label>&nbsp;{cms_help key2=help_stylesheet_name title=$mod->Lang('prompt_name')}</p>
            <p class="pageinput">
                <input id="css_name" type="text" name="{$actionid}name" size="50" maxlength="90" value="{$css->get_name()}" placeholder="{$mod->Lang('new_stylesheet')}"/>
            </p>
        </div>
    </div>{* column *}
    <div class="grid_6">
    {if $css->get_id()}
        <div class="pageoverflow">
            <p class="pagetext"><label for="css_created">{$mod->Lang('prompt_created')}:</label>&nbsp;{cms_help key2=help_stylesheet_created title=$mod->Lang('prompt_created')}</p>
            <p class="pageinput">
                {$css->get_created()|localedate_format:'%x %X'}
            </p>
        </div>
        <div class="pageoverflow">
            <p class="pagetext"><label for="css_modified">{$mod->Lang('prompt_modified')}:</label>&nbsp;{cms_help key2=help_stylesheet_modified title=$mod->Lang('prompt_modified')}</p>
            <p class="pageinput">
                {$css->get_modified()|localedate_format:'%x %X'}
            </p>
        </div>
    {/if}
    </div>{* column *}
</fieldset>

{tab_header name='content' label=$mod->Lang('prompt_stylesheet')}
{tab_header name='media_type' label=$mod->Lang('prompt_media_type')}
{tab_header name='media_query' label=$mod->Lang('prompt_media_query')}
{tab_header name='description' label=$mod->Lang('prompt_description')}
{if $has_designs_right}
    {tab_header name='designs' label=$mod->Lang('prompt_designs')}
{/if}
{tab_header name='advanced' label=$mod->Lang('prompt_advanced')}

{tab_start name='content'}
{if $css->has_content_file()}
  <div class="information">{$mod->Lang('info_css_content_file',$css->get_content_filename())}</div>
{else}
  <div class="pageoverflow">
      <p class="pagetext"><label for="stylesheet">{$mod->Lang('prompt_stylesheet')}:</label>&nbsp;{cms_help key2=help_stylesheet_content title=$mod->Lang('prompt_stylesheet')}</p>
      <p class="pageinput">
          {cms_textarea id='stylesheet' prefix=$actionid name=content value=$css->get_content() type=css rows=20 cols=80}
      </p>
  </div>
{/if}

{tab_start name='media_type'}
<!-- media -->
<div class="pagewarning">{$mod->Lang('info_editcss_mediatype_tab')}</div>
<div class="pageoverflow">
    <p class="patetext">{$mod->Lang('prompt_media_type')}:</p>
    {assign var='tmp' value='all,aural,speech,braille,embossed,handheld,print,projection,screen,tty,tv'}
    {assign var='all_types' value=explode(',',$tmp)}

    <p class="pageinput media-type">
    {foreach from=$all_types item='type' name='media_type'}{strip}
        <input id="media_type_{$type}" type="checkbox" name="{$actionid}media_type[]" value="{$type}" {if $css->has_media_type($type)}checked="checked"{/if}/>
        &nbsp;
        {assign var='tmp' value='media_type_'|cat:$type}
            <label for="media_type_{$type}">{$mod->Lang($tmp)}</label>
            {if !$smarty.foreach.media_type.last}<br/>{/if}
    {/strip}{/foreach}
    </p>
</div>

{tab_start name='media_query'}
<div class="pagewarning">{$mod->Lang('info_editcss_mediaquery_tab')}</div>
<div class="pageoverflow">
    <p class="pagetext"><label for="mediaquery">{$mod->Lang('prompt_media_query')}:</label>&nbsp;{cms_help key2=help_css_mediaquery title=$mod->Lang('prompt_media_query')}</p>
    <p class="pageinput">
        <textarea id="mediaquery" name="{$actionid}media_query" rows="10" cols="80">{$css->get_media_query()}</textarea>
    </p>
</div>

{tab_start name='description'}
<div class="pageoverflow">
    <p class="pagetext"><label for="txt_description">{$mod->Lang('prompt_description')}:</label>&nbsp;{cms_help key2=help_css_description title=$mod->Lang('prompt_description')}</p>
    <p class="pageinput">
        <textarea id="txt_description" name="{$actionid}description" rows="10" cols="80">{$css->get_description()}</textarea>
    </p>
</div>

{if $has_designs_right}
    {tab_start name='designs'}
    <!-- designs -->
    <div class="pageoverflow">
        <p class="pagetext"><label for="designlist">{$mod->Lang('prompt_designs')}:</label>&nbsp;{cms_help key2=help_css_designs title=$mod->Lang('prompt_designs')}</p>
        <p class="pageinput">
            <select id="designlist" name="{$actionid}design_list[]" multiple="multiple" size="5">
                {html_options options=$design_list selected=$css->get_designs()}
            </select>
        </p>
    </div>
{/if}

{tab_start name='advanced'}
{if $css->get_id() > 0}
  <div class="pageoverflow">
    <p class="pagetext">{$mod->Lang('prompt_cssfile')}:</p>
    <p class="pageinput">
        {if $css->has_content_file()}
            <input type="submit" id="importbtn" name="{$actionid}import" value="{$mod->Lang('import')}"/>
        {else}
            <input type="submit" id="exportbtn" name="{$actionid}export" value="{$mod->Lang('export')}"/>
        {/if}
    </p>
  </div>
{/if}
{tab_end}

{form_end}
