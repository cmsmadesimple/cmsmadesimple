/**
 * Interface Elements for jQuery
 * Animate Class
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
 * @name animateClass
 * @description Animates an element from its initital layout to a changed layout by a CSS class
 * @param Mixed classToAnimate string class to animate or array of two classes to animate from to
 * @param Mixed speed animation speed, integer for miliseconds, string ['slow' | 'normal' | 'fast']
 * @param Function callback (optional) A function to be executed whenever the animation completes.
 * @param String easing (optional) The name of the easing effect that you want to use.
 * @type jQuery
 * @cat Plugins/Interface
 * @author Paul Bakaus
 * @author Stefan Petre
 */
jQuery.fn.animateClass = function(classToAnimate, speed, callback, easing) {
	return this.queue('interfaceClassFX',function(){
		jQuery.fx.animateClass(this, classToAnimate, speed, callback, easing);
	});
};

jQuery.fx.animateClass = function(el, classToAnimate, duration, easing, callback) {
	var endClass = typeof classToAnimate == 'string' ? classToAnimate : classToAnimate[1];
	var startClass = typeof classToAnimate == 'string' ? null : classToAnimate[0];
	var oldStyleAttr = jQuery(el).attr("style") || '';
	/* In IE, style is a object.. */
	if(typeof oldStyleAttr == 'object') oldStyleAttr = oldStyleAttr["cssText"];
	
	jQuery(el).removeClass(endClass);
	if (startClass) {
		jQuery(el).addClass(startClass);
	}
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
	
	if (startClass) {
		jQuery(el).removeClass(startClass);
	}
	jQuery(el).addClass(endClass);
	var toAnimate = {};
	var toColors = {};
	var currentStyle = document.defaultView ? document.defaultView.getComputedStyle(el,null) :  el.currentStyle;
	for (var i=0; i<jQuery.fx.animatedCssRules.length; i++) {
		if (currentStyle[jQuery.fx.animatedCssRules[i]]) {
			newStyle = parseInt(currentStyle[jQuery.fx.animatedCssRules[i]]) || 0;
			if (newStyle != oldStyles[i]) {
				toAnimate[jQuery.fx.animatedCssRules[i]] = newStyle;
			}
		}
	}
	for (var i=0; i<jQuery.fx.animatedColorsCssRules.length; i++) {
		if (currentStyle[jQuery.fx.animatedColorsCssRules[i]] && currentStyle[jQuery.fx.animatedColorsCssRules[i]] != oldColors[i]) {
			toColors[jQuery.fx.animatedColorsCssRules[i]] = [oldColors[i],currentStyle[jQuery.fx.animatedColorsCssRules[i]]];
		}
	}
	jQuery(el)
		.removeClass(endClass)
		.animateColor(duration, toColors, easing, callback)
		.animate(
			toAnimate,
			duration,
			easing,
			function()
			{
				jQuery(this).addClass(endClass);
				/* Change style attribute back to original.
				 * For IE, we need to clear the damn object.
				 */
				if(typeof jQuery(this).attr("style") == 'object') {
					jQuery(this).attr("style")["cssText"] = "";
					jQuery(this).attr("style")["cssText"] = oldStyleAttr;
				} else {
					jQuery(this).attr("style", oldStyleAttr);	
				}
				jQuery.dequeue(this, 'interfaceClassFX');
			}
		);
};