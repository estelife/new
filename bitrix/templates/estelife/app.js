$(document).ready(function() {

	var timerID = 0,
		timer2ID = 0;

	//табы для раскрытия информации
	$('body').on('click', '.el-tab h3', function(){
		var prnt = $(this).parent(),
			el = $('a',$(this));


		if (el.hasClass('active')){
			el.removeClass('active');
			prnt.find('h3').next().slideUp('700').addClass('none');
		}else{
			el.addClass('active');
			prnt.find('h3').next().slideDown('700').removeClass('none');
		}

		return false
	});

	$(".menu>li.active1").addClass("active");
	
	$(".menu a").each(function() {
		var href = $(this).attr("href"),
			path_name = document.location.pathname.split('/'),
			reg=new RegExp('^.*'+path_name[1]+'.*$'),
			matches=href.match(reg);

		if (matches && !$(".menu>li.active").length) {
			$(this).closest(".menu>li").addClass("active").addClass('main');
			return;
		}
	});

	if (!$(".menu li.active").length) {
		$(".menu li:first").addClass("active");
	}
	
	$(".head .menu>li").hover(
		function() {
			var ob = this;
			clearTimeout(timerID);
			clearTimeout(timer2ID);
			timer2ID = setTimeout(function(){
					$(".menu>li").removeClass("active");
					$(ob).addClass("active");
				},
				150
			);


		},
		function() {
			timerID = setTimeout(function(){
				$(".menu>li").removeClass("active");
				$(".menu>li.main").addClass('active');
			},
			450
			);
		}
	);

	$(".half-video .video img").click(function() {
		var r = $(this).attr("rel");
		var frame = "<iframe width='481' height='274' src='"+r+"' frameborder='0' allowfullscreen></iframe>";
		$(".half-video .frame").html(frame);
		$(".half-video .video").removeClass("active");
		$(this).parent().addClass("active");
	});
	
	$(".half-video .video:first img").click();
});

var showDetail;

$(function home(){
	//Переход на детальную страницу
	$('.items .item').click(function(e){
		var target= $(e.target),
			link = $(this).find('a:first').attr('href');

		if(target[0].tagName!='A' && link.length>0)
			document.location.href=link;
	});

	$('.col2 .img').click(function(e){
		var target= $(e.target),
			link = $(this).find('a:first').attr('href');

		if(target[0].tagName!='A' && link.length>0)
			document.location.href=link;
	});

	//переключение между пунктами меню в эксперном мнении
	$('.experts .menu li').click(function(){
		var prnt = $(this).parent().parent();
			col = $('.experts .menu li'),
			index = col.index($(this));

		col.removeClass('active').eq(index).addClass('active');
		$('.item',prnt).addClass('none').eq(index).removeClass('none');

		return false;
	});

	//Переключение между вкладками
	$('.articles .menu li').click(function(){
		var prnt = $(this).parents('.articles:first'),
			col = $('.menu li',prnt),
			index = col.index($(this));

		$('.menu li',prnt).removeClass('active');
		$('.menu li',prnt).eq(index).addClass('active')
		$('.items' ,prnt).addClass('none');
		$('.items' ,prnt).eq(index).removeClass('none');

		var section_url = $('.items' ,prnt).eq(index).attr('rel');
		prnt.find('.title a').attr('href', section_url);

		return false;
	});

	//переключения в галереи
	$('body').on('click', '.media .menu a', function(){
		var lnk=$(this),
			rel=lnk.attr('rel');

		EL.loadModule('templates',function(){
			var detail_generator=new EL.templates({
				'path':'/api/estelife_ajax.php',
				'template':'photogallery',
				'params':{
					'action':'get_template'
				}
			});

			lnk.parents('.menu').find('.active').removeClass('active');
			lnk.addClass('.active');

			$.get('/api/estelife_ajax.php',{
				'action':'get_media',
				'params':{
					'photo': (rel=="ONLY_PHOTO" || rel=='ALL') ? 'Y' : 'N',
					'video': (rel=="ONLY_VIDEO" || rel=='ALL') ? 'Y' : 'N'
				}
			},function(r){
				if(r.result){
					detail_generator.ready(function(){
						var html = detail_generator.make(r);
						$('.media').find('.items')
							.html(html);
					});
				}else{
					alert('Ошибка получения фотогалереи');
				}
			},'json');
		});

		return false;
	});

	//Переключение между табами
	$('.menu_tab ul li').click(function(){
		var col = $('.menu_tab ul li'),
		index = col.index($(this));

		col.removeClass('active');
		$(this).addClass('active');

		$('.tabs').addClass('none');
		$('.tabs').eq(index).removeClass('none');

		return false;
	})
});


