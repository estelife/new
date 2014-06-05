var Router;
require([
	'mvc/Routers',
	'tpl/Template',
	'modules/Geo',
	'modules/Media',
	'modules/Functions'
],function(Routers,Template,Geo,Media,Functions){
	Router=new Routers.Default();

	$(function home(){
		var body=$('body');

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

	$(function(){
		var body=$('body');

		EL.notice();

		if(Backbone.history && !Backbone.History.started) {
			var historySettings;

			if(!(window.history && history.pushState)) {
				historySettings={
					'pushState':false,
					'hashChange': false,
					'silent': true
				};
			}else {
				historySettings={
					'pushState':true,
					'hashChange': true,
					'silent': true
				};
			}
			Backbone.history.start(historySettings);
		}

		body.on('click', '.reg_photo img', function(){
			EL.notice().show($(this));

			return false;
		});


		//подстановка в url авторизации backurl
		body.on('click touchstart touchend', '.goto-auth', function(e){
			if(e.type)
			var link=location.href.replace(location.protocol+'//'+location.host,'');
			var href='/personal/auth/?backurl='+encodeURIComponent(link);

			if ($(this).hasClass('logout'))
				href=hrerf+'&logout=yes';

			location.href=href;
			e.preventDefault()
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
					if (form.hasClass('main')){
						var succesText=form.attr('data-success')||'Вы успешно подписались на новые статьи!',
							succesTag=form.attr('data-success-tag')||'h3';

						form.html('<'+succesTag+' class="req-success">'+succesText+'</'+succesTag+'>');
					}else{
						alert('Вы успешно подписаны');
						$('input[name=email]').val('');
					}
				}else{
					alert(r.error);
				}
			},'json');

			e.preventDefault();
		});

		//Переход на детальную страницу
		body.on(
			EL.touchEvent.eventTrigger,
			'.items .item:not(.article,.activity), .items .article .item-in, .item.activity .user, .general-news .col1, .general-news .col2 .img',
			EL.touchEvent.callback(function(event,target){
				var currentTag=event.target.tagName,
					eventTarget = $(event.target),
					parentTag=eventTarget.parent()[0].tagName,
					link=(currentTag=='A') ?
						eventTarget.attr('href') :
						target.find('a:first').attr('href');

				if(link=='#'){
					event.preventDefault();
					return;
				}

				if((currentTag!='A' && link && link.length>0) || ['H1','H2','H3'].inArray(parentTag)>-1){
					Router.navigate(link,{trigger: true});
					lightMenu();
					event.preventDefault();
				}
			})
		);

		var intId;
		//переключение между пунктами меню в эксперном мнении
		body.on(
			EL.touchEvent.eventTrigger,
			'.experts .menu li',
			EL.touchEvent.callback(function(event, target){
				clearInterval(intId);
				showNextExpert(target);
				expertClick();
				event.preventDefault();
			})
		);

		body.on(
			EL.touchEvent.eventTrigger,
			'.ax-support',
			EL.touchEvent.callback(function(event, target){
				var link = target.attr('href');
				Router.navigate(link,{trigger: true});
				event.preventDefault();
			})
		);

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

		//Лайки
		body.on(
			'click',
			'.stat .likes', function(){
				var prnt = $(this).parent().parent(),
					act = $(this).hasClass('active'),
					md5 = EL.storage().getItem('like_'+prnt.attr('data-elid')+ prnt.attr('data-type'));

				if ($(this).parent().hasClass('notlike'))
					return false;

				$.post('/api/estelife_ajax.php',{
					'action':'set_likes',
					'elementId': prnt.attr('data-elid'),
					'type': prnt.attr('data-type'),
					'typeLike': 1,
					'md5': md5
				},function(r){
					if (r){
						if (act == false){
							$('.likes.islike').addClass('active').html(r.countLike+' и Ваш<i></i>');
						}else{
							$('.likes.islike').removeClass('active').html(r.countLike+'<i></i>');
						}
						$('.unlikes.islike').removeClass('active').html(r.countDislike+'<i></i>');
						EL.storage().setItem('like_'+r.element_id+ r.type, r.md5);
					}else{
						alert('Ошибка постановки лайков');
					}
				},'json');

				return false;
			}
		);

		body.on('click', '.stat .unlikes', function(){
			var prnt = $(this).parent().parent(),
				act = $(this).hasClass('active'),
				md5 = EL.storage().getItem('like_'+prnt.attr('data-elid')+ prnt.attr('data-type'));

			if ($(this).parent().hasClass('notlike'))
				return false;

			$.post('/api/estelife_ajax.php',{
				'action':'set_likes',
				'elementId': prnt.attr('data-elid'),
				'type': prnt.attr('data-type'),
				'typeLike': 2,
				'md5': md5
			},function(r){
				if (r){
					if (act == false){
						$('.unlikes.islike').addClass('active').html(r.countDislike+' и Ваш<i></i>');
					}else{
						$('.unlikes.islike').removeClass('active').html(r.countDislike+'<i></i>');
					}
					$('.likes.islike').removeClass('active').html(r.countLike+'<i></i>');
					EL.storage().setItem('like_'+r.element_id+ r.type, r.md5);
				}else{
					alert('Ошибка постановки лайков');
				}
			},'json');

			return false;
		});


		//Переключение между вкладками
		body.on('click','.articles .tabs-menu li', function(){
			var prnt = $(this).parents('.articles:first'),
				col = $('li',prnt),
				index = col.index($(this));

			$('li',prnt).removeClass('active').eq(index).addClass('active');
			$('.items' ,prnt).addClass('none').eq(index).removeClass('none');

			var section_url = $('.items' ,prnt).eq(index).attr('rel');
			prnt.find('.title a').attr('href', section_url);

			return false;
		});

		//табы для раскрытия информации
		var flag = 1;
		body.on('click', '.el-tab h3', function(){
			var prnt = $(this).parent(),
				el = $('a',$(this));

			if (flag == 1){
				flag = 0;
				if (el.hasClass('active')){
					el.removeClass('active');
					$('.text', prnt).animate({height: 0}, 500, function(){
						flag = 1;
					});
				}else{
					el.addClass('active');
					var th = $('.text', prnt).children(),
						height = 0;

					th.each(function(){
						height += $(this).outerHeight();
					});

					$('.text', prnt).animate({height: height +"px"}, 500, function(){
						flag = 1;
					});
				}
			}

			return false
		});

		//Переключение между табами
		body.on('click','.menu_tab ul li',function(e){
			var href = $(this).find('a').attr('href');

			if (!href || href == '#') {
				var col = $('.menu_tab ul li'),
					index = col.index($(this));

				col.removeClass('active');
				$(this).addClass('active');

				$('.tabs').addClass('none').eq(index).removeClass('none');
			} else {
				Router.navigate(href, {trigger: true});
			}

			e.preventDefault();
		});

		//переключения между табами в галереи
		body.on('click touchstart', '.media .menu a', function(){
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

			if(link.hasClass('no-ajax'))
				return;

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

		body.on('click touchstart','.nav a, .crumb a:not(.no-ajax), .search_page a', function(e){
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
				lightMenu();

				e.preventDefault();
			}
		}).on('submit','form.filter',function(e){
			var frm=$(this),
				page=frm.attr('action');

			if(!page || page.length<=0)
				throw 'invalid form action';

			var data={},
				keys={};

			frm.find('input,select').each(function(){
				var inpt=$(this),
					type=inpt.attr('type')||'select',
					name=inpt.attr('name'),
					val='';

				if(type=='text' || type=='select'){
					val=inpt.val();
				}else if(type=='checkbox' && inpt.prop('checked')){
					val=inpt.val();
				}

				if(val!='' && val!=0 && val!='0'){
					var matches;

					if(matches=name.match(/([a-z_\-0-9]+)\[(.*)\]/)){
						if(!keys.hasOwnProperty(matches[1]))
							keys[matches[1]]=[];

						var key=(matches[2]!='') ?
							matches[2] :
							Object.keys(keys[matches[1]]).length;

						keys[matches[1]].push(key);
						data[matches[1]+'['+key+']']=val;
					}else
						data[name]=val;
				}
			});

			Router.navigate(
				page+EL.query().toString(data),
				{trigger: true}
			);
			lightMenu();
			e.preventDefault();
		}).on('click touchstart','form.filter a.clear',function(e){
//			var href=$(this).attr('href');
//			Router.navigate(
//				href,
//				{trigger: true}
//			);
//			e.preventDefault();
		}).on('click touchstart','.logo',function(e){
			if (!$(this).hasClass('no-ajax')){
				Router.navigate(
					$(this).attr('href'),
					{trigger: true}
				);
				$('.main_menu').find('.main,.active,.second_active')
					.removeClass('main active second_active');
				e.preventDefault();
			}
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
		}).on('submit', 'form[name=add_review]', function(e){
			var form = new EL.Form($(this));

			form.registerAfterSend(function(data){
				if (!_.isObject(data) || !data.hasOwnProperty('reviews'))
					throw 'Какой-то fail';

				data = data.reviews;

				if (data.hasOwnProperty('error')) {
					form.getTarget().prepend('<div class="total_error">'+data.error.message+'</div>');
				} else if (data.hasOwnProperty('errors')) {
					_.map(data.errors, function(value, key){
						form.getTarget().find('[data-handler='+key+']').addClass('error');
					});
				} else if(data.hasOwnProperty('complete')) {
					Router.reviewList(data.complete.clinic_id);
					EL.notice().show(data.complete.text);
				} else {
					throw 'Какой-то fail';
				}
			});

			form.sendData({
				action: '/rest/review_form/'
			});

			e.preventDefault();
		}).on('change','form[name=review_filter] select', function() {
			var form = $(this.form),
				clinicId = form.find('input[name=clinic_id]').val(),
				problemId = form.find('select[name=problem_id]').val(),
				specialistId = form.find('select[name=specialist_id]').val();

			Router.reviewList(clinicId, problemId, specialistId);
		}).on('click', 'form[name=review_filter] .all', function(e){
				var form = $(this).parents('form:first'),
					clinicId = form.find('input[name=clinic_id]').val();

				Router.reviewList(clinicId);
				e.preventDefault();
		});
	});

	//меню

	function lightMenu(){
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
	}

	//Работа с Гео
	$(function(){
		var body=$('body');

		lightMenu();

		Geo.addEventListener({
			onCityChange:function(city){
				$('.cities li').removeClass('active');
				$('.cities a.'+city.ID).parent().addClass('active');

				$('.change_city span').html(city.NAME).attr('class', 'city_'+city.ID);
				$('.cities').addClass('none').removeClass('cities_open');

				//Functions.getPromotions(city.ID);
			}
		});

		//Вывод списка городов в шапке
		body.on('click touchstart','.change_main_city', function(){
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
		body.on('click touchstart','.change_promotions_city', function(){
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

	$(function other(){
		var body=$('body');

		body.on('click touchstart','.media .items .item',function(e){
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
				map.zoom(14);

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
		$('.map').trigger('showMap');

		body.on('updateFilter', 'form.filter', function(){
			var form=$(this);
			Functions.initFilter(form);
		});
		$('form.filter').trigger('updateFilter');

		body.on('updateForm','form:not(\'.filter\')',function(){
			Functions.initFormFields($(this));
		});
		$('form:not(\'.filter\')').trigger('updateForm');

		body.on('updateGallery', '.gallery', function(){
			var gallery=$(this);
			require(['modules/Slider'], function(Slider){
				Slider($('.gallery-in .item',gallery));
			});
		});
		$('.gallery').trigger('updateGallery');

		body.on('updateContent', function(){
			var activity = $('.activity');

			if(activity.length>0){
				var end,height,maxHeight = 0,
					items = activity.find('.item-in'),
					lastItemIndex = 0;

				items.each(function(i){
					height = $(this).height();

					if(maxHeight<=height)
						maxHeight = height;

					if((i>0 && i%4==0) || i+1==items.length){
						end = (i%4 == 0) ? i : items.length;

						for(var y = lastItemIndex; y<end; y++)
							items.eq(y).height(maxHeight);

						lastItemIndex = y;
						maxHeight=0;
					}
				});
			}
		}).trigger('updateContent');

		body.on('submit','form[name=add_request]',function(e){
			var form=$(this),
				data={
					'action':'add_request'
				};
			form.find('.error')
				.removeClass('error');
			form.find('.half_error')
				.removeClass('half_error');
			form.find('i')
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
						form.replaceWith('<p class="quality-result">Спасибо. Заявка принята, в ближайшее время с Вами свяжется наш специалист.</p>');
					}
				}
			},'json');

			e.preventDefault();
		});

		body.on('focus','.error input, .error textarea', function(){
			var prnt = $(this).parents('.error:first');

			if (prnt.hasClass('error')) {
				prnt.removeClass('error');
				prnt.find('input,textarea').next('i').remove();
			}

			var total_form = $(this.form),
				total_error = total_form.find('.total_error');

			if (total_error.hasClass('error'))
				total_error.removeClass('error');

			$('.success').remove();
		}).on('click', '.field.error', function(){
			var prnt = $(this);

			if (prnt.find('input,textarea').length)
				return;

			var total_error = $(this).parents('form:first').find('.total_error');

			if (total_error.hasClass('error'))
				total_error.removeClass('error');

			prnt.removeClass('error');
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

		body.on('click touchstart','.repost a', function(e){
			var href=$(this).attr('href'),
				width=550,
				height=400,
				left=screen.availWidth/2-width/2,
				top=screen.availHeight/2-height/2;
			window.open(href,'repost',"menubar=no,location=no,status=no,width="+width+",height="+height+",left="+left+",top="+top);
			e.preventDefault();
		});

		body.on('click touchstart','.show-quality-form',function(e){
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

		EL.helpMaker($('[data-help],[title]'));
		body.on('showHelp', '', function(){
			EL.helpMaker($('[data-help],[title]'));
		});

		//Пишем в базу историю поиска
		body.on('click touchstart', '.set_search_history', function(){
			var term=$('input[data-action=get_search_history]').val();

			if (term.length>0){
				$.post('/api/estelife_ajax.php',{
					'action':'set_search_history',
					'term':term
				},function(r){
					if (!r.save)
						alert('Ошибка сохранения запроса')
				},'json');
			}
		});

		//Автокомплит поиска
		body.on('focus','input[data-action=get_search_history]', function(){
			$(this).autocomplete({
				minLength:3,
				width:260,
				source:function(request, response){
					var data={
						'action':'get_search_history',
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
					$('input[data-action=get_search_history]').val(ui.item.name);
				}
			})
		});

		//отправка комментария
		body.on('submit','form[name=comments]', function(){
			var form=$(this);
			require(['mvc/Models','mvc/Views'],function(Models,Views){
				var data={};

				form.find('input,textarea').each(function(){
					var input=$(this);
					data[input.attr('name')]=input.val();
				});

				$.post('/rest/comments/',data,function(r){
					new Models.Static(r,{
						view:new Views.Comments({
							template:'comments_list'
						})
					});
				},'json');
			});

			return false;
		});

		//Показать все комментарии
		body.on('click touchstart', '.comments .more a span', function(){
			var el=$(this);
			require(['mvc/Models', 'mvc/Views'], function(Models,Views){
				var data={},
					form=el.parents('div.comments');

				data['id']=form.attr('data-id');
				data['type']=form.attr('data-type');
				if (el.hasClass('hide')){
					data['count']=5;
				}else{
					data['count']=0;
				}

				$.post('/rest/comments/',data,function(r){
					new Models.Static(r,{
						view:new Views.Comments({
							template:'comments_list'
						})
					});
				},'json');
			});

			return false;
		});

		//Количество символов в текстареа
		body.on('keyup', 'form textarea', function(){
			var prnt=$(this).parent().parent(),
				item=prnt.find('label span s'),
				maxchars=1000,
				number=$(this).val().length;
			if(number<=maxchars){
				var count=maxchars-number;
				item.html(count+' символ'+EL.spellAmount(count, ',а,ов'));
			}
			if(number==maxchars) {
				$(this).attr({maxlength: maxchars});
			}
		});

		body.on('click', '.add_review', function(e){
			var matches;

			if (matches = location.pathname.match(/cl([0-9]+)/))
				Router.reviewForm(matches[1]);

			e.preventDefault();
		}).on('click', '.show_terms', function(e){
				$.get('/about/review_terms.php', {
					action: 'show_terms'
				}, function(terms) {
					EL.notice().show(terms);
					var ch = $('form[name=add_review] input[name=read_term]');
					ch.prop('checked', true);
					ch.next('a[data-name=read_term]').addClass('active');
				});
				e.preventDefault();
		});

		(function(){
			var text = ['ужасно', 'плохо', 'удовл.', 'хор.', 'оч.хор.'];

			body.on('mouseout', '.rating a', function(){
				return false;
			}).on('mouseover','.rating a',function(e){
				var link=$(this),
					prnt=link.parent(),
					links=prnt.find('a'),
					active = links.filter('.active');

				if (!prnt.attr('data-index')) {
					prnt.attr('data-index', active.length-1);
				}

				active.removeClass('active');

				for(var i=0; i<links.length; i++){
					links.eq(i).addClass('active');

					if(link.get(0) == links.get(i))
						break;
				}

				prnt.find('span').html('<b>'+(i+1)+'</b> ('+text[i]+')');
			}).on('mouseout','.rating',function(){
				var prnt=$(this);
				var index=prnt.attr('data-index')||0;

				if (!prnt.find('a').length)
					return;

				if (index < 0) {
					prnt.find('a').removeClass('active');
					prnt.find('span').html('<b>0</b> (никак)');
				} else {
					index = parseInt(index);
					prnt.find('a').eq(index).mouseover();
					prnt.find('span').html('<b>'+(index+1)+'</b> ('+text[index]+')');
				}
			}).on('click','.rating a',function(){
				var prnt=$(this).parent();

				var active=prnt.find('.active').length;
				prnt.attr('data-index', active-1);

				var id = prnt.attr('id');

				if	(id)
					$('input[name='+id+']').val(active);

				return false;
			});

			if (location.href.match(/(review_list|review_form)/)) {
				$('.tabs-menu .t7').click();
			}
		})();
	});

	$(function interfaces(){
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

});
