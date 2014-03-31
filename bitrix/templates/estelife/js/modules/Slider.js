define(function(){
	return function(list){
		var settings={
			'list':list,
			'gallery':null,
			'first':null
		};
		var flag = false,
			desc;

		function init(){
			var min = 55555;
			settings.first = 0;
			list.eq(0).css('z-index', 10);
			list.each(function(i){
				var item = $(this),
					height = item.height();

				if (height < min){
					min = height;
				}

			});
			$('.gallery-in .item').css({
				'height': min,
				'overflow': 'hidden'
			});
		}

		function arrow_click_r(){
			if (list.length<=1)
				return false;
			if (flag == false){
				flag = true;
				var	item_next,
					item=list.eq(settings.first),
					item_w=item.width(),
					next=settings.first+ 1;

					if (next< list.length){
						item_next=list.eq(next);
					}else{
						item_next=list.eq(0);
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
								$(this).css('z-index', 1);
								$(this).css('right', '')
							});
							if (next < list.length){
								list.eq(next).css('z-index', 10);
								settings.first = next;
								desc = list.eq(next).find('.desc').html();
							}else{
								list.eq(0).css('z-index', 10);
								settings.first = 0;
								desc = list.eq().find('.desc').html();
							}
							flag = false
						}

					);
					$('.gallery-desc').html(desc);
			}

			return false;

		}

		function arrow_click_l(){
			if (list.length<=1)
				return false;
			if (flag == false){
				flag = true;
				var item_prev,
					item=list.eq(settings.first),
					item_w = item.width(),
					prev = settings.first -1;

				if (prev>= 0){
					item_prev=list.eq(prev);
				}else{
					item_prev=list.eq(list.length-1);

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
							$(this).css('z-index', 1);
							$(this).css('left', '')
						});
						if (prev >= 0){
							list.eq(prev).css('z-index', 10);
							settings.first = prev;
							desc = list.eq(prev).find('.desc').html();
						}else{
							list.eq(list.length-1).css('z-index', 10);
							settings.first = list.length-1;
							desc = list.eq().find('.desc').html();
						}
						flag = false
					}

				);

				$('.gallery-desc').html(desc);

			}

			return false;

		}

		$('body').on('click', '.gallery .arrow.left',arrow_click_l);
		$('body').on('click', '.gallery .arrow.right',arrow_click_r);
		init();
	};
});