<style type="text/css">
form.dirtyForm { color: salmon; }
</style>

<script type="text/javascript">
// <![CDATA[
$(document).ready(function(){
  // initialize the dirtyform stuff.
  $('#Edit_Content').dirtyForm({
    onDirty: function(){
      $('[name$=apply],[name$=submit]').show('slow');
    },
    beforeUnload: function() {
      $('#Edit_Content').lockManager('unlock');
    }
  });

  // hide sumit and apply buttons, till something changes
  $('[name$=apply],[name$=submit]').hide();
  $('#Edit_Content').on('click','[name$=apply],[name$=submit],[name$=cancel]',function(event){
    $('#Edit_Content :hidden').removeAttr('required');
    $('#Edit_Content').dirtyForm('option','dirty',false);
  });

  // initialize lock manager
{if $content_id > 0}
  $('#Edit_Content').lockManager({
      type: 'content',
      oid: {$content_id},
      uid: {get_userid(FALSE)},
      {if !empty($lock_timeout)}lock_timeout: {$lock_timeout},{/if}
      {if !empty($lock_refresh)}lock_refresh: {$lock_refresh},{/if}
      error_handler: function (err) {
          alert('got error ' + err.type + ' // ' + err.msg);
      },
      lostlock_handler: function (err) {
          // we lost the lock on this content... make sure we can't save anything.
          // and display a nice message.
          $('[name$=apply],[name$=submit]').hide('slow');
          $('[name$=cancel]').fadeOut().attr('value', '{$mod->Lang('close')}').fadeIn();
          $('#Edit_Content').dirtyForm('option', 'dirty', false);
          alert('{$mod->Lang('msg_lostlock')}');
      }
  });
{/if}

{if $content_obj->HasPreview()}
  $('#_preview_').click(function(){
        // serialize the form data
        var data = $('#Edit_Content').find('input:not([type=submit]), select, textarea').serializeArray();
        data.push({
            'name': '{$actionid}preview',
            'value': 1
        });
        data.push({
            'name': '{$actionid}ajax',
            'value': 1
        });
        $.post('{$smarty.server.REQUEST_URI}&showtemplate=false', data, function (resultdata, text) {
            $('#previewframe').attr('src', '{$preview_url}');
        });
    }); 
{/if}

    // here we want to disable the dirtyform stuff when these fields are changed
    $('#content_type').change(function () {
        $('#Edit_Content').dirtyForm('disable');
        $(this).closest('form').submit();
    });
    $('#id_disablewysiwyg').change(function () {
        $('#Edit_Content').dirtyForm('disable');
    });

    // submit the form if template id, and/or content-type fields are changed.
    $('#template_id, #content_type').on('change', function () {
        $(this).closest('form').submit();
    });

    // handle cancel/close ... and unlock
    $(document).on('click', '[name$=cancel]', function () {
        var tmp = $(this).val();
        if (tmp == '{$mod->Lang('close')}') {
            $('#Edit_Content').lockManager('unlock');
            return true;
        } else {
            return confirm('{$mod->Lang('editcontent_confirm_cancel')}');
        }
    });

    // handle apply (ajax submit)
    $(document).on('click', '[name$=apply]', function () {
        var data = $('#Edit_Content').find('input:not([type=submit]), select, textarea').serializeArray();
        data.push({
            'name': '{$actionid}ajax',
            'value': 1
        });
        data.push({
            'name': '{$actionid}apply',
            'value': 1
        });
        $.post('{$smarty.server.REQUEST_URI}&showtemplate=false', data, function (data, text) {
            var event = $.Event('cms_ajax_apply');
            event.response = data.response;
            event.details = data.details;
            event.close = 'close';
            $('body').trigger(event);
        }, 'json');
        return false;
    });
    jQuery('body').on('cms_ajax_apply', function (e) {
        var htmlShow = '';
        if (e.response == 'Success') {
            // here we could fire a custom event, give the details and let something else handle it.
            htmlShow = '<div class="pagemcontainer"><p class="pagemessage">' + e.details + '<\/p><\/div>';
            $('[name$=cancel]').fadeOut();
            $('[name$=cancel]').attr('value', '{$mod->Lang('close')}');
            $('[name$=cancel]').fadeIn();
        } else {
            htmlShow = '<div class="pageerrorcontainer"><ul class="pageerror">';
            htmlShow += e.details;
            htmlShow += '<\/ul><\/div>';
        }
        jQuery('#Edit_Content_Result').html(htmlShow);
    });
});
// ]]>
</script>

{$extra_content|default:''}

{if $content_id < 1}
    <h3>{$mod->Lang('prompt_editpage_addcontent')}</h3>
{else}
    <h3>{$mod->Lang('prompt_editpage_editcontent')}&nbsp;<em>({$content_id})</em></h3>
{/if}

{function submit_buttons}
<p class="pagetext"></p>
<p class="pageinput">
  <input type="submit" name="{$actionid}submit" value="{$mod->Lang('submit')}" class="pagebutton" title="{$mod->Lang('title_editpage_submit')}"/>
  <input type="submit" name="{$actionid}cancel" formnovalidate value="{$mod->Lang('cancel')}" class="pagebutton" title="{$mod->Lang('title_editpage_cancel')}"/>
  {if $content_id != ''}
    <input type="submit" name="{$actionid}apply" value="{$mod->Lang('apply')}" class="pagebutton" title="{$mod->Lang('title_editpage_apply')}"/>
  {/if}
  {if $content_obj->IsViewable() && $content_obj->Active()}
    <a rel="external" href="{$content_obj->GetURL()}" title="{$mod->Lang('title_editpage_view')}">{admin_icon icon='view.gif' alt='view_page'|lang}</a>
  {/if}
</p>
{/function}

<div id="Edit_Content_Result"></div>
<div id="Edit_Content">
{form_start content_id=$content_id}
  {submit_buttons}

  {* tab headers *}
  {foreach from=$tab_names item='tabname' name='tabs'}
    {assign var='tmp' value='tab_'|cat:$smarty.foreach.tabs.index}
    {tab_header name=$tmp label=$tabname}
  {/foreach}
  {if $content_obj->HasPreview()}
    {tab_header name='_preview_' label=$mod->Lang('prompt_preview')}
  {/if}

  {* tab content *}
  {foreach from=$tab_names item='tabname' name='tabs'}
    {assign var='idx' value=$smarty.foreach.tabs.index}
    {assign var='tmp' value='tab_'|cat:$idx}
    {tab_start name=$tmp}
      {if isset($tab_message_array[$idx])}{$tab_message_array[$idx]}{/if}

      {if isset($tab_contents_array[$idx])}
        {foreach from=$tab_contents_array[$idx] item='fld'}
        <div class="pageoverflow">
          <p class="pagetext">{$fld[0]}</p>
          <p class="pageinput">{$fld[1]}{if count($fld) == 3}<br/>{$fld[2]}{/if}</p>
        </div>
        {/foreach}
      {/if}
  {/foreach}
  {if $content_obj->HasPreview()}
    {tab_start name='_preview_'}
      <div class="pagewarning">{$mod->Lang('info_preview_notice')}</div>
      <iframe name="_previewframe_" class="preview" id="previewframe"></iframe>
  {/if}
  {tab_end}
{form_end}
</div>{* #Edit_Content *}