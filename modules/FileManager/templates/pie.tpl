<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <title>Image Editor</title>

    <link rel="stylesheet" type="text/css" href="{root_url}/modules/FileManager/js/jrac/style.jrac.css" />
    <link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.7/themes/base/jquery-ui.css" />

    <style>
      .pane > * {
          float: left;
      }
    </style>

    <script type="text/javascript" src="http://code.jquery.com/jquery-1.6.2.js"></script>
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.7/jquery-ui.js"></script>
    <script type="text/javascript" src="{root_url}/modules/FileManager/js/jrac/jquery.jrac.js"></script>

        <!-- SHJS - Syntax Highlighting for JavaScript -->
    <script type="text/javascript" src="http://shjs.sourceforge.net/sh_main.min.js"></script>
    <script type="text/javascript" src="http://shjs.sourceforge.net/lang/sh_javascript.min.js"></script>
    <link type="text/css" rel="stylesheet" href="http://shjs.sourceforge.net/sh_style.css" />
    <script type="text/javascript">$(document).ready(function(){ sh_highlightDocument(); });</script>

    <script type="text/javascript">
      <!--//--><![CDATA[//><!--
      $(document).ready(function(){
        // Apply jrac on some image.
        $('.pane img').jrac({
          'crop_width': 250,
          'crop_height': 170,
          'crop_x': 100,
          'crop_y': 100,
          'image_width': {$image_width},
          'viewport_onload': function() {
            var $viewport = this;
            var inputs = $viewport.$container.parent('.pane').find('.coords input:text');
            var events = ['jrac_crop_x','jrac_crop_y','jrac_crop_width','jrac_crop_height','jrac_image_width','jrac_image_height'];
            for (var i = 0; i < events.length; i++) {
              var event_name = events[i];
              // Register an event with an element.
              $viewport.observator.register(event_name, inputs.eq(i));
              // Attach a handler to that event for the element.
              inputs.eq(i).bind(event_name, function(event, $viewport, value) {
                $(this).val(value);
              })
              // Attach a handler for the built-in jQuery change event, handler
              // which read user input and apply it to relevent viewport object.
              .change(event_name, function(event) {
                var event_name = event.data;
                $viewport.$image.scale_proportion_locked = $viewport.$container.parent('.pane').find('.coords input:checkbox').is(':checked');
                $viewport.observator.set_property(event_name,$(this).val());
              });
            }
            $viewport.$container.append('<div>Image natual size: '
              +$viewport.$image.originalWidth+' x '
              +$viewport.$image.originalHeight+'</div>')
          }
        })
        // React on all viewport events.
        .bind('jrac_events', function(event, $viewport) {
          var inputs = $(this).parents('.pane').find('.coords input');
          inputs.css('background-color',($viewport.observator.crop_consistent())?'chartreuse':'salmon');
          $('#submit').prop('disabled', ($viewport.observator.crop_consistent())?false:true);
        });
      });
      //--><!]]>
    </script>
  </head>
  <body>
      

    <div class="pane clearfix">
      <img src="{$image}" alt="Loulou form Sos Chats Geneva" />
    
  	  <form action='{$formUrl}' method='GET' >
        <table class="coords">
          <tr><td>crop x</td><td><input type="text" id='{$id}cx' name='{$id}cx' /></td></tr>
          <tr><td>crop y</td><td><input type="text" id='{$id}cy' name='{$id}cy' /></td></tr>
          <tr><td>crop width</td><td><input type="text" id='{$id}cw' name='{$id}cw' /></td></tr>
          <tr><td>crop height</td><td><input type="text" id='{$id}ch' name='{$id}ch' /></td></tr>

          <tr><td>image width</td><td><input type="text" id='{$id}iw' name='{$id}iw' /></td></tr>
          <tr><td>image height</td><td><input type="text" id='{$id}ih' name='{$id}ih' /></td></tr>
          <tr><td>lock proportion</td><td><input type="checkbox" checked="checked" /></td></tr>
        </table>
        {foreach $hiddens hidden}{$hidden}{/foreach}
        <input type='hidden' id='showtemplate' name='showtemplate' value='false' />
        
        <input id='{$id}resize' type='submit' name='{$id}resize' value='resize' onclick='javascript:return confirm("Are you sure? There is no Crtl+z down here..");'/>
        <input id='{$id}crop' type='submit' name='{$id}crop' value='crop' onclick='javascript:return confirm("Are you sure? There is no Crtl+z down here..");'/>
        <input id='{$id}reset' type='submit' name='{$id}reset' value='reset' onclick='javascript:return confirm("Are you sure? It will reset your current modifications");'/>
        <input type='button' value='close' onclick='javascript:window.close();'/>
      </form>
	  
    </div>
  </body>
  </html>