define(['utils/System', 'utils/Animation'], function(System, Animation){
	return {
		floatPanel: function(element) {
			if (!element.length)
				return;

			$(document).ready(function() {
				slideLogoDescription(0);
			}).scroll(slideLogoDescription);

			function slideLogoDescription(speed){
				var html = document.documentElement,
					body = document.body;
				speed = speed && (typeof speed != 'object') && parseInt(speed);
				speed = (speed !== false && !isNaN(speed)) ? speed  : 100;

				var scrollLine = 100,
					scrollTop = html && html.scrollTop || body && body.scrollTop || 0,
					elementScrollTop = element.scrollTop();

				if (scrollTop >= scrollLine && !elementScrollTop) {
					var scrollStep = element.children().eq(0).outerHeight();
					element.stop().animate({scrollTop: scrollStep+'px'}, speed);
				} else if (scrollTop < scrollLine && elementScrollTop) {
					element.stop().animate({scrollTop: '0px'}, speed);
				}
			}
		},

		mainMenu: function(menuElement) {
			if (!menuElement.length)
				return;

			menuElement.find('a').click(function(e){
				var link = $(this),
					parent = link.parent();

				menuElement.find('li.active').not(parent).removeClass('active');

				if (parent.hasClass('active')) {
					hideSubmenu(link, function(){
						parent.removeClass('active');
					});
				} else {
					parent.addClass('active');
					showSubmenu(link, parent.find('.sub-menu'));
				}

				e.preventDefault();
			});

			function showSubmenu(link, submenuElement) {
				if (!submenuElement.length)
					return;

				var cloneMenu = submenuElement.clone(true),
					existsMenu = menuElement.find('.menu').next('.sub-menu');

				if (!existsMenu.length) {
					cloneMenu.hide();
					menuElement.append(cloneMenu);

					var cloneSize = System.getSize(cloneMenu),
						linkSize = System.getSize(link, true),
						menuOffset = menuElement.offset(),
						linkOffset = link.offset();

					linkOffset.left = linkOffset.left - menuOffset.left;
					linkOffset.top = linkOffset.top - menuOffset.top;

					cloneMenu.find('.col').hide();
					cloneMenu.width(linkSize.width).css({
						left: linkOffset.left +'px'
					}).height(0).show();

					var animProcess = new Animation.Process(cloneMenu.get(0), {
						animationType: Animation.Types.linear,
						duration: 100
					});
					animProcess.run('height', cloneSize.height+'px', function(){
						animProcess.run('width', cloneSize.width+'px',function() {
							cloneMenu.find('.col').fadeIn(50);
						});
						animProcess.run('left', 0);
					});
				} else {
					existsMenu.replaceWith(cloneMenu);
				}
			}

			function hideSubmenu(link, endCallback) {
				var existsMenu = menuElement.find('.menu').next('.sub-menu');

				if (!existsMenu.length)
					return;

				var linkSize = System.getSize(link, true),
					menuOffset = menuElement.offset(),
					linkOffset = link.offset();

				linkOffset.left = linkOffset.left - menuOffset.left;
				linkOffset.top = linkOffset.top - menuOffset.top;

				existsMenu.find('.col').hide();
				var animProcess = new Animation.Process(existsMenu.get(0), {
					animationType: Animation.Types.linear,
					duration: 100
				});
				animProcess.run('width', linkSize.width+'px', function(){
					animProcess.run('height', 0, function(){
						existsMenu.remove();
						if (endCallback && typeof endCallback == 'function')
							endCallback();
					});
				});
				animProcess.run('left', linkOffset.left+'px');
			}
		},

		toCart: function(element) {
			if (!element.length)
				return;


		}
	}
});
