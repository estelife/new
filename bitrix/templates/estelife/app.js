
//$(function home(){


	//Переход на детальную страницу
//	$('body').on('click', '.items .item', function(e){
//		var target= $(e.target),
//			link = $(this).find('a:first').attr('href')||'';
//
//		if(target[0].tagName!='A' && link.length>0)
//			document.location.href=link;
//	});

//	$('body').on('click', '.col2 .img', function(e){
//		var target= $(e.target),
//			link = $(this).find('a:first').attr('href');
//
//		if(target[0].tagName!='A' && link.length>0)
//			document.location.href=link;
//	});

//	//переключение между пунктами меню в эксперном мнении
//	$('body').on('click','.experts .menu li',function(){
//		var prnt = $(this).parent().parent();
//			col = $('.experts .menu li'),
//			index = col.index($(this));
//
//		col.removeClass('active').eq(index).addClass('active');
//		$('.item',prnt).addClass('none').eq(index).removeClass('none');
//
//		return false;
//	});

//	//Переключение между вкладками
//	$('body').on('click','.articles .menu li', function(){
//		var prnt = $(this).parents('.articles:first'),
//			col = $('.menu li',prnt),
//			index = col.index($(this));
//
//		$('.menu li',prnt).removeClass('active');
//		$('.menu li',prnt).eq(index).addClass('active')
//		$('.items' ,prnt).addClass('none');
//		$('.items' ,prnt).eq(index).removeClass('none');
//
//		var section_url = $('.items' ,prnt).eq(index).attr('rel');
//		prnt.find('.title a').attr('href', section_url);
//
//		return false;
//	});

//	//табы для раскрытия информации
//	$('body').on('click', '.el-tab h3', function(){
//
//		var prnt = $(this).parent(),
//			el = $('a',$(this));
//
//
//		if (el.hasClass('active')){
//			el.removeClass('active');
//			prnt.find('h3').next().slideUp('700').addClass('none');
//		}else{
//			el.addClass('active');
//			prnt.find('h3').next().slideDown('700').removeClass('none');
//		}
//
//		return false
//	});

	//переключения в галереи
//	$('body').on('click', '.media .menu a', function(){
//		var lnk=$(this),
//			rel=lnk.attr('rel');
//
//		EL.loadModule('templates',function(){
//			var detail_generator=new EL.templates({
//				'path':'/api/estelife_ajax.php',
//				'template':'photogallery',
//				'params':{
//					'action':'get_template'
//				}
//			});
//
//			lnk.parents('.menu').find('.active').removeClass('active');
//			lnk.addClass('.active');
//
//			$.get('/api/estelife_ajax.php',{
//				'action':'get_media',
//				'params':{
//					'photo': (rel=="ONLY_PHOTO" || rel=='ALL') ? 'Y' : 'N',
//					'video': (rel=="ONLY_VIDEO" || rel=='ALL') ? 'Y' : 'N'
//				}
//			},function(r){
//				if(r.result){
//					detail_generator.ready(function(){
//						var html = detail_generator.render(r);
//						$('.media').find('.items')
//							.html(html);
//					});
//				}else{
//					alert('Ошибка получения фотогалереи');
//				}
//			},'json');
//		});
//
//		return false;
//	});

//	//Переключение между табами
//	$('body').on('click','.menu_tab ul li',function(){
//		var col = $('.menu_tab ul li'),
//		index = col.index($(this));
//
//		col.removeClass('active');
//		$(this).addClass('active');
//
//		$('.tabs').addClass('none');
//		$('.tabs').eq(index).removeClass('none');
//
//		return false;
//	})
//});


//$(function(){
//	var bd=$('body');
//
//	EL.loadModule('Geo',function(){
//		EL.Geo.addEventListener({
//			onCityChange:function(city){
//				$('.cities li').removeClass('active');
//				$('.cities a.'+city.ID).parent().addClass('active');
//
//				$('.change_city span').html(city.NAME).attr('class', 'city_'+city.ID);
//				$('.cities').addClass('none').removeClass('cities_open');
//
//				getPromotions(city.ID);
//			}
//		});
//
//		//Вывод списка городов в шапке
//		$('body').on('click','.change_main_city', function(){
//			var lnk=$(this);
//
//			if(lnk.hasClass('active'))
//				lnk.removeClass('active');
//			else
//				lnk.addClass('active');
//
//			EL.Geo.load(
//				EL.Geo.Adapters.createAdapter('main')
//			);
//			return false;
//		});
//
//		//Вывод списка городов для акций
//		$('body').on('click','.change_promotions_city', function(){
//			var lnk=$(this);
//
//			if(lnk.hasClass('active'))
//				lnk.removeClass('active');
//			else
//				lnk.addClass('active');
//
//			EL.Geo.load(
//				EL.Geo.Adapters.createAdapter('promotion')
//			);
//			return false;
//		});

