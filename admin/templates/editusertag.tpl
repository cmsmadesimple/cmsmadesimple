<script type="text/javascript">
// <![CDATA[
$(document).ready(function(){
  $('#runbtn').button({
     icons: { primary: 'ui-icon-gear' }
  });
  $(document).on('click', '#runbtn', function(ev){
    // get the data
    ev.preventDefault();
    cms_confirm('{lang('confirm_runusertag')|strip|escape:'quotes'}').done(function(){
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
      	    var r,d,e;
	    try {
	        var x = $.parseJSON(resultdata);
		if( typeof x.response != 'undefined' ) {
		    r = x.response;
		    d = x.details;
	        } else {
		    d = resultdata;
		}
           } catch( e ) {
	       r = '_error';
	       d = resultdata;
	   }

	   e = $('<div />').text(d).html(); // quick tip for entity encoding.
	   if( r = '_error' ) e = d;
	   $('#edit_userplugin_runout').html(e);
	   $('#edit_userplugin_runout').dialog({ modal: true, width: 'auto' });
        });
    	return false;
    }).fail(function(){
       return false;
    })
  });

  $(document).on('click', '#applybtn', function(){
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
					<input id="submitme" type="submit" name="submit" value="{lang('submit')}" />
					<input type="submit" name="cancel" value="{lang('cancel')}" />
					{if $record.userplugin_id != ''}
						<input id="applybtn" type="submit" name="apply" value="{lang('apply')}" title="{lang('title_applyusertag')}" />
						<button id="runbtn" type="submit" name="run" title="{lang('runuserplugin')}"/>{lang('run')}</button>
					{/if}
				</p>
			</div>
			<div class="pageoverflow">
				<p class="pagetext">
					<label for="name">{lang('name')}:&nbsp;{cms_help key1=h_udtname title=lang('name')}</label>
				</p>
				<p class="pageinput">
					<input type="text" id="name" name="userplugin_name" value="{$record.userplugin_name}" size="50" maxlength="50" />
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
		<div class="pageoverflow">
			<p class="pagetext">
				<label for="code"><b>{lang('code')}:</b></label>&nbsp;{cms_help key1=h_udtcode title=lang('code')}
			</p>
			<p class="pageinput">
				{cms_textarea id='udtcode' name='code' value=$record.code wantedsyntax=php rows=10 cols=80}
			</p>
		</div>
		
	{tab_start name='description'}
		<div class="pageoverflow">
			<p class="pagetext">
				<label for="description">{lang('description')}:</label>&nbsp;{cms_help key1=h_udtdesc title=lang('description')}
			</p>
			<p class="pageinput">
				<textarea id="description" name="description" rows="3" cols="80">{$record.description}</textarea>
			</p>
		</div>
	{tab_end}

{form_end}

</div>