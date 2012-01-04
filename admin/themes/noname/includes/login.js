jQuery(document).ready(function($) {
	$('#error').effect('shake', {
		times: '6',
		distance: '3'
	}, 15);
	$('.message').hide().fadeIn(2600);
	$('.info-wrapper').removeClass('open');
	$('.toggle-info').click(function() {
		$('.info').toggle();
		$('.info-wrapper').toggleClass('open');
		return false;
	});
});
