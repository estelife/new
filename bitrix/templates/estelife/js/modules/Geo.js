define(['tpl/Template','modules/Functions'],function(Templates,Functions){
	var Geo=(function(){
		var html,
			listeners=[];

		function fireEvent(e,data){
			if(listeners.length>0){
				var key,event='on'+e.substr(0,1).toUpperCase()+e.substr(1);

				for(key in listeners){
					if(listeners.hasOwnProperty(key) && event in listeners[key])
						listeners[key][event](data);
				}
			}
		}

		return {
			load:function(adapter){
				if (!html){
					//Загружаем список и преобразуем шаблон
//					EL.loadModule('templates',function(){
						var detail_generator=new Templates({
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
									var h = detail_generator.render(r);
									if (h.length>0){
										html = h;

										if(typeof adapter=='object' && 'transform' in adapter)
											adapter.transform(h);
									}else{
										console.log('Ошибка получения html')
									}
								});
							}else{
								console.log('Ошибка получения городов')
							}
						},'json');
//					});
				}else{
					var h=html;

					if(typeof adapter=='object' && 'transform' in adapter)
						adapter.transform(h);
				}
			},

			setCity:function(city){
				$.get('/api/estelife_ajax.php',{
					'action':'set_city',
					'city': city
				},function(r){
					if(r.city){
						fireEvent('cityChange',r.city);
					}else{
						alert('Ошибка получения городов')
					}
				},'json');
			},

			addEventListener:function(listener){
				if(listeners.inArray(listener)==-1)
					listeners.push(listener);
			},

			removeEventListener:function(listener){
				var key=listeners.inArray(listener);

				if(key>-1)
					listeners.remove(key);
			}
		}
	})();
	Geo.Adapters=(function(){
		var adapters={
			'main':function(){
				this.transform = function(html){
					var prnt = $('.main_cities');
					html=$(html);

					if (prnt.html().length<=0)
						prnt.append(html);

					if (prnt.hasClass('cities_open'))
						prnt.addClass('none').removeClass('cities_open');
					else
						prnt.removeClass('none').addClass('cities_open');

					html.find('a').click(function(){
						var target=$(this);
						Geo.setCity(target.attr('class'));
					});
				}
			},
			'promotion':function(){
				this.transform = function(html){
					var prnt = $('.promotions_city'),
						city = [],
						i;
					html=$(html);


					html.find('.col2 ul li').each(function(){
						city.push($(this).html());
					});

					var count = Math.round(city.length/2);


					if (count>0 && city.length>0){
						var h = '<h4>Скоро с нами:</h4><ul>';
						for (i=0;i< city.length;i++) {
							if (i == count){
								h+='</ul><ul>';
							}
							h+='<li>'+city[i]+'</li>';
						}
						h+='</ul>';
					}

					html.find('.col2').html(h);

					if (prnt.html().length<=0)
						prnt.append(html);

					if (prnt.hasClass('cities_open'))
						prnt.addClass('none').removeClass('cities_open');
					else
						prnt.removeClass('none').addClass('cities_open');

					html.find('a').click(function(){
						var target=$(this),
							id = target.attr('class');

						$('li',prnt).removeClass('active');
						$('a.'+id, prnt).parent().addClass('active');

						$('.change_promotions_city span').html(target.html()).attr('class', 'city_'+id);
						$('.cities').addClass('none').removeClass('cities_open');

						Functions.getPromotions(id);
						return false;
					});
				}
			}
		};

		return {
			'createAdapter':function(nameAdapter){
				if(!(nameAdapter in adapters))
					throw 'adapter unsupported';

				return new adapters[nameAdapter]();
			}
		}
	})();

	return Geo;
});