/**
 * Created with JetBrains PhpStorm.
 * User: dmitriy
 * Date: 28.10.13
 * Time: 11:44
 * To change this template use File | Settings | File Templates.
 */
Estelife.prototype.bayan=function(list){
	var settings={
		'list':list,
		'gallery':null
	};

	function init(){
		settings.gallery=$('<div></div>')
			.addClass('el-bayan');
		list.eq(0).before(settings.gallery);
		settings.gallery.append(list);

		var width=settings.gallery.width(),
			height=0;

		list.each(function(i){
			var item=$(this),
				h=item.height();
			if(i==0)
				width-=item.width();
			if(h>height)
				height=h;
		});

		settings.gallery.css({
			'overflow':'hidden',
			'position':'relative'
		}).height(height);
		list.css({
			'position':'absolute'
		});

		var ln=list.length-1,
			size=width/ln;

		for(var i=ln,y=0; i>=0; i--,y++){
			list.eq(i).css({
				'left':'auto',
				'right':(y*size),
				'z-index':y+1
			})
		}

		list.hover(function(){
			var item=$(this);
			item.data('old-z',item.css('z-index'));
			item.css('z-index',ln+2);

			item.data('timeout',setTimeout(function(){
				item.find('.desc').stop().animate({'bottom':'0px'},200);
				item.data('timeout',false);
			},500))
		},function(){
			var item=$(this),
				timeout=item.data('timeout');
			item.css('z-index',item.data('old-z'));
			item.data('old-z',false);

			if(timeout){
				clearTimeout(timeout);
			}

			item.find('.desc').stop().animate({'bottom':'-'+item.height()+'px'});
		});
	}

	init();
};