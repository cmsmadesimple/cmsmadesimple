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

function escapeRegExp(string) {
  return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'); // $& means the whole matched string
}

function add_result(listid,content) {
  $('#searchresults_cont').show();
  search_text = $('#searchtext').val();
  casesensitive = $('#search_case').is(':checked');
  search_text_regex = RegExp(escapeRegExp(search_text),'g'+(casesensitive ? '' : 'i'));

  var html = $('<li/>');
    if (content.edit_url.length == 0) {
      var _a = $('<span/>',{class: "search_resulttitle"}).text(content.title);
    } else {
      var _a = $('<a/>').attr('href',content.edit_url.replace(/&amp;/g,'&')).attr('target','_blank').attr('title',content.title).text(content.title);
    }
    html.append(_a);
    
    if ('text' in content) {
      if (content.text !== '') { //should we show snippets?
        _p = $('<p/>').html(content.text);
        html.append(_p);
      }
    } else if (  Object.keys(content.locations).length > 0 ) {
      html.append(' (' + content.count + 'x)');
      _p = $('<p/>');
      Object.keys(content.locations).forEach(function(element) {
        _p.append('<i>' + element + ':</i><br/>');
        content.locations[element].forEach(function (snippet) {
         _s = '&nbsp;&nbsp;' + snippet.replace(search_text_regex,'<span class="search_oneresult">$&</span>') + '<br/>';
         _p.append(_s);
       });
      });
      html.append(_p);
    }
    else if( content.snippets.length > 0 ) {
      html.append(' (' + content.count + 'x)');
       _p = $('<p/>');
       content.snippets.forEach(function (snippet) {
         _s = snippet.replace(search_text_regex,'<span class="search_oneresult">$&</span>') + '<br/>';
         _p.append(_s);
       });
       html.append(_p);
    } 
  
    $('ul#'+listid).append(html);
    var c = $('ul#'+listid).children().length;
    $('ul#'+listid).closest('li.section').find('span.section_count').html(c);

}
function end_section() {
  cur_section = '';
}
$(document).ready(function(){
  $('#adminsearchform > form').attr('target','workarea');
  $('#workarea').attr('src',ajax_url);
  if( typeof(sel_all) != undefined ) {
    $('#filter_box input.filter_toggle:checkbox').attr('checked','checked');
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
      if ($(this).hasClass('filter_toggle')) $('#filter_all').removeAttr('checked');
    }
  });
  $('#searchresults').on('click','li.section',function(){
    $('.section_children').hide();
    $(this).children('.section_children').show();
  });
});
