{if !isset($is_ie)}
{* IE sucks... we only get here for REAL browsers. *}
<script type="text/javascript">{literal}
jQuery(function($) {
   $('.dialog').bind('dialogopen',function(event,ui){
     var url = '{/literal}{$chdir_url}{literal}';
     url = url.replace(/amp;/g,'')+'&showtemplate=false';
     $.get(url,function(data) {
       $('#fm_newdir').val('/'+data);
     });
   });
   $('#chdir_form').submit(function(e){
     var data = $(this).serialize();
     var url = '{/literal}{$chdir_url}{literal}';
     url = url.replace(/amp;/g,'')+'&showtemplate=false';
     $.post(url,data,function(data,textStatus,jqXHR){
       // stuff to do on post finishing.
       $('.dialog').dialog('close');
       $('#chdir_form').trigger('dropzone_chdir');
     });
     e.preventDefault();
   });

   var thediv = '#theme_dropzone';

   // prevent browser default drag/drop handling
   $(document).bind('drop dragover', function(e) {
       // prevent default drag/drop stuff.
       e.preventDefault();
   });

   $(thediv+'_i').fileupload({
     dataType: 'json',
     dropZone: $(thediv),
     maxChunkSize: {/literal}{$max_chunksize},{literal}
  
     progressall: function(e,data) {
       var total = (data.loaded / data.total * 100).toFixed(0);
       $(thediv).progressbar({ value: parseInt(total) });
       $('.ui-progressbar-value').html(total+'%');
     },

     stop: function(e,data) {
       $(thediv).progressbar('destroy');
       $(thediv).trigger('dropzone_stop');
     }
   });
});
{/literal}</script>
<div class="drop">
	<div class="drop-inner cf">
	{if isset($dirlist)}
		<span class="folder-selection open" title="{'open'|lang}"></span>
		<div class="dialog invisible" role="dialog" title="{$mod->Lang('change_working_folder')}">
			<form id="chdir_form" class="cms_form" action="{$chdir_url}" method="post">
				<fieldset>
					<legend>{$mod->Lang('change_working_folder')}</legend>
					<label>{$mod->Lang('folder')}: </label>
                                        <input type="hidden" name="m1_path" value="{$cwd}"/>
                                        <input type="hidden" name="m1_ajax" value="1"/>
					<select class="cms_dropdown" id="fm_newdir" name="m1_newdir">
                                          {html_options options=$dirlist selected=$cwd}
					</select>
					<input type="submit" name="m1_submit" value="{$mod->lang('ok')}" />
				</fieldset>
				</form>
		</div>
	{/if}								
		<div class="zone">
			<div id="theme_dropzone">
				{$formstart}
				<input type="hidden" name="disable_buffer" value="1"/>
				<input type="file" id="theme_dropzone_i" name="{$actionid}files[]" multiple style="display: none;"/>
				{$prompt_dropfiles}
				{$formend}
			</div>
		</div>
	</div>									
	<a href="#" title="{'open'|lang}/{'close'|lang}" class="toggle-dropzone">{'open'|lang}/{'close'|lang}</a>
</div>
{/if}
