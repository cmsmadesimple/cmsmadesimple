jQuery(document).ready(function($) {
	// shake error
	$('#error').effect('shake', {
		times: '6',
		distance: '3'
	}, 15);
	// hide message 
	$('.message').hide().fadeIn(2600);
	// toggle info window
	$('.info-wrapper').removeClass('open');
	$('.toggle-info').click(function() {
		$('.info').toggle();
		$('.info-wrapper').toggleClass('open');
		return false;
	});
	// focus input with class focus
	$('input:first.focus').focus();
});
