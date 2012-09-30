var cur_section = '';

function status_error(text) {
  var html = '<p class="status_error">'+text+'</p>';
  _update_status(html);
}
function status_msg(text) {
  var html = '<p class="pagetext">'+text+'</p>';
  _update_status(html);
}
function _update_status(html) {
  $('#status_area').html(html);
  $('#status_area').show();
}
function begin_section(id,lbl) {
  cur_section = lbl;
  $('#searchresults').append('<li class="section">'+lbl+'&nbsp;(<span class="section_count">0</span>)<ul class="section_children" id="'+id+'" style="display: none;"><ul>');
}
function add_result(listid,content,title,url) {
  $('#searchresults_cont').show();
  var html = '<li><a href="'+url+'" target="_blank" title="'+title+'">'+content+'</a></li>';
  var c = $('ul#'+listid).children().length + 1;
  $('ul#'+listid).prev('.section_count').html(c);
  $('ul#'+listid).append(html);
   
  $('#searchresults').find('a').each(function(){
    var t = $(this).data('events');
    if( t == undefined || t.length == 0 ) {
      $(this).bind('click',function(e){
        return confirm(clickthru_msg);
      });
    }
  });
}
function end_section() {
  cur_section = '';
}
$(document).ready(function(){
  $('#adminsearchform > form').attr('target','workarea');
  $('#workarea').attr('src',ajax_url);
  if( typeof(sel_all) != undefined ) {
    $('#filter_box input:checkbox').attr('checked','checked');
  }
  $('#filter_box input:checkbox').live('click',function(e){
    var v = $(this).val();
    if( v == -1 ) {
      var t = $(this).attr('checked');
      if( t == 'checked' ) {
        $('.filter_toggle').attr('checked',t);
      }
      else {
        $('.filter_toggle').removeAttr('checked');
      }
    } else {
      $('#filter_all').removeAttr('checked');
    }
  });
  $('#searchbtn').live('click',function(e){
    $('#searchresults').html('');
  });
  $('li.section').live('click',function(){
    $('.section_children').hide();
    $(this).children('ul').show();
  });
});
