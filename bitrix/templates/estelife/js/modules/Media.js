define(function(){
	var Media={};
	Media.win=(function(){
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
				shadow.click(function(e){
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
			$('body').css('overflow','hidden');
			_win().parent.css('top',(EL.browser().webkit ? $('body') : $('html')).scrollTop()+'px');
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
	Media.Gallery=function(s){
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
					item_desc=$('<p></p>'),
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

				$(document).keyup(function(e){
					var code=e.charCode || e.keyCode;
					if(code==27){
						_hide();
					}
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

			_gallery().item_desc
				.html(items[current].getTitle());

			preview.fadeTo(delay,0.3);
			big.fadeTo(delay,1);

			_gallery().parent
				.parents('.win:first')
				.animate({'scrollTop':'0px'},200);
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

		function _hide(){
			Media.win.close();
		}

		this.setImage=function(image){
			if(typeof image!='object' || !(image instanceof Media.GalleryImage))
				throw 'incorrect media item type';

			items.push(image);
		};

		this.setVideo=function(video){
			if(typeof video!='object' || !(video instanceof Media.GalleryVideo))
				throw 'incorrect media item type';

			items.push(video);
		};

		this.showVideo=function(){
			if(items.length==0)
				throw 'set one or more items';

			var video=false;
			_gallery().big.empty();

			for(var i=0; i<items.length; i++){
				if(items[i] instanceof Media.GalleryVideo){
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

			Media.win.content(_gallery().parent);
			Media.win.open();

			Media.VideoDirect.set('gallery_video',video.getVideoId());
			return true;
		};

		this.showImages=function(){
			if(items.length==0)
				throw 'set one or more items';

			var found=false;
			_gallery().previews.empty();
			_gallery().big.empty();

			$.map(items,function(item,i){
				if(item instanceof Media.GalleryImage){
					_gallery().previews.append('<li'+(i==0 ? ' class="active"':'')+'><img src="'+item.getPreview()+'" /></li>');
					_gallery().big.append('<div class="item"><div class="item-in"><img src="'+item.getBig()+'" /></div></div>');

					if(i==0){
						var title=item.getTitle();

						if(title!='')
							_gallery().item_desc
								.html(title);
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

			Media.win.content(_gallery().parent);
			Media.win.open();

			return true;
		};

		this.hide=_hide;

		this.prev=function(){
			_prev();
		};

		this.next=function(){
			_next();
		};
	};

	Media.GalleryVideo=function(t,d,i){
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

	Media.GalleryImage=function(t,p,b){
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

	Media.VideoDirect=(function(){
		var elements=[],
			videos=[],
			started;

		return {
			'set':function(element_id,video_id){
				elements.push(element_id);
				videos.push(video_id);
			},
			'start':function(){
				if(!started){
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
					started=true;
				}
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

	return Media;
});