$(function(){
	var bd=$('body');

	EL.loadModule(['ajaxSupport','url','Geo'],function(){
		EL.Geo.addEventListener({
			onCityChange:function(city){
				$('.cities li').removeClass('active');
				$('.cities a.'+city.ID).parent().addClass('active');

				$('.change_city span').html(city.NAME).attr('class', 'city_'+city.ID);
				$('.cities').addClass('none').removeClass('cities_open');

				getPromotions(city.ID);
			}
		});

		//Вывод списка городов в шапке
		$('.change_main_city').click(function(){
			EL.Geo.load(
				EL.Geo.Adapters.createAdapter('main')
			);
			return false;
		});

		//Вывод списка городов для акций
		$('.change_promotions_city').click(function(){
			EL.Geo.load(
				EL.Geo.Adapters.createAdapter('promotions')
			);
			return false;
		});

		var url=new EL.url();
		//подсветка урлов второго уровня
		var city_url = url.getQuery(document.location.pathname).city,
			reg=new RegExp('^.*(city=\\d*).*$');

		$(".submenu a").each(function(){
			var href = $(this).attr("href"),
				matches=href.match(reg);
			if(matches){
				if (matches[1] == 'city='+city_url){
					$(this).closest(".submenu>li").addClass("second_active");
					return;
				}
			}else{
				if ((href.indexOf(document.location.pathname) == 0) && !$(".submenu>li.active").length && document.location.pathname !='/' && document.location.pathname==href){
					$(this).closest(".submenu>li").addClass("second_active");
					return;
				}
			}
		});
	});

	initFilter($('.filter'));
});

function getPromotions(city){
	EL.loadModule('templates',function(){
		var detail_generator=new EL.templates({
			'path':'/api/estelife_ajax.php',
			'template':'promotions_index',
			'params':{
				'action':'get_template'
			}
		});

		$.get('/api/estelife_ajax.php',{
			'action':'get_promotions_index',
			'city':city
		},function(r){
			if(r.complete){
				detail_generator.ready(function(){
					var h = detail_generator.make(r.complete);
					if (h.length>0){
						$('.promotions.announces .items').html(h);
						$('.more_promotions').attr('href','/promotions/?city='+city);
					}else{
						console.log('Ошибка получения html')
					}
				});
			}else{
				console.log('Ошибка получения городов')
			}
		},'json');
	});
}

function initFilter(context){
	$('.text.date',context).each(function(){
		var current=$(this),
			img=current.find('img'),
			prnt=current.parent(),
			from=current.hasClass('from'),
			other=(from) ?
				prnt.find('.text.date:last') :
				prnt.find('.text.date:first');

		current.find('input').datepicker({
			numberOfMonths: 1,
			dateFormat: 'dd.mm.y',
			isRTL:(!from),
			onClose: function( selectedDate ) {
				other.find('input').datepicker(
					"option",
					(from ? 'minDate' : 'maxDate'),
					selectedDate
				);
			}
		});

		img.click(function(){
			$(this).parent().find('input').datepicker('show');
			return false;
		});
	});

	EL.loadModule('select',function(){
		$('select',context).each(function(){
			new EL.select($(this));
		});
	});

	$('select[data-rules]').change(function(){
		var current=$(this),
			val=current.val(),
			name=current.attr('name'),
			rules=current.attr('data-rules');

		if(!(rules=rules.match(/[\w\d\-_]+\:[^;]+/gi)))
			throw 'incorrect rule for linked fields';

		var temp=null,
			params={};
		params[name]=val;

		for(var i=0; i<rules.length; i++){
			temp=rules[i].split(':');
			params.action=temp[0];

			$.get(
				'/api/estelife_ajax.php',
				params,
				(function(selector){
					return function(r){
						var child = $(selector),
							prnt=child.parent();

						prnt.addClass('disabled');
						child.find('option:not(:first)').remove();

						if('list' in r && r.list.length>0){
							for(var i= 0; i< r.list.length; i++)
								child.append('<option value="'+ r.list[i].value+'">'+ r.list[i].label+'</option>');

							prnt.removeClass('disabled');
						}
					};
				})(temp[1]),
				'json'
			);
		}
	});

	var input=$('input[name=name]',context);

	if(input.length>0){
		EL.loadModule('autocompleteFilter',function(){
			new EL.autocompleteFilter(
				'input[name=name]',
				input.attr('data-action')
			);
		});
	}
}

