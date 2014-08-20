jQuery(function() {
	/* Init inview */
    $('.anim-el').on('inview', function(event, isVisible) {
		if (isVisible) {
			$(this).addClass('is-v');
		} else {
			$(this).removeClass('is-v');
		}
	});
	
});