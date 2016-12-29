<style type="text/css">
.upload-wrapper {
  margin: 10px 0
}
.hcentered {
  text-align: center
  }
.vcentered {
  display: table-cell;
  vertical-align: middle
   }
#dropzone {
  margin: 15px 0;
  border-radius: 4px;
  border: 2px dashed #ccc
  }
#dropzone:hover{
  cursor: move
}
#progressarea {
  margin: 15px;
  height: 2em;
  line-height: 2em;
  text-align: center;
  border: 1px solid #aaa;
  border-radius: 4px;
  display: none
  }
</style>
{*if !isset($is_ie)*}
{* IE sucks... we only get here for REAL browsers. *}
<script type="text/javascript">
$(document).ready(function(){

    var thediv = '#theme_dropzone';

    $(document).on('dialogopen', '.drop .dialog', function(event,ui){
        var url = '{$chdir_url}';
            url = url.replace(/amp;/g,'')+'&showtemplate=false';// ???

        $.get(url,function(data) {
            $('#fm_newdir').val('/'+data);
        });
    });

    $('#chdir_form').submit(function(e){
        var data = $(this).serialize();
        var url = '{$chdir_url}';
        url = url.replace(/amp;/g,'')+'&showtemplate=false'; // ???

        $.post(url,data,function(data,textStatus,jqXHR){
            // stuff to do on post finishing.
            $('#chdir_form').trigger('dropzone_chdir');
            $('.dialog').dialog('close');
        });

    e.preventDefault();
});

// prevent browser default drag/drop handling
$(document).on('drop dragover', function(e) {
    // prevent default drag/drop stuff.
    e.preventDefault();
});

    $(thediv+'_i').fileupload({
        dataType: 'json',
        dropZone: $(thediv),
        maxChunkSize: {$max_chunksize},

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
</script>
{html_options name=$name options=$options selected=$value}&nbsp;



<div class="drop">
  <div class="drop-inner cf">
  {*if isset($dirlist)}
    <span class="folder-selection open" title="{'open'|lang}"></span>
    <div class="dialog invisible" role="dialog" title="{$FileManager->Lang('change_working_folder')}">
      <form id="chdir_form" class="cms_form" action="{$chdir_url}" method="post">
        <fieldset>
          <legend>{$FileManager->Lang('change_working_folder')}</legend>
          <label>{$FileManager->Lang('folder')}: </label>
                                        <input type="hidden" name="m1_path" value="{$cwd}"/>
                                        <input type="hidden" name="m1_ajax" value="1"/>
          <select class="cms_dropdown" id="fm_newdir" name="m1_newdir">
                                          {html_options options=$dirlist selected="/`$cwd`"}
          </select>
          <input type="submit" name="m1_submit" value="{$FileManager->lang('submit')}" />
        </fieldset>
        </form>
    </div>
  {/if*}
    <div class="zone">
      <div id="theme_dropzone">
      <form action="{$upload_link}" method="post" enctype="multipart/form-data">
        {*$formstart*}
        <input type="hidden" name="disable_buffer" value="1"/>
        <input type="file" id="theme_dropzone_i" name="{$actionid}files[]" multiple style="display: none;"/>
        {***$prompt_dropfiles***} -><-
        </form>
      </div>
    </div>
  </div>
  <a href="#" title="{'open'|lang}/{'close'|lang}" class="toggle-dropzone">{'open'|lang}/{'close'|lang}</a>
</div>
{*/if*}
<form id="upload_form" class="cms_form" action="{$upload_link}" method="post" enctype="multipart/form-data">
  <input type="file" name="fileToUpload" id="fileToUpload">
  <input type="submit" value="{$fpmod->Lang('upload_file')}" name="submit">
</form>

{*
<a href="#" id="upload" class="open" onclick="return false;">Upload</a>&nbsp;
<div class="dialog" title="Basic dialog" style="width=80%">
  <div class='drop_target'>
  
{*
  <form action="{$upload_link}" method="post" enctype="multipart/form-data">
    <input type="file" name="fileToUpload" id="fileToUpload">
    <input type="submit" value="{$fpmod->Lang('upload_file')}" name="submit">
  </form>
  </div> 
</div>
*}
{*
<form id="upload" method="post" action="upload.php" enctype="multipart/form-data">
  <div id="drop">
    Drop Here

    <a>Browse</a>
    <input type="file" name="upl" multiple />
  </div>

  <ul>
    <!-- The file uploads will be shown here -->
  </ul>

</form>
*}



{*
<script>

$(document).ready(function(){
    $("button").click(function(){
        $("#div1").load("demo_test.txt");
    });
});
</script>
*}

{*
<script>
$( "#dialog" ).dialog({ autoOpen: false });
$( "#opener" ).click(function() {
  $( "#dialog" ).dialog( "open" );
});
</script>
*}