function activateMenuLink(lnk,title){
	if(lnk.length<=0)
		return;

	var prnt=lnk.parents('.menu:first'),
		sub=(lnk.parents('.submenu:first').length>0),
		setTitle=!title;

	prnt.find('.main,.active,.second_active')
		.removeClass('main active second_active');

	if(sub){
		lnk.parent()
			.addClass('second_active')
			.parents('li:first').addClass('main active');
	}else{
		lnk.parent()
			.addClass('main active');
	}

	if(setTitle)
		$('.content .block-header h1').html(lnk.attr('title'));
}

function getFilterForm(type,query){
	var temp=(query && typeof 'object') ?
			query : {},
		data=$.extend({
			'action':'get_filter_data',
			'filter':type
		},temp);

	$.getJSON(
		'/api/estelife_ajax.php',
		data,
		function(r){
			var el_filter=$('.el-filter');

			if('filter' in r){
				var filter_generator=new EL.templates({
					'path':'/api/estelife_ajax.php',
					'template':type+'_filter',
					'params':{
						'action':'get_template'
					}
				});

				filter_generator.ready(function(){
					el_filter.remove();
					$('.sidebar:first').prepend(
						filter_generator.make(r.filter)
					);
					el_filter=$('.el-filter');
					initFilter(el_filter);
				});
			}else{
				el_filter.remove();
			}
		}
	);
}

$(function(){
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

	EL.videoDirect.start();

	$('.media .items').on('click','.item',function(e){
		var link=$(this),
			id=link.attr('data-id'),
			video=link.hasClass('video');

		if(!id)
			return;

		$.post('/api/estelife_ajax.php',{
			'action':'get_media_content',
			'id':id,
			'video':(video) ? 1 : 0
		},function(r){
			if('images' in r){
				var m=new EL.media({
					'title': r.gallery.name,
					'description': r.gallery.description
				});

				$.map(r.images,function(item){
					m.setImage(new EL.mediaImage(
						item.title,
						item.small,
						item.big
					));
				});

				m.showImages();
			}else if('video' in r){
				var m=new EL.media();

				m.setVideo(new EL.mediaVideo(
					r.video.title,
					r.video.description,
					r.video.video_id
				));
				m.showVideo();
			}
		},'json');

		e.preventDefault();
	});

	var icons={
		'default':'/bitrix/templates/estelife/images/icons/point.png'
	};

	$('.map').each(function(){
		var origin=$(this),
			jmap=origin.clone(),
			map=new VMap(),
			lat=$('span.lat',jmap).html(),
			lng=$('span.lng',jmap).html();

		if(!lat || !lng){
			jmap.hide();
			return;
		}

		jmap.css({
			'visibility':'hidden',
			'position':'absolute',
			'left':'-100000px'
		});
		$('body').append(jmap);

		map.markers().icons(icons);
		map.create(jmap,lat,lng);
		map.zoom(16);

		map.markers().add(new map.marker(lat,lng));
		map.markers().draw();

		map.load(function(){
			jmap.css({
				'visibility':'visible',
				'position':'relative',
				'left':0
			});
			origin.replaceWith(jmap);
		});
	});
});