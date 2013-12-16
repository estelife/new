/**
 * Created with JetBrains PhpStorm.
 * User: dmitriy
 * Date: 13.12.13
 * Time: 12:22
 * To change this template use File | Settings | File Templates.
 */
Estelife.prototype.win=(function(){
	var win;

	function _win(){
		if(!win){
			var wrap=$('<div class="win-wrap"></div>'),
				shadow=$('<div class="win-shadow"></div>'),
				close=$('<a href="#" class="win-close"></a>'),
				content=$('<div class="win-content"></div>'),
				parent=$('<div class="win"></div>');

			parent.append(wrap,shadow)
				.height($(window).height());

			wrap.append(close,content);
			parent.hide();

			$('body').append(parent);
			$(window).resize(function(){
				parent.height($(this).height());
			});

			close.click(function(e){
				_hide();
				e.preventDefault();
			});

			win={
				'parent':parent,
				'content':content,
				'shadow':shadow
			}
		}
		return win;
	}

	function _hide(){
		$('body').css('overflow','auto');
		_win().parent.hide();
	}

	function _show(){
		var bd=$('body');
		bd.css('overflow','hidden');
		_win().parent.css('top',bd.scrollTop()+'px');
		_win().parent.show();
	}

	return {
		content:function(c){
			_win().content
				.empty()
				.append(c)
		},
		open:function(){
			_show();
		},
		close:function(){
			_hide();
		}
	}
})();
Estelife.prototype.media=function(s){
	var gallery,
		settings=(typeof s=='object') ? s : {},
		items=[],
		current=0;

	function _gallery(){
		if(!gallery){
			var parent=$('<div class="win-gallery gallery"></div>'),
				head=$('<div class="win-gallery-head"><a href="#"></a><span></span></div>'),
				stat=$('<ul class="stat"><li class="likes"><span></span><i></i></li><li class="unlikes"><span></span><i></i></li></ul>'),
				big_item=$('<div class="win-gallery-in"><div class="win-gallery-items"></div><a href="#" class="arrow left">Назад<i></i></a><a href="#" class="arrow right">Вперед<i></i></a></div>'),
				arrows=big_item.find('a').hide(),
				item_desc=$('<p></p>').hide(),
				previews=$('<ul class="win-gallery-previews">').hide();

			head.append(stat);
			parent.append(
				head,
				big_item,
				item_desc,
				previews,
				'<div class="win-gallery-bottom-fix"></div>'
			);

			gallery={
				'parent':parent,
				'arrows':{
					'right':arrows.eq(1),
					'left':arrows.eq(0)
				},
				'big':big_item.find('.win-gallery-items'),
				'previews':previews,
				'likes':stat.find('.likes').hide(),
				'unlikes':stat.find('.unlikes').hide(),
				'name':head.find('a'),
				'description':head.find('span').eq(0),
				'item_desc':item_desc
			};

			if('title' in settings)
				gallery.name.html(settings.title);

			if('link' in settings)
				gallery.name.attr('href',settings.link);

			if('description' in settings)
				gallery.description.html(settings.description);

			if('likes' in settings)
				gallery.likes.find('span').html(settings.likes);

			if('unlikes' in settings)
				gallery.unlikes.find('span').html(settings.unlikes);

			gallery.previews.on('click','li',function(e){
				var li=$(this),
					lis=gallery.previews.find('li');

				_showItem(lis.index(li));
				e.preventDefault();
			});
			gallery.arrows.left.click(function(e){
				_prev();
				e.preventDefault();
			});
			gallery.arrows.right.click(function(e){
				_next();
				e.preventDefault();
			});
		}

		return gallery;
	}

	function _showItem(item){
		var delay=300,
			preview=_gallery().previews
				.find('li')
				.eq(current),
			big=_gallery().big
				.find('.item')
				.eq(current);

		preview.fadeTo(delay,1);
		big.fadeTo(delay,0);

		current=item;

		preview=_gallery().previews
			.find('li')
			.eq(current);
		big=_gallery().big
			.find('.item')
			.eq(current);

		preview.fadeTo(delay,0.3);
		big.fadeTo(delay,1);
	}

	function _prev(){
		var prev=(current==0) ?
			items.length-1 :
			current-1;

		_showItem(prev);
	}

	function _next(){
		var next=current+1;

		if(next>=items.length)
			next=0;

		_showItem(next);
	}

	this.setImage=function(image){
		if(typeof image!='object' || !(image instanceof EL.mediaImage))
			throw 'incorrect media item type';

		items.push(image);
	};

	this.setVideo=function(video){
		if(typeof video!='object' || !(video instanceof EL.mediaVideo))
			throw 'incorrect media item type';

		items.push(video);
	};

	this.showVideo=function(){
		if(items.length==0)
			throw 'set one or more items';

		var video=false;
		_gallery().big.empty();

		for(var i=0; i<items.length; i++){
			if(items[i] instanceof EL.mediaVideo){
				_gallery().big.append('<div class="item"><div class="item-in"><div id="gallery_video"></div></div></div>');
				video=items[i];
				break;
			}
		}

		if(!video)
			return false;

		_gallery().previews.hide();
		_gallery().arrows.left.hide();
		_gallery().arrows.right.hide();
		_gallery().item_desc.hide();

		_gallery().name.html(video.getTitle());
		_gallery().description.html(video.getDescription());


		EL.win.content(_gallery().parent);
		EL.win.open();

		EL.videoDirect.set('gallery_video',video.getVideoId());
		return true;
	};

	this.showImages=function(){
		if(items.length==0)
			throw 'set one or more items';

		var found=false;
		_gallery().previews.empty();
		_gallery().big.empty();

		$.map(items,function(item,i){
			if(item instanceof EL.mediaImage){
				_gallery().previews.append('<li'+(i==0 ? ' class="active"':'')+'><img src="'+item.getPreview()+'" /></li>');
				_gallery().big.append('<div class="item"><div class="item-in"><img src="'+item.getBig()+'" /></div></div>');

				if(i==0){
					var title=item.getTitle();

					if(title!='')
						_gallery().item_desc
							.html(title)
							.show();
				}

				found=true;
			}
		});

		if(!found)
			return false;

		if(items.length>1){
			_gallery().big.find('.item:not(:first)').hide();
			_gallery().previews.show();
			_gallery().arrows.left.show();
			_gallery().arrows.right.show();
		}

		EL.win.content(_gallery().parent);
		EL.win.open();

		return true;
	};

	this.hide=function(){
		EL.win.hide();
	};

	this.prev=function(){
		_prev();
	};

	this.next=function(){
		_next();
	};
};

Estelife.prototype.mediaVideo=function(t,d,i){
	var title=t||'',
		description=d||'',
		video_id=i||'';

	this.getTitle=function(){
		return title;
	};

	this.getDescription=function(){
		return description;
	};

	this.getVideoId=function(){
		return video_id;
	};
};

Estelife.prototype.mediaImage=function(t,p,b){
	var title=t||'',
		preview=p||'',
		big=b||'';

	this.getTitle=function(){
		return title;
	};

	this.getPreview=function(){
		return preview;
	};

	this.getBig=function(){
		return big;
	}
};

Estelife.prototype.videoDirect=(function(){
	var elements=[],
		videos=[];

	return {
		'set':function(element_id,video_id){
			elements.push(element_id);
			videos.push(video_id);
		},
		'start':function(){
			var ob=this,v,player;
			setTimeout(function(){
				if(v=ob.next()){
					player=new YT.Player(v.element,{
						height:540,
						width:770,
						videoId: v.video
					})
				}
				ob.start();
			},100);
		},
		'next':function(){
			if(videos.length>0){
				return {
					'element':elements.shift(),
					'video':videos.shift()
				}
			}
			return false;
		}
	};
})();