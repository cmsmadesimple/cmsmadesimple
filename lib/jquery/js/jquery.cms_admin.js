$(document).ready(function(){
  // handles clicking on a cms_helpicon image
  // if div containing help text cannot be found help text is loaded via
  // ajax.
  $('.cms_help img.cms_helpicon').live('click',function(){
    var txt;
    var key = $(this).next('span.cms_helpkey').html();
    if( key.length && $('#cmshelp_'+key).length == 0 ) {
      // get the text via ajax
      // put it in the div.
      var e = $('<div class="cms_helptext" title="'+cms_data['title_help']+'" id="cmshelp_'+key+'" style="display: none;"></div>');
      $(this).append(e);
      $.get(cms_data['ajax_help_url'],{ key: key },function(data){
        $('#cmshelp_'+key).html(data);
	//alert('got '+data);
      });
    }
    $('#cmshelp_'+key).dialog();
  });
});
