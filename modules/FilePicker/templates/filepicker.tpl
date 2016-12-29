<style>
.fileinput-button {
  position: relative;
  overflow: hidden;
  display: inline-block;
}
.fileinput-button input {
  position: absolute;
  top: 0;
  right: 0;
  margin: 0;
  opacity: 0;
  -ms-filter: 'alpha(opacity=0)';
  font-size: 200px !important;
  direction: ltr;
  cursor: pointer;
}
#dropzone {
    background: palegreen;
    width: 150px;
    height: 50px;
    line-height: 50px;
    text-align: center;
    font-weight: bold;
}
#dropzone.in {
    width: 600px;
    height: 200px;
    line-height: 200px;
    font-size: larger;
}
#dropzone.hover {
    background: lawngreen;
}
#dropzone.fade {
    -webkit-transition: all 0.3s ease-out;
    -moz-transition: all 0.3s ease-out;
    -ms-transition: all 0.3s ease-out;
    -o-transition: all 0.3s ease-out;
    transition: all 0.3s ease-out;
    opacity: 1;
}
</style>
{html_options name=$name options=$options selected=$value}&nbsp;
{*
<input id="myfileupload" type="file" name="files[]" data-url="server/php/" multiple>
<div id="dropzone" class="fade well">Drop files here</div>
*}
<!-- The fileinput-button span is used to style the file input field as button -->
    <span class="btn btn-success fileinput-button">
        <i class="glyphicon glyphicon-plus"></i>
        <span>Select files...</span>
        <!-- The file input field used as target for the file upload widget -->
        <input id="fileupload" name="files[]" multiple="" type="file">
    </span>
    <br>
    <br>
    <!-- The global progress bar -->
    <div id="progress" class="progress">
        <div class="progress-bar progress-bar-success"></div>
    </div>
    <!-- The container for the uploaded files -->
    <div id="files" class="files"></div>

<script>
/*
$(function () {
    $('#fileupload').fileupload({
        url: '{$actionurl}',  
        dataType: 'json',
        done: function (e, data) {
            $.each(data.result.files, function (index, file) {
                $('<p/>').text(file.name).appendTo(document.body);
            });
        }
    });
});
*/
/*
$( document ).ready(function() {
    console.log( "ready!" );
    $('#myfileupload').fileupload({
      
    });
});
*/

$(function () {
      $(document).bind('drop dragover', function (e) {
        e.preventDefault();
    });
    $('#fileupload').fileupload({
        dataType: 'json',
        url: '{$actionurl}',
        dropZone: $('#dropzone'),
        done: function (e, data) {
            $.each(data.result.files, function (index, file) {
                $('<p/>').text(file.name).appendTo(document.body);
            });
        },
      progressall: function (e, data) {
          var progress = parseInt(data.loaded / data.total * 100, 10);
          $('#progress .bar').css(
              'width',
              progress + '%'
          );
      }
    });
});
</script>