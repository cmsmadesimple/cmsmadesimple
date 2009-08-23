function cms_ajax_call(fName, aArgs, iInt)
{
    jQuery.ajax({
        url: cms_ajax_callback_url,
        data: {
            cms_ajax_function_name: fName,
            cms_ajax_args: JSON.stringify(jQuery.makeArray(aArgs))
        },
        dataType: "json",
        type: "POST",
        success: function (json) {
            cms_ajax_callback(json);
        }
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
    /* current color of the element */
    var originalColor = jQuery(this).css('backgroundColor');

    /* find the first "real" color from the parent elements */
    var parentEl = this.parentNode;
    while(originalColor == 'transparent' && parentEl)
    {
        originalColor = jQuery(parentEl).css('backgroundColor');
        parentEl = parentEl.parentNode;
    }

    /* swap element to the highlight color */
    jQuery(this).css('backgroundColor', color);

    /* in IE, style is an object */
    if(typeof this.oldStyleAttr == 'object')
    {
        this.oldStyleAttr = this.oldStyleAttr["cssText"];
    }

    /* animate back to the original color */
    jQuery(this).animate(
        {'backgroundColor':originalColor},
        speed
    );
};
