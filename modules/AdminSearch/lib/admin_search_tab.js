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
function begin_section(id,lbl,desc) {
  cur_section = lbl;
  var txt = '<li class="section" id="sec_'+id+'">'+lbl+'&nbsp;(<span class="section_count">0</span>)';
  txt = txt + '<div class="section_children" style="display: none;">';
  if( typeof desc == 'string' && desc.length > 0 ) {
    txt += '<p>'+desc+'</p>';
  }
  txt += '<ul id="'+id+'"></ul>';
  txt += '</div>';
  $('#searchresults').append(txt);
}
function add_result(listid,content,title,url,text) {
    $('#searchresults_cont').show();
    var html = $('<li/>');
    if( url.length == 0 ) {
  console.debug('url is empty');
  html.html(content);
    }
    else {
  var _a = $('<a/>').attr('href',url).attr('target','_blank').attr('title',title).text(content);
  html.append(_a);
  if( text.length > 0 ) html.append('<p>'+text+'</p>');
    }
    $('ul#'+listid).append(html);
    var c = $('ul#'+listid).children().length;
    $('ul#'+listid).closest('li.section').find('span.section_count').html(c);

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
  $('#filter_box input:checkbox').on('click',function(e){
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
  $('#searchresults').on('click','li.section',function(){
    $('.section_children').hide();
    $(this).children('.section_children').show();
  });
});
