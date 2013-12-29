<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>{$mod->Lang('filepickertitle')}</title>
  <link rel="stylesheet" type="text/css" href="{$stylesheet_href}"/>
  {cms_jquery}
  <style type="text/css">
  </style>
  <script language="javascript" type="text/javascript">
  $(document).ready(function(){
    $('#linker_cancel').click(function(){
      top.tinymce.activeEditor.windowManager.close();
      return false;
    });

    $('#linker_submit').click(function(){
      var alias = $('#linker_val').val();
      var lbl = $('#linker_text').val();
      if( alias.length == 0 ) {
	// nothing submitted.
	alert('{$mod->Lang('error_nopage')}');
      }
      else {
       {literal}
        var sel = top.tinymce.activeEditor.selection.getContent();
        if( sel.length > 0 ) {
          var txt = '<a href="{cms_selflink href=' + alias + '}">' + sel + '</a>';
          top.tinymce.activeEditor.selection.setContent(txt);
        }
        else {
          var txt = '<a href="{cms_selflink href=' + alias + '}">' + lbl + '</a>';
	  top.tinymce.activeEditor.execCommand( 'mceInsertContent', false, txt );
        }
        top.tinymce.activeEditor.windowManager.close();
        {/literal}
      }
      return false;
    });

    $('#linker_text').autocomplete({
      minLength: 2,
      source: function( request, response ) {
        $.ajax({
          url: '{cms_action_url action='ajax_getpages' forjs=1}&showtemplate=false',
	  dataType: 'json',
          data: {
            term: request.term
          },
          success: function( data ) {
	    response( data );
          },
        });
      },
      focus: function( event, ui ) {
        return false;
      },
      select: function( event, ui ) {
        $('#linker_val').val(ui.item.value);
        $('#linker_text').val(ui.item.label).select();
        return false;
      }
    });
  });
  </script>
</head>
<body>
<div id="page_content" class="cf">
    <div class="pageoverflow">
      <p class="pagetext"><label for="linker_text">{$mod->Lang('prompt_linker')}:</label></p>
      <p class="pageinput">
        <input id="linker_text" type="text" style="width: 100%;"/>
        <br/>
        {$mod->Lang('info_linker_autocomplete')}
      </p>
    </div>
    <div class="pageoverflow">
      <p class="pagetext"><label for="linker_text">{$mod->Lang('prompt_selectedalias')}:</label></p>
      <p class="pageinput">
        <input id="linker_val" type="text" readonly="readonly"/>
      </p>
    </div>
    <div class="pageoverflow" style="float: right;">
      <p class="pagetext"></p>
      <p class="pageinput">
        <input type="submit" id="linker_submit" value="{$mod->Lang('ok')}"/>
        <input type="submit" id="linker_cancel" value="{$mod->Lang('cancel')}"/>
      </p>
    </div>
</div>{* #page_content *}
</body>
</html>