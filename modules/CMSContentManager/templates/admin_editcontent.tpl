<script type="text/javascript">
// <![CDATA[
$(document).ready(function(){ 
  {if $content_obj->HasPreview()}
  $('#_preview_').click(function(){
    // serialize the form data
    {$wysiwyg_submit_script|default:''}
    var data = $('#Edit_Content').find('input:not([type=submit]), select, textarea').serializeArray();
    data.push({ 'name': 'preview', 'value': 1});
    data.push({ 'name': 'ajax', 'value': 1});
    $.post('{$smarty.server.REQUEST_URI}&showtemplate=false',data,function(resultdata,text){
        $('#previewframe').attr('src','{$preview_url}');
    });
  });
  {/if}

  $('#template_id, #content_type').live('change',function(){
    $(this).closest('form').submit();
  });

  $('[name=cancel]').click(function(){
    var tmp = $(this).val();
    if( tmp == '{$mod->Lang('close')}' ) {
      return true;
    }
    else {
      return confirm('{$mod->Lang('cancel')}');
    }
  });

  $('[name=apply]').live('click',function(){
    {$wysiwyg_submit_script|default:''}
    var data = $('#Edit_Content').find('input:not([type=submit]), select, textarea').serializeArray();
    data.push({ 'name': 'ajax', 'value': 1});
    data.push({ 'name': 'apply', 'value': 1 });
    $.post('{$smarty.server.REQUEST_URI}&showtemplate=false',data,function(data,text){
       var event = $.Event('cms_ajax_apply');
       event.response = data.response;
       event.details = data.details;
       event.close = 'close';
       $('body').trigger(event);
    },'json');
    return false;
  });

  jQuery('body').on('cms_ajax_apply',function(e){
    var htmlShow = '';
    if( e.response == 'Success' ) {
      // here we could fire a custom event, give the details and let something else handle it.
      htmlShow = '<div class="pagemcontainer"><p class="pagemessage">' + e.details + '<\/p><\/div>';
      jQuery('[name=cancel]').fadeOut();
      jQuery('[name=cancel]').attr('value','{$mod->Lang('close')}');
      jQuery('[name=cancel]').fadeIn();
    }
    else {
      htmlShow = '<div class="pageerrorcontainer"><ul class="pageerror">';
      htmlShow += e.details;
      htmlShow += '<\/ul><\/div>';
    }
    jQuery('#Edit_Content_Result').html(htmlShow);
  });
});
// ]]>
</script>

{if $content_id < 1}
<h3>{$mod->Lang('prompt_editpage_addcontent')}</h3>
{else}
<h3>{$mod->Lang('prompt_editpage_editcontent')}&nbsp;<em>({$content_id})</em></h3>
{/if}

{function submit_buttons}
<p class="pagetext"></p>
<p class="pageinput">
  <input type="submit" name="{$actionid}submit" value="{$mod->Lang('submit')}" class="pagebutton" title="{$mod->Lang('title_editpage_submit')}"/>
  <input type="submit" name="{$actionid}cancel" value="{$mod->Lang('cancel')}" class="pagebutton" title="{$mod->Lang('title_editpage_cancel')}"/>
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
      {if $idx == 0}{submit_buttons}{/if}
      {if isset($tab_message_array[$idx])}{$tab_message_array[$idx]}{/if}

      {if isset($tab_contents_array[$idx])}
        {foreach from=$tab_contents_array[$idx] item='fld'}
        <div class="pageoverflow">
          <p class="pagetext">{$fld[0]}</p>
          <p class="pageinput">{$fld[1]}{if count($fld) == 3}<br/>{$fld[2]}{/if}</p>
        </div>
        {/foreach}
      {/if}
      {submit_buttons}
  {/foreach}
  {if $content_obj->HasPreview()}
    {tab_start name='_preview_'}
      <div class="pagewarning">{$mod->Lang('info_preview_notice')}</div>
      <iframe name="_previewframe_" class="preview" id="previewframe"></iframe>
  {/if}
  {tab_end}
{form_end}
</div>{* #Edit_Content *}