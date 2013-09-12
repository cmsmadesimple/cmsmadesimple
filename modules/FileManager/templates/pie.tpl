<script type="text/javascript">
<!--//--><![CDATA[//><!--
$(document).ready(function(){
  // Apply jrac on some image.
  $('#img').jrac({
    'crop_width': 250,
    'crop_height': 170,
    'crop_x': 100,
    'crop_y': 100,
    'image_width': {$image_width},
    'viewport_width': $('#test1').width()-30,
    'viewport_onload': function() {
      var $viewport = this;
      var inputs = $('table#coords input:text');
      var events = ['jrac_crop_x','jrac_crop_y','jrac_crop_width','jrac_crop_height','jrac_image_width','jrac_image_height'];
      for (var i = 0; i < events.length; i++) {
        var event_name = events[i];
        // Register an event with an element.
        $viewport.observator.register(event_name, inputs.eq(i));
        // Attach a handler to that event for the element.
        inputs.eq(i).bind(event_name, function(event, $viewport, value) {
          $(this).val(Math.floor(value));
        })
        // Attach a handler for the built-in jQuery change event, handler
        // which read user input and apply it to relevent viewport object.
        .change(event_name, function(event) {
          var event_name = event.data;
          $viewport.$image.scale_proportion_locked = $viewport.$container.parent('.pane').find('.coords input:checkbox').is(':checked');
          $viewport.observator.set_property(event_name,$(this).val());
        });
      }
      $('#natsize').html($viewport.$image.originalWidth+' x '+$viewport.$image.originalHeight);
   }
 })
 // React on all viewport events.
 .bind('jrac_events', function(event, $viewport) {
   var inputs = $('table#coords input:text');
   if($viewport.observator.crop_consistent()) {
     inputs.removeClass('invalid');
     inputs.addClass('valid');
   }
   else {
     inputs.removeClass('valid');
     inputs.addClass('invalid');
   }
   $('#submit').prop('disabled', ($viewport.observator.crop_consistent())?false:true);
 });
});
//--><!]]>
</script>

<style type="text/css">
input.invalid { background-color: salmon; }
</style>

<h3>{$mod->Lang('resizecrop')}</h3>

{$formstart}
<div>
  <div id="test1" style="width: 74%; float: left;">
    <img id="img" src="{$image}" alt=""/>
  </div>
  <div style="width: 24%; float: left;">
    <div style="pageoverflow">
      <p class="pagetext">{$mod->Lang('image')}:&nbsp;{$filename}</p>
      <p class="pagetext">{$mod->Lang('pie_image_natural_size')}: <span id="natsize"></span></p>
    </div>
    <table id="coords" class="coords">
      <tr><td>{$mod->Lang("pie_crop_x")}</td><td><input type="text" id='{$actionid}cx' name='{$actionid}cx' /></td></tr>
      <tr><td>{$mod->Lang("pie_crop_y")}</td><td><input type="text" id='{$actionid}cy' name='{$actionid}cy' /></td></tr>
      <tr><td>{$mod->Lang("pie_crop_w")}</td><td><input type="text" id='{$actionid}cw' name='{$actionid}cw' /></td></tr>
      <tr><td>{$mod->Lang("pie_crop_h")}</td><td><input type="text" id='{$actionid}ch' name='{$actionid}ch' /></td></tr>
      <tr><td>{$mod->Lang("pie_image_w")}</td><td><input type="text" id='{$actionid}iw' name='{$actionid}iw' /></td></tr>
      <tr><td>{$mod->Lang("pie_image_h")}</td><td><input type="text" id='{$actionid}ih' name='{$actionid}ih' /></td></tr>
      <tr><td>{$mod->Lang("pie_lock_proportion")}</td><td><input type="checkbox" checked="checked" /></td></tr>
    </table>
    <div style="pageoverflow">
      <button id="submit" name="{$actionid}save">{$mod->Lang('save')}</p>
      <button name="{$actionid}cancel">{$mod->Lang('cancel')}</p>
    </div>
  </div>
  <div style="clear: both;"></div>
</div>
{$formend}
