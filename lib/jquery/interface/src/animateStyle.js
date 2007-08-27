/**
 * Interface Elements for jQuery
 * Animate Style
 * 
 * http://interface.eyecon.ro
 * 
 * Copyright (c) 2006 Stefan Petre, Paul Bakaus (http://www.paulbakaus.com)
 * Dual licensed under the MIT (MIT-LICENSE.txt) 
 * and GPL (GPL-LICENSE.txt) licenses.
 *   
 *
 */
 
/**
 * @name animateStyle
 * @description Animates an element from its initital layout by appling inline CSS rules
 * @param String styleToAnimate string of inline CSS rules to animate to
 * @param Mixed speed animation speed, integer for miliseconds, string ['slow' | 'normal' | 'fast']
 * @param Function callback (optional) A function to be executed whenever the animation completes.
 * @param String easing (optional) The name of the easing effect that you want to use.
 * @type jQuery
 * @cat Plugins/Interface
 * @author Paul Bakaus
 * @author Stefan Petre
 */

jQuery.fn.animateStyle = function(styleToAnimate, duration, callback, easing) {
	return this.queue('interfaceStyleFX',function(){
		jQuery.fx.animateStyle(this, styleToAnimate, duration, callback, easing);
	});
};

jQuery.fx.animateStyle = function(el, styleToAnimate, duration, easing, callback) {
	var newStyles;
	if (typeof styleToAnimate == 'string') {
		newStyles = jQuery.convertInlineStylesToHash(styleToAnimate);
	} else {
		newStyles = styleToAnimate;
	}
	var oldStyleAttr = jQuery(el).attr("style") || '';
	/* In IE, style is a object.. */
	if(typeof oldStyleAttr == 'object') oldStyleAttr = oldStyleAttr["cssText"];
	
	var oldStyles = [];
	var oldColors = [];
	
	var currentStyle = document.defaultView ? document.defaultView.getComputedStyle(el,null) :  el.currentStyle;
	
	for (var i=0; i<jQuery.fx.animatedCssRules.length; i++) {
		if (currentStyle[jQuery.fx.animatedCssRules[i]])
			oldStyles[i] = parseInt(currentStyle[jQuery.fx.animatedCssRules[i]]) || 0;
	}
	for (var i=0; i<jQuery.fx.animatedColorsCssRules.length; i++) {
		if (currentStyle[jQuery.fx.animatedColorsCssRules[i]])
			oldColors[i] = currentStyle[jQuery.fx.animatedColorsCssRules[i]];
	}
	
	jQuery(el).css(newStyles);
	
	var toAnimate = {};
	var toColors = {};
	var stylesLength = 0;
	var currentStyle = document.defaultView ? document.defaultView.getComputedStyle(el,null) :  el.currentStyle;
	for (var i=0; i<jQuery.fx.animatedCssRules.length; i++) {
		if (currentStyle[jQuery.fx.animatedCssRules[i]] && newStyles[jQuery.fx.animatedCssRules[i]]) {
			newStyle = parseInt(currentStyle[jQuery.fx.animatedCssRules[i]]) || 0;
			if (newStyle != oldStyles[i]) {
				toAnimate[jQuery.fx.animatedCssRules[i]] = newStyle;
				stylesLength++;
			}
		}
	}
	for (var i=0; i<jQuery.fx.animatedColorsCssRules.length; i++) {
		if (currentStyle[jQuery.fx.animatedColorsCssRules[i]] && currentStyle[jQuery.fx.animatedColorsCssRules[i]] != oldColors[i]) {
			toColors[jQuery.fx.animatedColorsCssRules[i]] = [oldColors[i],currentStyle[jQuery.fx.animatedColorsCssRules[i]]];
		}
	}
	/* Change style attribute back to original.
	 * For IE, we need to clear the damn object.
	 */
	if(typeof jQuery(el).attr("style") == 'object') {
		jQuery(el).attr("style")["cssText"] = "";
		jQuery(el).attr("style")["cssText"] = oldStyleAttr;
	} else {
		jQuery(el).attr("style", oldStyleAttr);	
	}
	jQuery(el)
		.animateColor(duration, toColors, easing, callback)
		.animate(
			toAnimate,
			duration,
			easing
		);
	var times = window.setTimeout(
		function() {
			jQuery.dequeue(el, 'interfaceStyleFX');
		},
		duration
	);
};
jQuery.convertInlineStylesToHash = function(stylesToConvert) {
	var newStyles = {};
	if (typeof stylesToConvert == 'string') {
		stylesArray = stylesToConvert.split(';');
		jQuery.each(
			stylesArray,
			function() {
				rule = this.split(':');
				if (rule.length == 2) {
					newStyles[jQuery.trim(rule[0].replace(/\-(\w)/g,function(m,c){return c.toUpperCase();}))] = jQuery.trim(rule[1]);
				}
			}
		);
	}
	return newStyles;
};