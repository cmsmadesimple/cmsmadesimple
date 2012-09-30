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
function begin_section(lbl) {
  $('#searchresults').append('<li>'+lbl+'<ul>');
}
function add_result(content,title,url) {
  $('#searchresults_cont').show();
  var html = '<li><a href="'+url+'" target="_blank" title="'+title+'">'+content+'</a></li>';
  $('#searchresults').append(html);
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
  $('#searchresult').append('</ul></li>');
}
$(document).ready(function(){
  $('#adminsearchform > form').attr('target','workarea');
  $('#workarea').attr('src',ajax_url);
  if( typeof(sel_all) != undefined ) {
    $('#filter_box input:checkbox').attr('checked','checked');
  }
  $('#filter_all').live('click',function(e){
    var t = $(this).attr('checked');
    if( t == 'checked' ) {
      $('.filter_toggle').attr('checked',t);
    }
    else {
      $('.filter_toggle').removeAttr('checked');
    }
  });
  $('#searchbtn').live('click',function(e){
    alert('test');
    $('#searchresults').html('');
  });
});
