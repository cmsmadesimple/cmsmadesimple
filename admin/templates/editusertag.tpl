<script type="text/javascript">
// <![CDATA[
$(document).ready(function(){  
  $('#runbtn').live('click',function(){
    // get the data
    if( !confirm('{lang('confirm_runusertag')}') ) return false;
    {$syntax_module_submit_js|default:''}

    var code = $('#udtcode').val();
    if( code.length == 0 ) {
      var d = '{lang('noudtcode')}';
      txt = '<div class="pageerrorcontainer"><ul class="pageerror">' + d + '<\/ul><\/div>';
      $('#edit_userplugin_result').html( txt );
      return false;
    }
    var data = $('#edit_userplugin').find('input:not([type=submit]), select, textarea').serializeArray();
    data.push({ 'name': 'code', 'value': code });
    data.push({ 'name': 'run', 'value': 1 });
    data.push({ 'name': 'apply', 'value': 1 });
    data.push({ 'name': 'ajax', 'value': 1 });
    $.post('{$smarty.server.REQUEST_URI}',data,function(resultdata,text) {
      var x = $.parseJSON(resultdata);
      var r = x.response;
      var d = x.details;

      var e = $('<div />').text(d).html(); // quick tip for entity encoding.
      $('#edit_userplugin_runout').html(e);
      $('#edit_userplugin_runout').dialog({ modal: true });
    });
    return false;
  });

  $('#applybtn').live('click',function(){
    {$syntax_module_submit_js|default:''}

    var data = $('#edit_userplugin').find('input:not([type=submit]), select, textarea').serializeArray();
    data.push({ 'name': 'ajax', 'value': 1 });
    data.push({ 'name': 'apply', 'value': 1 });

    $.post('{$smarty.server.REQUEST_URI}',data,function(resultdata,text) {
      var x = $.parseJSON(resultdata);
      var r = x.response;
      var d = x.details;
      var txt = '';
      if( r == 'Success' ) {
        txt = '<div class="pagemcontainer"><p class="pagemcessage">' + d + '<\/p><\/div>';
        $('[name=cancel]').fadeOut();
        $('[name=cancel]').attr('value','{lang('close')}');
        $('[name=cancel]').button('option','label','{lang('close')}');
        $('[name=cancel]').fadeIn();
      }
      else {
        txt = '<div class="pageerrorcontainer"><ul class="pageerror">' + d + '<\/ul><\/div>';
      }
      $('#edit_userplugin_result').html( txt );
    });
    return false;
  });
});
//]]>
</script>

<div class="pagecontainer">
{if $record.userplugin_id == ''}
<h3>{lang('addusertag')}</h3>
{else}
<h3>{lang('editusertag')}</h3>
{/if}

<div id="edit_userplugin_runout" title="{lang('output')}" style="display: none;"></div>
<div id="edit_userplugin_result"></div>

{form_start url='editusertag.php' id='edit_userplugin' userplugin_id=$record.userplugin_id}
<fieldset>
  <div style="width: 49%; float: left;">
    <div class="pageoverflow">
      <p class="pagetext"></p>
      <p class="pageinput">
        <input id="submitme" type="submit" name="submit" value="{lang('submit')}"/>
        {if $record.userplugin_id != ''}
        <input id="applybtn" type="submit" name="apply" value="{lang('apply')}" title="{lang('title_applyusertag')}"/>
        <input id="runbtn" type="submit" name="run" value="{lang('run')}" title="{lang('runuserplugin')}"/>
        {/if}
        <input type="submit" name="cancel" value="{lang('cancel')}"/>
      </p>
    </div>

    <div class="pageoverflow">
      <p class="pagetext"><label for="name">{lang('name')}</label>:&nbsp;{cms_help key1=h_udtname}</p>
      <p class="pageinput">
        <input type="text" id="name" name="userplugin_name" value="{$record.userplugin_name}" size="50" maxlength="50"/>
      </p>
    </div>
  </div>

  <div style="width: 49%; float: right;">
    {if $record.create_date != ''}
    <div class="pageoverflow">
      <p class="pagetext">{lang('created_at')}:</p>
      <p class="pageinput">{$record.create_date|cms_date_format}</p>
    </div>
    {/if}

    {if $record.modified_date != ''}
    <div class="pageoverflow">
      <p class="pagetext">{lang('last_modified_at')}:</p>
      <p class="pageinput">{$record.modified_date|cms_date_format}</p>
    </div>
    {/if}
  </div>

</fieldset>
{tab_header name='code' label=lang('code')}
{tab_header name='description' label=lang('description')}

{tab_start name='code'}
<label for="code">{lang('code')}:</label>&nbsp;{cms_help key1=h_udtcode}<br/>
{cms_textarea id='udtcode' name='code' content=$record.code syntax=1}

{tab_start name='description'}
<label for="description">{lang('description')}:</label>&nbsp;{cms_help key1=h_udtdesc}
<br/>
<textarea id="description" name="description">{$record.description}</textarea>
{tab_end}
{form_end}

<p class="pageback">
  <a class="pageback" href="{$backurl}">&#171; {lang('back')}</a>
</p>
</div>