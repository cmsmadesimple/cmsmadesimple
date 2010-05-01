function cms_ajax_call(fName, aArgs, options)
{
    var success = function (data, textStatus) {
        cms_ajax_callback(data);
    };
    var complete = function (XMLHttpRequest, textStatus) { this; } //Not implemented
    var error = function (XMLHttpRequest, textStatus, errorThrown) { this; } //Not implemented
    var dataFilter = function (data, type) { return data; } //Not implemented
    var beforeSend = function (XMLHttpRequest) { return this; } //Not implemented
    
    if (options['success'])
        success = options['success'];
    if (options['complete'])
        complete = options['complete'];
    if (options['error'])
        error = options['error'];
    if (options['dataFilter'])
        dataFilter = options['dataFilter'];
    if (options['beforeSend'])
        beforeSend = options['beforeSend'];
    
    jQuery.ajax({
        url: cms_ajax_callback_url,
        data: {
            cms_ajax_function_name: fName,
            cms_ajax_args: JSON.stringify(jQuery.makeArray(aArgs))
        },
        dataType: "json",
        type: "POST",
        success: success,
        complete: complete,
        error: error,
        dataFilter: dataFilter,
        beforeSend: beforeSend
    });
}

function cms_ajax_callback(json)
{
    if (jQuery.isArray(json))
    {
        for (i = 0; i < json.length; i++)
        {
            if (jQuery.isArray(json[i]) && json[i][0] == 'sc')
            {
                eval(json[i][1]);
            }
            else if (jQuery.isArray(json[i]) && json[i][0] == '__debug')
            {
                if (window.console && window.console.firebug)
                {
                    console.debug(json[i][1]);
                }
            }
        }
    }
}

jQuery.fn.serializeForCmsAjax = function()
{
    return ["sf", this.serialize()];
};

jQuery.fn.highlight = function(color, speed, easing, callback)
{
    jQuery(this).effect("highlight", {color: color}, speed);
};

jQuery.fn.serializeObject = function()
{
    var o = {};
    var a = this.serializeArray();
    $.each(a, function() {
        if (o[this.name]) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    return o;
};

/*
jQuery delayed observer
(c) 2009 - Maxime Haineault (max@centdessin.com)
*/

(function($){
    $.extend($.fn, {
        delayedObserver: function(callback, delay, options){
            return this.each(function(){
                var el = $(this);
                var op = options || {};
                el.data('oldval', el.val())
                    .data('delay', delay || 0.5)
                    .data('condition', op.condition || function() { return ($(this).data('oldval') == $(this).val()); })
                    .data('callback', callback)
                    [(op.event||'keyup')](function(){
                        if (el.data('condition').apply(el)) { return; }
                        else {
                            if (el.data('timer')) { clearTimeout(el.data('timer')); }
                            el.data('timer', setTimeout(function(){
                                el.data('callback').apply(el);
                            }, el.data('delay') * 1000));
                            el.data('oldval', el.val());
                        }
                    });
            });
        }
    });
})(jQuery);
