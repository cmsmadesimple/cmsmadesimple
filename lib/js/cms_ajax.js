jQuery.fn.highlight = function(color, speed, easing, callback) {
    
	/* current color of the element */
	var originalColor = jQuery(this).css('backgroundColor');
	
	/* find the first "real" color from the parent elements */
	var parentEl = this.parentNode;
	while(originalColor == 'transparent' && parentEl) {
		originalColor = jQuery(parentEl).css('backgroundColor');
		parentEl = parentEl.parentNode;
	}
	
	/* swap element to the highlight color */
	jQuery(this).css('backgroundColor', color);
	
	/* in IE, style is an object */
	if(typeof this.oldStyleAttr == 'object') {
	    this.oldStyleAttr = this.oldStyleAttr["cssText"];
	}
	
	/* animate back to the original color */
	jQuery(this).animate(
		{'backgroundColor':originalColor},
		speed
	);
};

function cms_ajax_call(fName, aArgs, iInt)
{
	$.ajax({
			url: cms_ajax_callback_url,
			data: {
				cms_ajax_function_name: fName,
				cms_ajax_args: cms_ajax_array_to_xml(jQuery.makeArray(aArgs))
			},
			dataType: "xml",
			success: function (xml) {
				cms_ajax_callback(xml);
			}
	});
}

function cms_ajax_callback(xml)
{
	$(xml).find('ajax').children().each(
		function()
		{
			if (this.tagName == 'as') //from ->modify_html
			{
				var selector = $(this).find("s").text();
				var val = $(this).find("t").text();
				
				$(selector).html(val);
			}
			else if (this.tagName == 'sc') //from ->script
			{
				var val = $(this).find("t").text();

				eval(val);
			}
		}
	);
}

function cms_ajax_array_to_xml(ary)
{
	var xml = "<ajaxarray>";
	for (i = 0; i < ary.length; i++)
	{
		xml += '<e><![CDATA[' + ary[i] + ']]></e>';
	}
	xml += "</ajaxarray>";

	return xml;
}