//		//меню
//		EL.SystemSettings.ready(function(s){
//			$(".main_menu a").each(function() {
//				var href = $(this).attr("href"),
//					path_name = document.location.pathname.split('/'),
//					reg,matches;
//
//				if(!_.isEmpty(path_name[1])){
//					reg=new RegExp('^.*'+path_name[1]+'.*$');
//					matches=href.match(reg);
//				}
//
//				if (matches && !$(".main_menu>li.active").length) {
//					$(this).closest(".main_menu>li").addClass("active").addClass('main');
//					return false;
//				}else{
//					var mass = s.directions;
//					path_name = path_name[1];
//					reg=new RegExp('^([a-z]{2})[0-9]+$');
//					matches=path_name.match(reg);
//
//					if (matches && mass[matches[1]].length>0){
//						reg=new RegExp('^.*'+mass[matches[1]]+'.*$');
//						var href_matches=href.match(reg);
//
//						if (href_matches && !$(".main_menu>li.active").length){
//							$(this).closest(".main_menu>li").addClass("active").addClass('main');
//							return false;
//						}
//					}
//				}
//			});
//
//			//подсветка урлов второго уровня
//			$(".submenu a").each(function(){
//				var href = $(this).attr("href"),
//					path_name = document.location.href.split('/').slice(3).join('/');
//				path_name = '/'+path_name;
//
//				if (href==path_name || path_name == '/apparatuses-makers/') {
//					if (path_name == '/apparatuses-makers/'){
//						$('.submenu li a[href="/preparations-makers/"]').parent().addClass("second_active").parent().parent().addClass("active").addClass('main');
//					}else{
//						$(this).closest(".submenu>li").addClass("second_active");
//					}
//					return false;
//				}else{
//
//					var mass = s.directions,
//						reg=new RegExp('^([a-z]{2})[0-9]+$');
//					path_name = document.location.pathname.split('/').slice(1, -1).pop();
//
//					if (path_name){
//						var matches=path_name.match(reg);
//
//						if ((matches && mass[matches[1]].length>0) || (matches && matches[1]=='am')){
//
//							if (matches[1]=='am'){
//								$('.submenu li a[href="/preparations-makers/"]').parent().addClass("second_active").parent().parent().addClass("active").addClass('main');
//							}else{
//								reg=new RegExp('^\/'+mass[matches[1]]+'\/$');
//								var href_matches=href.match(reg);
//
//								if (href_matches){
//									$(this).closest(".submenu>li").addClass("second_active");
//									return false;
//								}
//							}
//						}
//					}
//				}
//			});
//		});

//	});
//
//	initFilter($('.filter'));
//});

//function getPromotions(city){
//	EL.loadModule('templates',function(){
//		var tpl=new EL.templates({
//			'path':'/api/estelife_ajax.php',
//			'template':'promotions_index',
//			'params':{
//				'action':'get_template'
//			}
//		});
//
//		$.get('/api/estelife_ajax.php',{
//			'action':'get_promotions_index',
//			'city':city
//		},function(r){
//			if(r.list){
//				tpl.ready(function(){
//					tpl.set('list',r.list);
//					var h = tpl.render();
//					if (h.length>0){
//						$('.promotions.announces .items').html(h);
//						$('.more_promotions').attr('href','/promotions/?city='+city);
//					}else{
//						console.log('Ошибка получения html')
//					}
//				});
//			}else{
//				console.log('Ошибка получения городов')
//			}
//		},'json');
//	});
//}

//$(function(){
	/*var city_id;
	EL.geolocation(
		function(position){
			var myLatlng = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
			var map=new VMap();
			map.geocode(myLatlng,map.latlng);

			map.on('geocode_true', function(lat, lng, address){
				if (address.city){
					$.get('/api/estelife_ajax.php',{
						'action':'geolocation',
						'city':address.city
					},function(r){
						if (r.list.length == 1){
							city_id = r.list.city;
						}else{
							city_id = EL.cookie._get('estelife_city');
						}
					},'json');
				}else{
					city_id = EL.cookie._get('estelife_city');
				}
			});
		},
		function(){
			city_id = EL.cookie._get('estelife_city');
		}
	)*/

//	if($('.media').length>0){
//		EL.loadModule('win',function(){
//			EL.videoDirect.start();
//
//			$('.media .items').on('click','.item',function(e){
//				var link=$(this),
//					id=link.attr('data-id'),
//					video=link.hasClass('video');
//
//				if(!id)
//					return;
//
//				$.post('/api/estelife_ajax.php',{
//					'action':'get_media_content',
//					'id':id,
//					'video':(video) ? 1 : 0
//				},function(r){
//					if('images' in r){
//						var m=new EL.media({
//							'title': r.gallery.name,
//							'description': r.gallery.description
//						});
//
//						$.map(r.images,function(item){
//							m.setImage(new EL.mediaImage(
//								item.title,
//								item.small,
//								item.big
//							));
//						});
//
//						m.showImages();
//					}else if('video' in r){
//						var m=new EL.media();
//
//						m.setVideo(new EL.mediaVideo(
//							r.video.title,
//							r.video.description,
//							r.video.video_id
//						));
//						m.showVideo();
//					}
//				},'json');
//
//				e.preventDefault();
//			});
//		});
//	}
//
//	var icons={
//		'default':'/bitrix/templates/estelife/images/icons/point.png'
//	};
//
//	$('.map').each(function(){
//		var origin=$(this),
//			jmap=origin.clone(),
//			map=new VMap(),
//			lat=$('span.lat',jmap).html(),
//			lng=$('span.lng',jmap).html();
//
//		if(!lat || !lng){
//			jmap.hide();
//			return;
//		}
//
//		jmap.css({
//			'visibility':'hidden',
//			'position':'absolute',
//			'left':'-100000px'
//		});
//		$('body').append(jmap);
//
//		map.markers().icons(icons);
//		map.create(jmap,lat,lng);
//		map.zoom(16);
//
//		map.markers().add(new map.marker(lat,lng));
//		map.markers().draw();
//
//		map.load(function(){
//			jmap.css({
//				'visibility':'visible',
//				'position':'relative',
//				'left':0
//			});
//			origin.replaceWith(jmap);
//		});
//	});
//});