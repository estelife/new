require([
	'mvc/Routers',
	'tpl/Template',
	'modules/Geo',
	'modules/Media',
	'modules/Functions'
],function(Routers,Template,Geo,Media,Functions){
	var body=$('body');
		Router=new Routers.Default();

	$(function home(){
		var timerID = 0,
			timer2ID = 0;

		//Наведение на меню
		$(".main_menu>li").hover(
			function() {
				var ob = this;
				clearTimeout(timerID);
				clearTimeout(timer2ID);
				timer2ID = setTimeout(function(){
						$(".main_menu>li").removeClass("active");
						$(ob).addClass("active");
					},
					100
				);
			},
			function() {
				timerID = setTimeout(function(){
						$(".main_menu>li").removeClass("active");
						$(".main_menu>li.main").addClass('active');
					},
					550
				);
			}
		);

		//Наведение на подменю
		$(".submenu").hover(
			function() {
				var ob = this;
				$(ob).parent().addClass("hover");
			},
			function() {
				$(".main_menu>li").removeClass("hover");
			}
		);

	});

	// BULLSHIT
	$(function(){
		Backbone.history.start({
			'pushState':true,
			'hashChange': false
		});

		//подписка
		body.on('submit', 'form.subscribe', function(e){
			var form=$('form.subscribe'),
				data={'action':'set_subscribe'};
			form.find('input').each(function(){
				data[$(this).attr('name')]=$(this).val();
			});
			if ($('input[type=checkbox]', form).prop('checked')){
				data['always'] = 1;
			}else{
				data['always'] = 0;
			}
			$.post('/api/estelife_ajax.php',
				data,
				function(r){
				if (r.complete == 1){
					alert('Вы успешно подписаны');
					$('input[name=email]').val('');
				}else{
					alert(r.error);
				}
			},'json');

			e.preventDefault();
		});

		//Переход на детальную страницу
		body.on('click', '.items .item, .general-news .col1, .general-news .col2 .img', function(e){
			var target=$(e.target),
				currentTag=target[0].tagName,
				parentTag=target.parent()[0].tagName,
				link=(currentTag=='A') ?
					target.attr('href') :
					$(this).find('a:first').attr('href');

			if((currentTag!='A' && link && link.length>0) || ['H1','H2','H3'].inArray(parentTag)>-1){
				Router.navigate(link,{trigger: true});
				EL.goto($('.main_menu'));
				e.preventDefault();
			}
		});


		var intId;
		//переключение между пунктами меню в эксперном мнении
		body.on('click','.experts .menu li',function(){
			clearInterval(intId);
			showNextExpert($(this));
			expertClick();
			return false;
		});

		function showNextExpert(li){
			var prnt = $('.experts'),
				col = $('.menu li',prnt),
				act = $('.menu li.active',prnt);


			if(li){
				index = col.index(li);
			}else{
				index =col.index(act);
				index++;
			}

			if(index >= col.length)
				index = 0;

			col.removeClass('active').eq(index).addClass('active');
			$('.item',prnt).addClass('none').eq(index).removeClass('none');
		}

		function expertClick(){
			intId=setInterval(
				function(){
					showNextExpert();
				},
				4000);
		}
		expertClick();


		//Переключение между вкладками
		body.on('click','.articles .menu li', function(){
			var prnt = $(this).parents('.articles:first'),
				col = $('.menu li',prnt),
				index = col.index($(this));

			$('.menu li',prnt).removeClass('active').eq(index).addClass('active');
			$('.items' ,prnt).addClass('none').eq(index).removeClass('none');

			var section_url = $('.items' ,prnt).eq(index).attr('rel');
			prnt.find('.title a').attr('href', section_url);

			return false;
		});

		//табы для раскрытия информации
		body.on('click', '.el-tab h3', function(){
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

		//Переключение между табами
		body.on('click','.menu_tab ul li',function(){
			var col = $('.menu_tab ul li'),
				index = col.index($(this));

			col.removeClass('active');
			$(this).addClass('active');

			$('.tabs').addClass('none').eq(index).removeClass('none');

			return false;
		});

		//переключения между табами в галереи
		body.on('click', '.media .menu a', function(){
			var lnk=$(this),
				rel=lnk.attr('rel');

			var tpl=new Template({
				'path':'/api/estelife_ajax.php',
				'template':'photogallery',
				'params':{
					'action':'get_template'
				}
			});

			lnk.parents('.menu').find('.active').removeClass('active');
			if (lnk.attr('rel') != 'ALL'){
				lnk.addClass('active');
				lnk.parent().parent().find('.first').find('a').removeClass('none');

			}else{
				lnk.parent().parent().find('.first').find('a').addClass('none');
			}

			$.get('/api/estelife_ajax.php',{
				'action':'get_media',
				'params':{
					'photo': (rel=="ONLY_PHOTO" || rel=='ALL') ? 'Y' : 'N',
					'video': (rel=="ONLY_VIDEO" || rel=='ALL') ? 'Y' : 'N'
				}
			},function(r){
				if(r.result){
					tpl.ready(function(){
						var html=tpl.render(r);
						$('.media').find('.items')
							.html(html);
					});
				}else{
					alert('Ошибка получения фотогалереи');
				}
			},'json');

			return false;
		});

		$('.main_menu a').click(function(e){
			var link=$(this),
				href=link.attr('href')||'',
				parent=link.parents('ul:first'),
				menu=$('.main_menu');

			if(href.length>0 && href!='#'){
				Router.navigate(href,{trigger: true});
				menu.find('.main,.active,.second_active')
					.removeClass('main active second_active');

				if(parent.hasClass('main_menu')){
					link.parent().addClass('main active')
				}else{
					parent=link.parents('li');
					parent.eq(0).addClass('second_active');
					parent.eq(1).addClass('main active');
				}
				e.preventDefault();
			}
		});

		body.on('click','.nav a, .articles .title a, .crumb a, .search_page a', function(e){
			var lnk=$(this),
				href=lnk.attr('href'),
				crumb=lnk.parents('.crumb:first');

			if(href && href.length>0){
				if(crumb.length<=0)
					EL.goto($('.main_menu'));

				Router.navigate(
					href.replace(/^\/rest/,''),
					{trigger: true}
				);

				e.preventDefault();
			}
		}).on('submit','form.filter',function(e){
			var frm=$(this),
				page=frm.attr('action');

			if(!page || page.length<=0)
				throw 'invalid form action';

			var data={};

			frm.find('input,select').each(function(){
				var inpt=$(this),
					type=inpt.attr('type')||'select',
					name=inpt.attr('name'),
					val='';

				if(type=='text' || type=='select'){
					val=inpt.val();
				}else{
					val=frm.find('input[name='+name+']:checked')
						.attr('value')||0;
				}

				if(val!='' && val!=0 && val!='0')
					data[name]=val;
			});

			Router.navigate(
				page+EL.query().toString(data),
				{trigger: true}
			);
			e.preventDefault();
		}).on('click','form.filter a.clear',function(e){
//			var href=$(this).attr('href');
//			Router.navigate(
//				href,
//				{trigger: true}
//			);
//			e.preventDefault();
		}).on('click','.logo',function(e){
			Router.navigate(
				$(this).attr('href'),
				{trigger: true}
			);
			$('.main_menu').find('.main,.active,.second_active')
				.removeClass('main active second_active');
			e.preventDefault();
		}).on('submit','form[name=search]',function(e){
			var frm=$(this),
				href=frm.attr('action'),
				text=frm.find('input[name=q]').val();

			if(text.length>0){
				Router.navigate(
					href+'?q='+text,
					{trigger: true}
				);
				$('.main_menu').find('.main,.active,.second_active')
					.removeClass('main active second_active');
			}else{
				alert('Укажите поисковый запрос')
			}

			e.preventDefault();
		});

	});

	//меню
	EL.SystemSettings.ready(function(s){
		$(".main_menu a").each(function() {
			var href = $(this).attr("href"),
				path_name = document.location.pathname.split('/'),
				reg,matches;

			if(!_.isEmpty(path_name[1])){
				reg=new RegExp('^.*'+path_name[1]+'.*$');
				matches=href.match(reg);
			}

			if (matches && !$(".main_menu>li.active").length) {
				$(this).closest(".main_menu>li").addClass("active").addClass('main');
				return false;
			}else{
				var mass = s.directions;
				path_name = path_name[1];
				reg=new RegExp('^([a-z]{2})[0-9]+$');
				matches=path_name.match(reg);

				if (matches && mass[matches[1]].length>0){
					reg=new RegExp('^.*'+mass[matches[1]]+'.*$');
					var href_matches=href.match(reg);

					if (href_matches && !$(".main_menu>li.active").length){
						$(this).closest(".main_menu>li").addClass("active").addClass('main');
						return false;
					}
				}
			}
		});

		//подсветка урлов второго уровня
		$(".submenu a").each(function(){
			var href = $(this).attr("href"),
				reg=new RegExp('^('+href+')(\\?.*)?$'),
				path_name = document.location.href.split('/').slice(3).join('/');
			path_name = '/'+path_name;
			matches =path_name.match(reg);

			if (matches || path_name == '/apparatuses-makers/') {
				if (path_name == '/apparatuses-makers/'){
					$('.submenu li a[href="/preparations-makers/"]').parent().addClass("second_active").parent().parent().addClass("active").addClass('main');
				}else{
					$(this).closest(".submenu>li").addClass("second_active");
				}
				return false;
			}else{

				var mass = s.directions,
					reg=new RegExp('^([a-z]{2})[0-9]+$');
				path_name = document.location.pathname.split('/').slice(1, -1).pop();

				if (path_name){
					var matches=path_name.match(reg);

					if ((matches && mass[matches[1]].length>0) || (matches && matches[1]=='am')){

						if (matches[1]=='am'){
							$('.submenu li a[href="/preparations-makers/"]').parent().addClass("second_active").parent().parent().addClass("active").addClass('main');
						}else{
							reg=new RegExp('^\/'+mass[matches[1]]+'\/$');
							var href_matches=href.match(reg);

							if (href_matches){
								$(this).closest(".submenu>li").addClass("second_active");
								return false;
							}
						}
					}
				}
			}
		});
	});

	//Работа с Гео
	$(function(){
		Geo.addEventListener({
			onCityChange:function(city){
				$('.cities li').removeClass('active');
				$('.cities a.'+city.ID).parent().addClass('active');

				$('.change_city span').html(city.NAME).attr('class', 'city_'+city.ID);
				$('.cities').addClass('none').removeClass('cities_open');

				Functions.getPromotions(city.ID);
			}
		});

		//Вывод списка городов в шапке
		body.on('click','.change_main_city', function(){
			var lnk=$(this);

			if(lnk.hasClass('active'))
				lnk.removeClass('active');
			else
				lnk.addClass('active');

			Geo.load(
				Geo.Adapters.createAdapter('main')
			);
			return false;
		});

		//Вывод списка городов для акций
		body.on('click','.change_promotions_city', function(){
			var lnk=$(this);

			if(lnk.hasClass('active'))
				lnk.removeClass('active');
			else
				lnk.addClass('active');

			Geo.load(
				Geo.Adapters.createAdapter('promotion')
			);
			return false;
		});
	});

	body.on('click','.media .items .item',function(e){
		Media.VideoDirect.start();

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
			var m;
			if('images' in r){
				m=new Media.Gallery({
					'title': r.gallery.name,
					'description': r.gallery.description
				});
				$.map(r.images,function(item){
					m.setImage(new Media.GalleryImage(
						item.title,
						item.small,
						item.big
					));
				});
				m.showImages();
			}else if('video' in r){
				m=new Media.Gallery();
				m.setVideo(new Media.GalleryVideo(
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

	body.on('showMap', '', function(){
		$('.map',$(this)).each(function(){
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
			body.append(jmap);

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

	body.on('update', 'form.filter', function(){
		var form=$(this);
		Functions.initFilter(form);
	});

	body.on('submit','form[name=add_request]',function(e){
		var form=$(this),
			data={
				'action':'add_request'
			};
		form.find('error')
			.removeClass('error')
			.find('i')
			.remove();
		form.find('input').each(function(){
			var input=$(this);
			data[input.attr('name')]=input.val();
		});
		$.post('/api/estelife_ajax.php',data,function(r){
			if(r.hasOwnProperty('error')){
				if(r.error.hasOwnProperty('message')){
					alert(r.error.message);
				}else{
					var field;
					for(f in r.error){
						field=form.find('input[id='+f+']')
							.parent();
						field.addClass('error');
						field.append('<i>'+ r.error[f]+'</i>');
					}
				}
			}else if(r.hasOwnProperty('step')){
				if(r.step==3){
					form.replaceWith('<p>Спасибо. Заявка принята, в ближайшее время с Вами свяжется наш специалист.</p>');
				}
			}
		},'json');

		e.preventDefault();
	});

	body.on('focus','input.preload', function(){
		$(this).autocomplete({
			minLength:3,
			source:function(request, response){
				var action=$(this.element).attr('data-action');

				if(!action)
					return;

				var data={
					'action':action,
					'term':request.term
				};
				$.post('/api/estelife_ajax.php',data,function(result){
					if(result.hasOwnProperty('list')){
						response($.map(result.list,function( item ) {
							return {
								label: item.name,
								value: item.name,
								id: item.id
							}
						}));
					}
				},'json');
			},
			select:function(e,ui){
				var input=$(this);
				input.next('input[type=hidden]').val(ui.item.id);
			},
			open:function(e,ui){
				var input=$(this),
					width=input.data('auWidth');

				if(!width){
					width=input.width();
					pLeft=parseInt(input.css('padding-left').replace(/[^0-9]+/,''));
					pRight=parseInt(input.css('padding-right').replace(/[^0-9]+/,''));

					if(pLeft)
						width+=pLeft;

					if(pRight)
						width+=pRight;

					input.data('auWidth',width);
				}

				$('.ui-autocomplete').width(width);
			}
		})
	});

	body.on('click','.repost a', function(e){
		var href=$(this).attr('href'),
			width=550,
			height=400,
			left=screen.availWidth/2-width/2,
			top=screen.availHeight/2-height/2;
		window.open(href,'repost',"menubar=no,location=no,status=no,width="+width+",height="+height+",left="+left+",top="+top);
		e.preventDefault();
	});

	body.on('click','.show-quality-form',function(e){
		var link=$(this),
			form=link.next('form');

		if(link.hasClass('active')){
			link.removeClass('active');
			form.stop().animate({'height':'0px'},200);
		}else{
			link.addClass('active');
			var height=form.find('.quality-in').height();
			form.stop().animate(
				{'height':height+'px'},
				200,
				'swing',
				function(){
					EL.goto(form,true);
				}
			);
		}

		e.preventDefault();
	});

	var toTop=$('.to-top'),
		min=200,
		max=1000;

	toTop.click(function(){
		EL.goto();
		return false;
	});

	changeOpacityByScroll($(document).scrollTop());
	$(document).scroll(function(){
		changeOpacityByScroll($(this).scrollTop());
	});

	function changeOpacityByScroll(scroll){
		if(scroll>=min && scroll<=max){
			if(toTop.is(':hidden')){
				toTop.css({
					'display':'block',
					'opacity':0
				});
			}

			if(scroll<max)
				toTop.css('opacity',parseFloat(scroll/(max-min)).toFixed(1));
		}else if(scroll>max){
			toTop.css({
				'display':'block',
				'opacity':1
			});
		}else if(scroll<min && toTop.is(':visible'))
			toTop.css('display','none');
	}
});
