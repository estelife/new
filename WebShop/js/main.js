require([
	'utils/Effects'
], function(Effects){
	Effects.floatPanel($('.top .col'));
	Effects.mainMenu($('.main-menu'));

	$('.top .user').bind('click', function(e){
		var prnt = $(this).parent(),
			ul = prnt.find('ul');

		if (!ul.length)
			return;

		if (prnt.hasClass('active')) {
			prnt.removeClass('active');
			ul.stop().animate({top: '-='+(ul.outerHeight())+'px'}, 150, 'swing', function(){
				prnt.find('.pseudo-block').hide();
			});
		} else {
			prnt.addClass('active');
			prnt.find('.pseudo-block').show();
			ul.stop().animate({top: '0px'}, 150);
		}

		e.stopPropagation();
	});
	$(document).bind('click', function() {
		var link = $('.top .user');

		if (link.parent().hasClass('active'))
			link.click();
	});
	$('.item .button').click(function(e){
		Effects.toCart($(this).parents('.item:first'));
		e.preventDefault();
	});

	$('.float-cart .scroll').jScrollPane({
		hideFocus: true,
		autoReinitialise: true,
		autoReinitialiseDelay: 200,
		mouseWheelSpeed: 30
	});
});