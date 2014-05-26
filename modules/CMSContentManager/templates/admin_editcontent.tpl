<style type="text/css">
form.dirtyForm { color: salmon; }
</style>

<script type="text/javascript">
// <![CDATA[
$(document).ready(function(){
  // initialize the dirtyform stuff.
  $('#Edit_Content').dirtyForm({
    beforeUnload: function() {
      {if isset($lock_timeout) && $lock_timeout > 0}$('#Edit_Content').lockManager('unlock');{/if}
    }
  });

{if $content_id > 0}
  {if isset($lock_timeout) && $lock_timeout > 0}
  // initialize lock manager
  $('#Edit_Content').lockManager({
      type: 'content',
      oid: {$content_id},
      uid: {get_userid(FALSE)},
      {if !empty($lock_timeout) && $lock_timeout > 0}lock_timeout: {$lock_timeout},{/if}
      {if !empty($lock_refresh) && $lock_timeout > 0}lock_refresh: {$lock_refresh},{/if}
      error_handler: function (err) {
          alert('got error ' + err.type + ' // ' + err.msg);
      },
      lostlock_handler: function (err) {
          // we lost the lock on this content... make sure we can't save anything.
          // and display a nice message.
          $('[name$=cancel]').fadeOut().attr('value', '{$mod->Lang('close')}').fadeIn();
          $('#Edit_Content').dirtyForm('option', 'dirty', false);
          alert('{$mod->Lang('msg_lostlock')}');
      }
  });
  {/if}
{/if}

{if $content_obj->HasPreview()}
  $('#_preview_').click(function(){
      if( typeof tinyMCE != 'undefined') tinyMCE.triggerSave();
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
        $.post('{$preview_ajax_url}&showtemplate=false', data, function (resultdata, text) {
            if( resultdata != null && resultdata.response == 'Error' ) {
	        $('#previewframe').attr('src','').hide();
                $('#preview_errors').html('<ul></ul>');
	        for( var i = 0; i < resultdata.details.length; i++ ) {
                  $('#preview_errors').append('<li>'+resultdata.details[i]+'</li>');
                }
                $('#previewerror').show();
            }
            else {
		var x = new Date().getTime();
	        var url = '{$preview_url}&junk='+x;
	        $('#previewerror').hide();
                $('#previewframe').attr('src', url).show();
            }
        },'json');
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
    $('#template_id').change(function(event){
        $('#Edit_Content').dirtyForm('disable');
    });

    // submit the form if template id, and/or content-type fields are changed.
    $('#template_id, #content_type').on('change', function () {
        $(this).closest('form').submit();
    });

    // handle cancel/close ... and unlock
    $(document).on('click', '[name$=cancel]', function () {
        var dirty = $('#Edit_Content').dirtyForm('option','dirty');
        var tmp = $(this).val();
        if (tmp == '{$mod->Lang('close')}') {
	  {if isset($lock_timeout) && $lock_timeout > 0}$('#Edit_Content').lockManager('unlock');{/if}
	}
        return true;
    });

    $('#Edit_Content').on('click','[name$=apply],[name$=submit],[name$=cancel]',function(event){
      $('#Edit_Content :hidden').removeAttr('required');
    });

    // handle apply (ajax submit)
    $(document).on('click', '[name$=apply]', function () {
        if( typeof tinyMCE != 'undefined') tinyMCE.triggerSave(); // TODO this needs better approach, create a common "ajax save" function that can be reused
        var data = $('#Edit_Content').find('input:not([type=submit]), select, textarea').serializeArray();
        data.push({
            'name': '{$actionid}ajax',
            'value': 1
        });
        data.push({
            'name': '{$actionid}apply',
            'value': 1
        });
        $.post('{$apply_ajax_url}&showtemplate=false', data, function (data, text) {
            var event = $.Event('cms_ajax_apply');
	    event.response = data.response;
	    event.details = data.details;
	    event.close = '{$mod->Lang('close')}';
	    if( typeof data.url != '' ) event.url = data.url;
            $('body').trigger(event);
        }, 'json');
            return false;
    });

    $(document).on('cms_ajax_apply',function(e){
      $('#Edit_Content').dirtyForm('option','dirty',false);
      if( typeof e.url != '' && typeof e.url != undefined ) {
        $('a#viewpage').attr('href',e.url);
      }
    });

    {if isset($designchanged_ajax_url)}
    $('#design_id').change(function(){
      var v = $(this).val();
      var data = { '{$actionid}design_id': v };
      $.get('{$designchanged_ajax_url}',data,function(data,text) {
        if( typeof data == 'object' ) {
	  var sel = $('#template_id').val();
          $('#template_id').empty();
          for( key in data ) {
	    $('#template_id').append('<option value="'+key+'">'+data[key]+'</option>');
	  }
	  $('#template_id').val(sel);
        }
      }, 'json' );
    });

    $('#design_id').trigger('change');
    $('#Edit_Content').dirtyForm('option','dirty',false);
    {/if}
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
    <a id="viewpage" rel="external" href="{$content_obj->GetURL()}" title="{$mod->Lang('title_editpage_view')}">{admin_icon icon='view.gif' alt='view_page'|lang}</a>
  {/if}
</p>
{/function}

<div id="Edit_Content_Result"></div>
<div id="Edit_Content">
{form_start content_id=$content_id}
  {submit_buttons}

  {* tab headers *}
  {foreach $tab_names as $key => $tabname}
    {tab_header name=$key label=$tabname}
  {/foreach}
  {if $content_obj->HasPreview()}
    {tab_header name='_preview_' label=$mod->Lang('prompt_preview')}
  {/if}

  {* tab content *}
  {foreach $tab_names as $key => $tabname}
    {tab_start name=$key}
      {if isset($tab_message_array[$key])}{$tab_message_array[$key]}{/if}

      {if isset($tab_contents_array[$key])}
        {foreach $tab_contents_array.$key as $fld}
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
      <div id="previewerror" class="red" style="display: none; color: #000;">
        <fieldset>
          <legend>LEGEND</legend>
          <ul id="preview_errors"></ul>
        </fieldset>
      </div>
  {/if}
  {tab_end}
{form_end}
</div>{* #Edit_Content *}