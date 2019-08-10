// a simple bit of jquery to detect scripts of type text/cms_javascript
// and clone them and process them.
// source: https://gist.github.com/RonnyO/2391995

(function() {
   // your page initialization code here
   // the DOM will be available here
   var matches = document.querySelectorAll('script[type="text/cms_javascript"]')
   if( !matches.length ) return
   for( let i = 0; i < matches.length; i++ ) {
      let cloned = matches[i].cloneNode(true)
      cloned.removeAttribute('type')
      document.documentElement.appendChild(cloned)
   }
})();
/*
$(function(){
    $('script[type="text/cms_javascript"]').each(function(){
	var content = $(this).html();
	$('<script/>').html( content ).insertAfter( this );
    })
});
*/
