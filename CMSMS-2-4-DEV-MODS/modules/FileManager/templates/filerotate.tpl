<h3>{$mod->Lang('rotateimage')}</h3>

<style type="text/css">
img#rotimg {
  z-index: 0;
}
</style>

<script type="text/javascript">
$(function(){
  $('#rotangle').slider({ min: -180, max: 180, value: 0,
	                  change: function( event, ui ) {
                            $('#angletxt').val(ui.value);
                          },
	                  slide: function( event, ui ) {
                            $('#angletxt').val(ui.value);
			    $('#rotimg').rotate({ animateTo: ui.value });
                          }
                        });
   $(document).on('click', 'button.autorotate', function(){
      var id = $(this).attr('id');
      var dir = id.substr(0,3);
      var val = parseInt(id.substr(3),10);
      if( dir == 'neg' ) val = val * -1;
      $('#angletxt').val(val);
      $('#rotimg').rotate({ animateTo: val });
      $('#rotangle').slider( 'value', val );
      return false;
   });
});
</script>

{$startform}
<div class="information">{$mod->Lang('info_rotate')}</div>
<div class="pageoverflow">
  <p class="pagetext">{$mod->Lang('image')}: {$filename}</p>
  <p class="pageinput">
    <img id="rotimg" src="{$image}" width="{$width}" height="{$height}"/>
  </p>
</div>
<div class="pageoverflow">
  <p class="pagetext"><label>{$mod->Lang('angle')}: <input type="text" readonly="readonly" id="angletxt" name="{$actionid}angle" value="0"/></label></p>
  <p class="pageinput">{$mod->Lang('predefined')}:
    <button class="autorotate" id="neg180" title="{$mod->Lang('rotate_neg180')}">-180</button>
    <button class="autorotate" id="neg135" title="{$mod->Lang('rotate_neg135')}">-135</button>
    <button class="autorotate" id="neg90" title="{$mod->Lang('rotate_neg90')}">-90</button>
    <button class="autorotate" id="neg45" title="{$mod->Lang('rotate_neg45')}">-45</button>
    <button class="autorotate" id="neg30" title="{$mod->Lang('rotate_neg30')}">-30</button>
    <button class="autorotate" id="pos30" title="{$mod->Lang('rotate_pos30')}">+30</button>
    <button class="autorotate" id="pos45" title="{$mod->Lang('rotate_pos45')}">+45</button>
    <button class="autorotate" id="pos90" title="{$mod->Lang('rotate_pos90')}">+90</button>
    <button class="autorotate" id="pos135" title="{$mod->Lang('rotate_pos135')}">+135</button>
    <button class="autorotate" id="pos180" title="{$mod->Lang('rotate_pos180')}">+180</button>
  </p>
  <p class="pageinput" id="rotangle" title="{$mod->Lang('info_rotate_slider')}">
  </p>
</div>
<div class="pageoverflow">
  <p class="pagetext"><label for="postrotate">{$mod->Lang('postrotate')}:</label>&nbsp;{cms_help key2='help_postrotate' title=$mod->Lang('postrotate')}</p>
  <p class="pageinput">
    <select id="postrotate" name="{$actionid}postrotate">
    {html_options options=$opts selected=$postrotate}
    </select>
  </p>
</div>
<div class="pageoverflow">
  <p class="pagetext"><label for="createthumb">{$mod->Lang('createthumbnail')}:</label></p>
  <p class="pageinput">
    <select id="createthumb" name="{$actionid}createthumb">{cms_yesno selected=$createthumb}</select>
  </p>
</div>
<div class="pageoverflow">
  <p class="pagetext"></p>
  <p class="pageinput">
    <input type="submit" name="{$actionid}save" value="{$mod->Lang('save')}"/>
    <input type="submit" name="{$actionid}cancel" value="{$mod->Lang('cancel')}"/>
  </p>
</div>
{$endform}