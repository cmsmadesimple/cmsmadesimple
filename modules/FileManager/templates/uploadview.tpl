<script type="text/javascript">{literal}
$(function() {
   var _jqXHR = [];  // jqXHR array
   var _files = [];  // filenames

    // prevent browser default drag/drop handling
    $(document).bind('drop dragover', function(e) {
        // prevent default drag/drop stuff.
        e.preventDefault();
    });

    $(document).on('click', '#cancel', function(e){
        e.preventDefault();
        aborting = true;
        var ul = $('#fileupload').data('fileupload');
        if( typeof ul != 'undef' )
        {
          var data = {};
          data.errorThrown = 'abort';
	  ul._trigger('fail', e, data);
        }
    });

    // create our file upload area.
    $('#fileupload').fileupload({

        add: function(e, data) {
            _files.push(data.files[0].name);
            _jqXHR.push(data.submit());
        },	

    	dataType: 'json',
    	dropZone: $('#dropzone'),
    	maxChunkSize: {/literal}{$max_chunksize},{literal}
    	
    	start: function(e,data){
		$('#cancel').show();
    		$('#progressarea').show();
    	},
    	
    	done: function(e,data){
		_files = [];
		_jqXHR = [];		
        },

        fail: function(e, data) {
            $.each(_jqXHR, function(index,obj){
                if( typeof obj == 'object' )
 		{
		  obj.abort();
		  if( index < _files.length && typeof data.url != 'undefined' ) {
  		    // now delete the file.
                    var turl = '{/literal}{$action_url}{literal}';
		    turl = turl.replace(/amp;/g,'') + '&' + $.param({ file: _files[index] });
		    $.ajax({
	               url: turl,
                       type: 'DELETE'
                    });
                  }
                }
            });
	    _jqXHR = [];
            _files = [];
        },

        progressall: function(e, data) {
        	// overall progress callback
        	var perc = (data.loaded / data.total * 100).toFixed(2);
            var total = null;
            total = (data.loaded / data.total * 100).toFixed(0);
            var str = perc + ' %';
        	//console.log(total);
        	barValue(total);
        		function barValue(total) {
                    	$("#progressarea").progressbar({
                    		value: parseInt(total)
                    	});
                    	$(".ui-progressbar-value").html(str);							    
        		}
        },

        stop: function(e, data) {
            $('#filesarea').load(refresh_url);
            $('#cancel').fadeOut();
            $('#progressarea').fadeOut();
        }  
    });
});
{/literal}</script>

<style type="text/css">{literal}
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
{/literal}</style>
<h3>{$prompt_path}&nbsp;{$path}</h3>

{$formstart}
<input type="hidden" name="disable_buffer" value="1"/>
<fieldset>
{if isset($is_ie)}
<div class="pageerrorcontainer message">
  <p class="pageerror">{$ie_upload_message}</p>
</div>
{/if}
<div class="upload-wrapper">
<div style="width: 60%; float: left;">
  {*<input type="hidden" name="MAX_FILE_SIZE" value="{$maxfilesize}" />*}{* recommendation for browser *}
  <input id="fileupload" type="file" name="{$actionid}files[]" size="50" multiple/>
  <div id="pageoverflow">
    <p class="pagetext"></p>
    <p class="pageinput">
      <input id="cancel" type="submit" value="{$mod->Lang('cancel')}" style="display: none;"/>
    </p>
  </div>
</div>
<div id="leftcol" style="height: 4em; width: 40%; float: left; display: table;">
  {if !isset($is_ie)}
  <div id="dropzone" class="vcentered hcentered"><p id="dropzonetext">{$mod->Lang('prompt_dropfiles')}</p></div>
  {/if}
</div>
<div class="clearb"></div>
<div id="progressarea"></div>
</div>
</fieldset>
{$formend}
