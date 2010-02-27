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
