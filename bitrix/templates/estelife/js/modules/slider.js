/**
 * Created with JetBrains PhpStorm.
 * User: dmitriy
 * Date: 28.10.13
 * Time: 11:44
 * To change this template use File | Settings | File Templates.
 */
Estelife.prototype.slider=function(list){
	var settings={
		'list':list,
		'gallery':null,
		'first':null
	};
	var flag = false;

	function init(){
		var min = 5555;
		settings.first = 0;
		list.eq(0).css('z-index', 10);
		list.each(function(i){
			var item = $(this),
				height = item.height();

			if (height < min){
				min = height;
			}

		});
		$('.big-photo').css({
			'height': min,
			'overflow': 'hidden'
		});
	}

	function arrow_click_r(){
		if (flag == false){
			flag = true;
			var	item = list.eq(settings.first),
				item_w = item.width(),
				next=settings.first+ 1;

				if (next< list.length){
					var item_next=list.eq(next);
				}else{
					var item_next=list.eq(0);
				}

				var item_next_w=item_next.width();
				item_next.css({'right': 0-item_next_w,'z-index':10});
				item_next.animate(
					{right:0},
					500
				);

				item.animate(
					{right:item_w},
					500,
					function(){
						list.each(function(){
							$(this).css('z-index', 1)
							$(this).css('right', '')
						});
						if (next < list.length){
							list.eq(next).css('z-index', 10);
							settings.first = next;
						}else{
							list.eq(0).css('z-index', 10);
							settings.first = 0;
						}
						flag = false
					}

				);
		}

	}

	function arrow_click_l(){
		if (flag == false){
			flag = true;
			var item=list.eq(settings.first),
				item_w = item.width(),
				prev = settings.first -1;

			if (prev>= 0){
				var item_prev=list.eq(prev);
			}else{
				var item_prev=list.eq(list.length-1);

			}


			var item_prev_w=0-item_prev.width();
			item_prev.css({'left': item_prev_w,'z-index':10});
			item_prev.animate(
				{left:0},
				500
			);

			item.animate(
				{left:item_w},
				500,
				function(){
					list.each(function(){
						$(this).css('z-index', 1)
						$(this).css('left', '')
					});
					if (prev >= 0){
						list.eq(prev).css('z-index', 10);
						settings.first = prev;
					}else{
						list.eq(list.length-1).css('z-index', 10);
						settings.first = list.length-1;
					}
					flag = false
				}

			);
		}


	}

	$('.photo_slider .arrow.left').click(arrow_click_l);
	$('.photo_slider .arrow.right').click(arrow_click_r);
	init();
};