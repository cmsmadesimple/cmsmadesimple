// a simple bit of jquery to detect scripts of type text/cms_javascript
// and clone them and process them.
// source: https://gist.github.com/RonnyO/2391995
$(function(){
    $('script[type="text/cms_javascript"]').each(function(){
        $(this).clone().prop('type','').insertAfter(this)
    })
});
