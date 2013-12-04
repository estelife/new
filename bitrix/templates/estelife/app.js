$(document).ready(function() {

	var timerID = 0,
		timer2ID = 0;

	//табы для раскрытия информации
	$('body').on('click', '.el-tab div', function(){
		var prnt = $(this).parent(),
			el = $('span',$(this));

		if (el.hasClass('open')){
			el.removeClass('open').addClass('close');
			prnt.find('p').slideUp('700').addClass('none');
		}else if (el.hasClass('close')){
			el.removeClass('close').addClass('open');
			prnt.find('p').slideDown('700').removeClass('none');
		}
	});

	
	$(".menu>li:last").addClass("last");
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

	//Вывод списка городов
	$('.change_city').click(function(){
		var col = $('.change_city'),
			index = col.index($(this)),
			prnt = $('.main_cities');

		if (prnt.html() != ''){
			if (prnt.hasClass('cities_open'))
				prnt.addClass('none').removeClass('cities_open');
			else
				prnt.removeClass('none').addClass('cities_open');
		}else{

			EL.loadModule('templates',function(){
				var detail_generator=new EL.templates({
					'path':'/api/estelife_ajax.php',
					'template':'cities',
					'params':{
						'action':'get_template'
					}
				});

				$.get('/api/estelife_ajax.php',{
					'action':'get_cities'
				},function(r){
					if(r.active && r.passive){
						detail_generator.ready(function(){
							var html = detail_generator.make(r);
							if (html.length>0){
								if (index == 0){
									prnt.html(html).addClass('cities_open');
								}else{

								}
							}
						});
					}else{
						alert('Ошибка получения городов')
					}
				},'json');
			});
		}
	});

	//Смена города
	$('body').on('click', '.main_cities .col1 a', function(){
		var th = $(this);


		$.get('/api/estelife_ajax.php',{
			'action':'set_city',
			'city': th.attr('class')
		},function(r){
			if(r.city){
				$('.main_cities .col1 li').removeClass('active');
				th.parent().addClass('active');
				$('.panel .change_city span').html(r.city.NAME);
				$('.panel .change_city span').attr('class', 'city_'+r.city.ID);
				$('.main_cities').addClass('none').removeClass('cities_open');
			}else{
				alert('Ошибка получения городов')
			}
		},'json');
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

	//открытие ссылки
	$('.item, .col2').click(function(){
		document.location.href = $(this).find('a').attr('href');
	});

	//переключения в галереи
	$('body').on('click', '.get_only_photos, .get_only_videos, .get_photos_and_videos', function(){
		var rel = $(this).attr('rel'),
			video,
			photo;

		EL.loadModule('templates',function(){
			var detail_generator=new EL.templates({
				'path':'/api/estelife_ajax.php',
				'template':'photogallery',
				'params':{
					'action':'get_template'
				}
			});

			if (rel=="ONLY_PHOTO"){
				photo = "Y";
				video = "N"
			}else if (rel=="ONLY_VIDEO"){
				photo = "N";
				video = "Y"
			}else{
				photo = "Y";
				video = "Y"
			}

			$.get('/api/estelife_ajax.php',{
				'action':'get_media',
				'params':{
					'photo': photo,
					'video': video
				}
			},function(r){
				if(r.result){
					detail_generator.ready(function(){
						var html = detail_generator.make(r);
						if (html.length>0){
							$('.media').html(html);
							if (rel=="ONLY_PHOTO"){
								$('.get_only_photos').addClass('active');
							}else if (rel=="ONLY_VIDEO"){
								$('.get_only_videos').addClass('active');
							}
						}
					});
				}else{
					alert('Ошибка получения фотогалереи');
				}
			},'json');
		});

		return false;
	});


});

$(function(){
	var detail=$('.el-ditem'),
		detail_generator,list_generator,
		bd=$('body');

	EL.loadModule(['ajaxSupport','url'],function(){
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

//	EL.loadModule(['ajaxSupport','url'],function(){
//		var url=new EL.url();
//
//		//подсветка урлов второго уровня
//		var city_url = url.getQuery(document.location.pathname).city,
//			reg=new RegExp('^.*(city=\\d*).*$');
//
//		$(".submenu a").each(function(){
//			var href = $(this).attr("href"),
//				matches=href.match(reg);
//			if(matches){
//				if (matches[1] == 'city='+city_url){
//					$(this).closest(".submenu>li").addClass("second_active");
//					return;
//				}
//			}else{
//				if ((href.indexOf(document.location.pathname) == 0) && !$(".submenu>li.active").length && document.location.pathname !='/' && document.location.pathname==href){
//					$(this).closest(".submenu>li").addClass("second_active");
//					return;
//				}
//			}
//		});
//
//		var as=new EL.ajaxSupport(url),ff;
//
//		if(!as.check())
//			return;
//
//		EL.loadModule(['filter','templates'],function(){
//			url.push();
//			ff=new EL.filter({
//				'send_path':'/api/estelife_ajax.php'
//			});
//
//			// Отрабатываем открытие детальной страницы
//			(function(){
//				if(!as.detailAction())
//					return;
//
//				detail_generator=new EL.templates({
//					'path':'/api/estelife_ajax.php',
//					'template':as.detailAction(),
//					'params':{
//						'action':'get_template'
//					}
//				});
//
//				bd.on('click','.el-get-detail',function(){
//					showDetail($(this).attr('href'));
//					return false;
//				});
//			})();
//
//			// Вешаем Ajax на пункты меню для страниц, которые его поддерживают
//			(function(){
//				bd.on('click','.menu a',function(){
//					var lnk=$(this),
//						href=lnk.attr('href');
//
//					if(!href || href=='' || href=='#')
//						return false;
//
//					var u=new EL.url(href),
//						a=new EL.ajaxSupport(u),
//						list=a.listAction();
//
//					if(!list)
//						return true;
//
//					EL.bigLoader().create();
//
//					url.set(u);
//					as=a;
//					list_generator=new EL.templates({
//						'path':'/api/estelife_ajax.php',
//						'template':list,
//						'params':{
//							'action':'get_template'
//						}
//					});
//
//					if(as.detailAction())
//						detail_generator=new EL.templates({
//							'path':'/api/estelife_ajax.php',
//							'template':as.detailAction(),
//							'params':{
//								'action':'get_template'
//							}
//						});
//
//					ff.clear();
//
//					var key,
//						query=url.getQuery();
//
//					for(key in query)
//						ff.set(key,query[key]);
//
//					ff.send({
//						'action':list
//					});
//
//					getFilterForm(as.name(),query);
//					activateMenuLink(lnk);
//					return false;
//				});
//			})();
//
//			// Отрабатываем фильтр
//			(function(){
//				if(!as.listAction())
//					return;
//
//				list_generator=new EL.templates({
//					'path':'/api/estelife_ajax.php',
//					'template':as.listAction(),
//					'params':{
//						'action':'get_template'
//					}
//				});
//
//				ff.on('before_send',function(){
//					return true;
//				});
//
//				ff.on('after_send',function(data){
//					EL.goto($('.content'));
//
//					var nf=$('.el-not-found'),
//						prnt=$('.el-items'),temp,
//						bl=prnt.parents('.block:first'),
//						color=('block_color' in data) ?
//							data.block_color :
//							'red';
//
//					if(bl.is(':hidden')){
//						$('.el-ajax-detail').remove();
//						bl.show();
//					}
//
//					bl.find('.block-header')
//						.removeClass()
//						.addClass('block-header '+color);
//					prnt.find('.el-item').remove();
//
//					if('list' in data && data.list.length>0){
//						list_generator.ready(function(){
//							nf.hide();
//
//							for(var i=0; i<data.list.length; i++){
//								temp=list_generator.make(data.list[i]);
//								prnt.append(temp);
//							}
//						});
//					}else{
//						nf.show();
//					}
//
//					if('nav' in data){
//						$('.pagination').empty()
//							.prepend(data.nav);
//					}
//
//					if('filter' in data){
//						if('action' in data.filter)
//							delete data.filter.action;
//
//						if(!url.check(new RegExp('^'+url.current().addslashes()+'$')))
//							url.set(url.current());
//
//						url.setQuery(data.filter);
//						url.push();
//					}
//
//					EL.bigLoader().destroy();
//					EL.smallLoader().destroy();
//					EL.formBlock().destroy();
//				});
//
//				ff.on('send_error',function(error){
//					if('console' in window)
//						console.log(error.text);
//				});
//
//				bd.on('change','select,input',function(){
//					var sel=$(this),
//						frm=$(sel[0].form);
//
//					setTimeout(function(){
//						EL.smallLoader().create(sel);
//						EL.formBlock().create(sel);
//
//						frm.find('input,select').each(function(){
//							var inpt=$(this),
//								name=inpt.attr('name'),
//								val=inpt.val();
//
//							if(val==0 || val=='')
//								ff.del(name);
//							else
//								ff.set(name,val);
//						});
//
//						ff.send({
//							'action':as.listAction()
//						});
//					},100);
//				});
//
//				bd.on('click','.el-cl-filter',function(){
//					var frm=$(this).parents('.el-filter:first').find('form');
//
//					if(frm.length<=0)
//						return false;
//
//					ff.block(true);
//					frm.find('select,input').each(function(){
//						var inpt=$(this),
//							tag=inpt[0].tagName.toLowerCase(),
//							prnt=inpt.parent();
//
//						if(tag=='select'){
//							if(inpt.data('vapi_select')){
//								inpt.data('vapi_select').selectedIndex(0);
//							}else{
//								inpt[0].selectedIndex=0;
//							}
//						}else if(prnt.hasClass('date from') && 'datepicker' in $){
//							var date=new Date();
//							inpt.datepicker('setDate',date);
//							prnt.next().find('input').datepicker(
//								"option",
//								'minDate',
//								date
//							)
//						}else if(prnt.hasClass('date to')){
//							prnt.prev().find('input').datepicker(
//								"option",
//								'maxDate',
//								null
//							);
//							inpt.val('');
//						}else
//							inpt.val('');
//					});
//
//					url.set('/'+url.get(0)+'/');
//
//					ff.block(false);
//					ff.clear();
//					ff.send({
//						'action':as.listAction()
//					});
//
//					return false;
//				});
//			})();
//
//			if(as.listAction()){
//				bd.on('click','.pagination a',function(){
//					var matches,
//						form;
//
//					if((matches=$(this).attr('href').match(/(PAGEN_[0-9]+)=([0-9]+)/))==false)
//						return false;
//
//					if((form=$('.el-filter form')).length>0)
//						EL.formBlock().create(form);
//
//					ff.set(matches[1],matches[2]);
//					ff.send({
//						'action':as.listAction()
//					});
//
//					return false;
//				});
//			}
//
//			var popped=('state' in window.history),
//				initialURL=location.href;
//
//			window.onpopstate=function(e){
//				var initialPop=(!popped && location.href==initialURL);
//				popped=true;
//
//				if(initialPop )
//					return;
//
//				if(e.state!=null){
//					if(!('path' in e.state || !e.state.path))
//						location.reload();
//
//					if(url.check(new RegExp('^'+e.state.path.addslashes()+'$'))){
//						history.back();
//						return;
//					}
//
//					url.set(e.state.path);
//					url.blockPush(true);
//					as=new EL.ajaxSupport(url);
//
//					var sl,action,
//						type=as.type();
//
//					activateMenuLink($('.menu a[href=\''+url.current()+'\']'));
//					getFilterForm(as.name(),url.getQuery());
//
//					if(type=='list' && (action=as.listAction())){
//						list_generator=new EL.templates({
//							'path':'/api/estelife_ajax.php',
//							'template':action,
//							'params':{
//								'action':'get_template'
//							}
//						});
//
//						ff.block(true);
//						ff.clear();
//						var filter=url.getQuery();
//
//						for(prop in filter){
//							ff.set(prop,filter[prop]);
//							sl=$('select[name='+prop+']');
//
//							if(sl.length>0 && sl.data('vapi_select'))
//								sl.data('vapi_select').set(
//									filter[prop]
//								);
//						}
//
//						ff.block(false);
//						ff.send({
//							'action':action
//						});
//					}else if(type=='detail' && as.detailAction){
//						showDetail(e.state.path);
//					}else{
//						location.reload();
//					}
//				}
//			};
//		});
//
//		showDetail=function(href){
//			var matches=href.match(/\/?([\w\d\-_\.]+)\/?$/);
//
//			if(!matches){
//				alert('Системная ошибка');
//				return;
//			}
//
//			if(!detail_generator || !as.detailAction())
//				return;
//
//			EL.bigLoader().create();
//
//			$('.el-ajax-detail').remove();
//
//			$.ajax({
//				url:'/api/estelife_ajax.php',
//				cache:false,
//				data:{
//					'id':matches[1],
//					'action':as.detailAction()
//				},
//				type:'POST',
//				async:true,
//				success:function(r){
//					if('item' in r){
//						detail_generator.ready(function(){
//							var bl=$('.block:first'),
//								result=detail_generator.make(r.item);
//
//							EL.goto(bl);
//
//							url.set(href);
//							url.push();
//
//							bl.hide().after(result);
//							bl=bl.next();
//
//							if('jScrollPane' in $.fn){
//								bl.find('[data-scroll=true]').each(function(){
//									$(this).jScrollPane({
//										hideFocus:true,
//										verticalDragMaxHeight:100,
//										verticalDragMinHeight:50,
//										autoReinitialise:false,
//										autoReinitialiseDelay:200,
//										verticalGutter:0,
//										mouseWheelSpeed:30
//									});
//								});
//							}
//
//							var gallery=bl.find('.el-gallery'),
//								slider=bl.find('.big-photo'),
//								contacts = bl.find('.next_prev_contact');
//
//							if(gallery.length>0){
//								EL.loadModule(['bayan','colorbox'],function(){
//									EL.bayan(gallery.find('.image'));
//									EL.colorbox();
//								});
//							}
//
//							if(slider.length>0){
//								EL.loadModule('slider',function(){
//									EL.slider(slider.find('img'));
//								});
//							}
//
//							if(contacts.length>0){
//								EL.loadModule('miniSlider',function(){
//									EL.miniSlider($('.el-scroll .slider_content .el-scroll-in'), '.el-scroll .left', '.el-scroll .right');
//								});
//							}
//						});
//					}else if('error' in r){
//						alert('Системная ошибка');
//
//						if('console' in window)
//							console.error(r.error);
//					}
//
//					EL.bigLoader().destroy();
//				},
//				dataType:'json'
//			});
//		}
//	});
//	initFilter($('.el-filter'));
});


$(function(){
	initFilter($('.filter'));
});

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
							prnt=child.parent().parent();

						prnt.addClass('disabled');
						child.find('option:not(:first)').remove();

						if('list' in r && r.list.length>0){
							for(var i= 0; i< r.list.length; i++){
								child.append('<option value="'+ r.list[i].value+'">'+ r.list[i].label+'</option>');
							}
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
});