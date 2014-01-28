$(function(){
	if(typeof VMap!='function')
		return;

	$('.adm-detail-tab').on('click',function(){
		$(window).resize();
		var context=$('.adm-detail-content:visible'),
			elat=context.find('.estelife-latlng');

		if(elat.length>0){
			var latlng=elat.data('latlng');
			context.find('.gmap').data('gmap').center(latlng.lat,latlng.lng);

			elat.data('latlng',false);
			elat.removeClass('estelife-latlng');
		}
	});

	var bd=$('body'),
		icons={
			'default':'/bitrix/themes/.default/icons/estelife/point_green.png'
		};

	$('.gmap').each(function(){
		$(this).show(0,function(){
			var jmap=$(this),
				prnt=jmap.parents('table:first'),
				map=new VMap(),
				lat=$('input[name*=\'latitude\']',prnt).val(),
				lng=$('input[name*=\'longitude\']',prnt).val();

			map.markers().icons(icons);

			if(lat!='' && lng!=''){
				var adc=prnt.parents('.adm-detail-content:first');

				if(adc.is(':hidden')){
					prnt.addClass('estelife-latlng').data('latlng',{
						'lat':lat,
						'lng':lng
					});
				}

				map.create(jmap,lat,lng);
				map.zoom(14);
				map.markers().clear();
				map.markers().add(new map.marker(lat,lng));
				map.markers().draw();
			}else{
				map.create(jmap,59.9395237,30.312020599999983);
				map.zoom(10);
			}

			google.maps.event.addListener(map.map(),'dblclick',function(e){
				var lat=e.latLng.lb,
					lng=e.latLng.mb;
				map.geocode(e.latLng,map.latlng);
			});

			map.on('geocode_true',geocodeResult);
			jmap.data('gmap',map);
		});
	});

	bd.on('click','.estelife-services .forms a',function(){
		var lnk=$(this),
			prnt=lnk.parent();

		if(prnt.find('form').length>0)
			return false;

		var index,
			action=prnt.attr('data-action'),
			form=$('<form></form>').attr({
				'method':'post',
				'action':lnk.attr('href')
			});

		form.append(
			'<label for="name">Название</label>',
			'<input type="text" name="name" id="name" value="" />',
			'<input type="submit" name="send" value="Сохранить" class="adm-btn-save" />',
			'<input type="hidden" name="action" value="save_'+action+'" />'
		);

		if((index=$.inArray(action,['service','methods','service_concreate']))>-1){
			var lists=$('.estelife-services .lists'),
				spec=lists.find('ul.specialization a.active').attr('data-id'),
				serv=(index>0) ?
					lists.find('ul.service a.active').attr('data-id') : 0;

			form.append(
				'<input type="hidden" name="spec_id" value="'+spec+'" />',
				'<input type="hidden" name="service_id" value="'+serv+'" />'
			);
		}

		prnt.append(form);
		lnk.hide();

		form.submit(function(){
			var frm=$(this),
				prnt=frm.parent(),
				data={};

			frm.find('input').each(function(){
				var inpt=$(this);
				data[inpt.attr('name')]=inpt.val();
			});

			if(!('name' in data)){
				alert('Системная ошибка. Обратитесь к разработчику.');
				return false;
			}else if(data.name==''){
				alert('Заполните поле название');
				return false;
			}

			$.post('/bitrix/admin/estelife_ajax.php',data,function(r){
				if('specialization' in r){
					$('ul.specialization').append('<li><a href="#" data-id="'+ r.specialization.id+'">'+r.specialization.name+'</a></li>');
				}else if('service' in r){
					$('ul.service').append('<li><a href="#" data-id="'+ r.service.id+'">'+r.service.name+'</a></li>');
				}else if('service_concreate' in r){
					$('ul.service_concreate').append('<li><a href="#" data-id="'+ r.service_concreate.id+'">'+r.service_concreate.name+'</a></li>');
				}else if('methods' in r){
					$('ul.methods').append('<li><a href="#" data-id="'+ r.methods.id+'">'+r.methods.name+'</a></li>');
				}else if('error' in r)
					alert(r.error.text);

				frm.remove();
				prnt.find('a').show();
			},'json');

			return false;
		});
		return false;
	});
	bd.on('click','.estelife-services .selected a',function(){
		var prnt=$(this).parent(),
			sl=prnt.parent(),
			id=prnt.attr('data-cservice');
		prnt.remove();

		if(sl.find('li').length<=0)
			sl.hide();

		$('.service_concreate a[data-id='+id+']').removeClass('active');
	});
	bd.on('click','.estelife-services .lists a',function(){
		var forms=$('.estelife-services .forms'),
			lnk=$(this),
			prnt=lnk.parents('ul'),
			id=lnk.attr('data-id'),
			cls=prnt.attr('class');

		if(lnk.hasClass('active'))
			return false;

		var action=(cls=='specialization') ?
			'get_service' :
			(cls=='service' || cls=='methods' ?
				'get_service_concreate' :
				'set');

		if(action=='set'){
			var temp=[],
				active=$('.estelife-services .lists a.active').not('.service_concreate .active'),
				sl=$('.estelife-services .selected');

			$.merge(active,lnk);

			active.each(function(){
				var t=$(this).clone(true);
				console.log(t.html());
				temp.push(t.html());
			});

			var spec=active.eq(0).attr('data-id'),
				serv=active.eq(1).attr('data-id'),
				cserv=(active.length==4) ?
					active.eq(3).attr('data-id') :
					active.eq(2).attr('data-id');

			if(sl.find('[data-cservice='+cserv+']').length>0)
				return false;

			sl.show();
			sl.append('<li data-spec="'+spec+'" data-service="'+serv+'" data-cservice="'+cserv+'">'+temp.join(', ')+'<input type="hidden" name="services[]" value="'+cserv+'" /><a href="#">x</a></li>');
			lnk.addClass('active');
		}else{
			$.get('/bitrix/admin/estelife_ajax.php',{
				'action':action,
				'id':id,
				'by_method':(cls=='methods') ? 1 : 0
			},function(r){
				if('list' in r || 'methods' in r){
					var uls=prnt.parent().find('ul'),
						ul=(cls=='specialization' ? uls.filter('.service') : uls.filter('.service_concreate')),
						frm_prnt=forms.find('[data-action='+cls+']').next(),
						currents=[],
						currentPrices=[];

					ul.empty();
					if(cls=='specialization')
						ul.next().empty();

					if(cls=='service' || cls=='methods'){
						var sl=$('.estelife-services .selected');
						sl.find('li').each(function(){
							currents.push($(this).attr('data-cservice'))
							currentPrices.push($(this).find('input[type=text]').val());
						});
					}

					var current;

					for(var i=0; i<r.list.length; i++){
						ul.append('<li><a href="#" data-id="'+ r.list[i].id+'">'+ r.list[i].name+'</a></li>');

						if((current=$.inArray(r.list[i].id,currents))>-1){
							ul.find('li:last a').addClass('active');
							ul.find('li:last a').append('<input type="text" name="service_price['+r.list[i].id+']" value="'+currentPrices[current]+'" />');
						}else if(cls=='service' || cls=='methods')
							ul.find('li:last a').append('<input type="text" name="service_price['+r.list[i].id+']" value="0.00" />');
					}

					prnt.find('.active').removeClass('active');
					lnk.addClass('active');

					frm_prnt.find('form').remove();
					frm_prnt.find('a').show();
					frm_prnt.show();

					if(cls=='specialization'){
						frm_prnt=frm_prnt.next();
						frm_prnt.find('form').remove();
						frm_prnt.find('a').show();
						frm_prnt.hide();
					}

					if(cls=='specialization' || cls=='service'){
						var methods=uls.filter('.methods');
						methods.empty();

						for(var i=0; i<r.methods.length; i++)
							methods.append('<li><a href="#" data-id="'+ r.methods[i].id+'">'+ r.methods[i].name+'</a></li>');
					}
				}else if('error' in r){
					alert(r.error)
				}
			},'json');
		}

		return false;
	});

	var ctrlActive = 0;
	var countClick = 0;
	var firstClick = 0;
	var firtsTr = 0;
	var firstTd = 0;
	var secondTd = 0;
	var secondTr = 0;
	var firstElem = '';
	var secondClick = 0;
	var secondElem = '';
	var action_active = 1;

	$(document).keydown(function (e) {

		if(e.which == 90){
			ctrlActive = 1;
		}
	});

	$(document).keyup(function (e) {
			ctrlActive = 0;
			countClick = 0;
	});




	$('.estelife-services .selected li').each(function(){
		var id=$(this).attr('data-cservice')
		$('.service_concreate a[data-id='+id+']').addClass('active').find('input').val($(this).find('input[type=text]').val())
	});

	bd.on('keyup','.estelife-services .lists input[type=text], .estelife-services .selected input[type=text]',function(){
		var inpt=$(this),
			name=inpt.attr('name');

		$('input[name=\''+name+'\']').not(inpt).val(inpt.val());
	});

	var bh=$('.estelife-busy-hours');
	bh.on('mouseover mouseout click','th, td.h',function(e){
		var el=$(this),
			tag=this.tagName.toLowerCase(),
			trg=$();

		if(e.type=='mouseover' || e.type=='click'){
			if(tag=='th' && el.hasClass('all')){
				trg=bh.find('td').not('.h');
			}else if(tag=='th'){
				var index=bh.find('th').index(el);
				bh.find('tr').each(function(){
					$.merge(trg,$(this).find('td').eq(index))
				});
			}else{
				trg=el.parent().find('td').not('.h')
			}

			if(e.type=='mouseover'){
				trg.addClass('hover')
			}else{
				if(el.hasClass('bh-click')){
					el.removeClass('bh-click');
					trg.filter('.bh-active')
						.removeClass('active')
						.find('input').prop('checked',false);
				}else{
					el.addClass('bh-click');
					trg.not('.active')
						.addClass('bh-active active')
						.find('input').prop('checked',true);
				}
			}
		}else{
			bh.find('td.hover').removeClass('hover');
		}
	}).on('click','td:not(.h)',function(){
			var cur=$(this),
				table=$('.estelife-busy-hours'),
				prnt=cur.parent(),
				all_td=table.find('td'),
				all_tr=table.find('tr'),
				currindex_temp=all_td.index(cur),
				cur_tr = cur.find('input').val(),

				active=prnt.find('.active');

				var cur_td = prnt.find('td').index(cur);

				if(ctrlActive == 1){
					if(cur.hasClass('active')){
						cur.removeClass('active').find('input').prop('checked',false);
					}else{
						cur.removeClass('bh-active').addClass('active').find('input').prop('checked',true);
					}

					var td = $('td');
					countClick = countClick+1;
					var curId = $(this)[0]['id'];

					if(countClick == 1){

						if(cur.hasClass('active')){
							action_active = 1;
						}else{
							action_active = 0;
						}

						firstClick = curId;
						firstClick = parseFloat(firstClick);
						firtsTr = cur_tr;
						firtsTr = parseFloat(firtsTr);
						firstTd = cur_td;
						firstTd = parseFloat(firstTd);
						firstElem = cur;
						if(firstElem.hasClass('active')){
							//firstElem.removeClass('bh-active active').find('input').prop('checked',false);
						}
					}else if(countClick == 2){
						secondClick = curId;
						secondClick = parseFloat(secondClick);
						secondElem = cur;
						secondTr = cur_tr;
						secondTr = parseFloat(secondTr);
						secondTd = cur_td;
						secondTd = parseFloat(secondTd);
					}


					if(countClick == 2){
						$.each(all_tr,  function(i)   {

							$.each($(this).find('td') , function(k){
								var td_class =this.className;
								var  start_k = k+1;
								var finish_k = k;

								console.log(action_active);

								if(i >= firtsTr && i <= secondTr && start_k >= firstTd+1 && finish_k < secondTd+1){

									if(action_active == 0){
										$(this).removeClass('bh-active active').find('input').prop('checked',false);
									}else{
										$(this).removeClass('bh-active').addClass('active').find('input').prop('checked',true);
									}

									if(td_class !="active"){

									}
									if(td_class == 'active'){


									}
								}
							});

						});
						countClick = 0;
					}

				}else{
					if(cur.hasClass('active')){
						cur.removeClass('active').find('input').prop('checked',false);
					}else{
						cur.removeClass('bh-active').addClass('active').find('input').prop('checked',true);
					}
				}

			cur.removeClass('bh-active');

			if(cur.hasClass('active')){
				if(active.length==1){
					//cur.removeClass('active').find('input').prop('checked',false);
				}else if(active.length>1){
					var currindex=all_td.index(cur)+1,
						maxindex=all_td.index(active.eq(active.length-1));

					/*for(var i=currindex; i<=maxindex; i++){
						all_td.eq(i).removeClass('bh-active active').find('input').prop('checked',false);
					}
				}
			}else{
				if(active.length<1){
					cur.addClass('active').find('input').prop('checked',true);
				}else if(active.length>=1){
					var currindex=all_td.index(cur),
						activeindex=all_td.index(active.eq(0)),
						start=currindex,
						end=activeindex;
					//console.log(currindex);

					if(activeindex<currindex){
						start=activeindex;
						end=currindex;
					}
/*
					for(var i=start;i<=end;i++)
						all_td.eq(i).removeClass('bh-active').addClass('active').find('input').prop('checked',true);*/
				}
			}
		});

	bh.find('input:checked').parent().addClass('active');

	$('input[name*=\'city_name\'],input[name*=\'metro_name\'], input[name*=\'country_name\']').autocomplete({
		minLength:3,
		source: function(request,response) {
			var action='get_countries',
				inpt=this.element,
				name=this.element.attr('name');

			if(name.match(/(metro_name)+/gi)){
				var city=parseInt($('input[name=\''+name.replace('metro_name','city_id')+'\']').val());
				if(isNaN(city) || city<=0){
					alert('Выберите город')
					return false;
				}
				action='get_metros';
			}else if (name.match(/(city_name)+/gi)){
				var country_id= 0,
					country = $('input[name=\''+name.replace('city_name','country_id')+'\']');
				if(country.length>0){
					country_id=country.val();
				}
				action='get_cities';
			}

			$.get('/bitrix/admin/estelife_ajax.php',{
				'action':action,
				'term':request.term,
				'city_id':city,
				'country_id':country_id
			},function(r){
				if('list' in r){
					if(r.list.length==1){
						var item= r.list.shift();
						inpt.val(item.name);
						$('input[name*=\''+inpt.attr('data-input')+'\']',inpt.parent()).val(item.id);

						if(name.match(/(city_name)+/gi)){
							$('input[name=\''+name.replace('city_name','metro_name')+'\']').prop('readonly',false);
							$('input[name=\''+name.replace('city_name','address')+'\']').prop('readonly',false);
						}
					}else{
						response($.map(r.list, function(item) {
							return {
								label: item.name,
								value: item.name,
								id: item.id
							}
						}));
					}
				}
			},'json');

			return true;
		},
		select:function(e, ui) {
			var inpt=$(this),
				name=inpt.attr('name');

			$('input[name*=\''+inpt.attr('data-input')+'\']',inpt.parent()).val(ui.item.id)

			if(name.match(/(city_name)+/gi)){
				$('input[name=\''+name.replace('city_name','metro_name')+'\']').prop('readonly',false);
				$('input[name=\''+name.replace('city_name','address')+'\']').prop('readonly',false);
			}
		}
	})/*.on('paste',function(e){
	 var element=$(this),
	 action='get_cities',
	 term=e.originalEvent.clipboardData.getData('Text');

	 if(element.attr('name')=='metro_name'){
	 var city=parseInt($('input[name=city_id]').val());

	 if(isNaN(city) || city<=0){
	 alert('Выберите город')
	 return false;
	 }

	 action='get_metros';
	 }

	 $.get('/bitrix/admin/estelife_ajax.php',{
	 'action':action,
	 'term':term,
	 'city_id':city
	 },function(r){
	 if('list' in r){
	 if(r.list.length>0){
	 var item= r.list.shift();
	 element.val(item.name);
	 $('input[name='+element.attr('data-input')+']').val(item.id);
	 $('input[name=metro_name]').prop('readonly',false);
	 $('input[name=address]').prop('readonly',false);
	 }
	 }
	 },'json');
	 });*/

	$('input[name*=\'address\']').autocomplete({
		minLength:3,
		source: function(request,response) {
			var inpt=this.element,
				prnt=inpt.parents('table:first'),
				name=this.element.attr('name'),
				city=$('input[name=\''+name.replace('address','city_name')+'\']').val();

			if(city.length<3){
				alert('Не указан город');
				return false;
			}

			$.get('/bitrix/admin/estelife_ajax.php',{
				'action':'address',
				'term':request.term,
				'city':city
			},function(r){
				if('list' in r){
					if(r.list.length==1){
						var item= r.list.shift(),
							map=prnt.find('.gmap').data('gmap');

						//inpt.val(item.name);

						if(map)
							map.geocode(item.address);

						response();
					}else{
						response($.map(r.list, function(item) {
							return {
								label: item.name,
								value: item.name,
								address: item.address
							}
						}));
					}
				}
			},'json');

			return true;
		},
		select:function(e, ui) {
			var inpt=$(this),
				map=inpt.parents('table:first').find('.gmap').data('gmap');

			if(map)
				map.geocode(ui.item.address);
		}
	})/*.on('paste change',function(e){
	 var name=$(this).attr('name'),
	 city=$('input[name=\''+name.replace('address','city_name')+'\']').val(),
	 val=(e.type=='paste') ?
	 e.originalEvent.clipboardData.getData('Text') :
	 $(this).val();

	 if(city.length<3){
	 alert('Не указан город');
	 }else{
	 map.geocode('Россия, '+city+', '+val);
	 }
	 });*/

	$('input[name=clinic_name]').autocomplete({
		minLength:3,
		source:function(request,response){
			$.get('/bitrix/admin/estelife_ajax.php',{
				'action':'clinic',
				'term':request.term
			},function(r){
				if('list' in r){
					response($.map(r.list, function(item) {
						return {
							label: item.name,
							value: item.name,
							address: item.id
						}
					}));
				}
			},'json');
		},
		select:function(e, ui){
			var lnk = $('input[name='+$(this).attr('data-input')+']');
			lnk.val(ui.item.id);

		}
	}).on('paste',function(e){
			var inpt=$(this),
				val=e.originalEvent.clipboardData.getData('Text');
			$.get('/bitrix/admin/estelife_ajax.php',{
				'action':'clinic',
				'term':val
			},function(r){
				if('list' in r){
					var item= r.list.shift();
					inpt.val(item.name);
					$('input[name='+inpt.attr('data-input')+']').val(item.id);
				}
			},'json');
		});



	bd.on('click','.estelife-use .use a',function(){
		var lnk=$(this),
			prnt=lnk.parent(),
			spans=prnt.find('span'),
			inputs=prnt.parent().find('input');

		spans.each(function(){
			var ff_name=$(this).attr('data-input');
			inputs.filter('[name='+ff_name+']').val($(this).html());
		});

		lnk.hide();
		return false;
	});

	bd.on('click','.one-list a',function(){
		var lnk=$(this);

		if(lnk.hasClass('active')){
			lnk.removeClass('active');
			lnk.prev().prop('checked',false);
		}else{
			lnk.addClass('active');
			lnk.prev().prop('checked',true);
		}

		return false;
	});
});
$(function(){
	$('body').on('click','.estelife-more',function(){
		var lnk=$(this),
			prnt=lnk.parents('tr:first');

		if(lnk.hasClass('estelife-delete')){
			prnt.remove();
		}else{
			var cln=prnt.clone(true);
			cln.find('.estelife-more')
				.removeClass('adm-btn-save')
				.addClass('adm-btn-delete estelife-delete')
				.html('');

			setTimeout(function(){
				var field=prnt.find('input,textarea,select').not('.ignore_cleared');

				if(field.length>0 && field[0].tagName=='SELECT'){
					cln.find('select')[0].selectedIndex=field[0].selectedIndex;
					field[0].selectedIndex=0;
				}else
					field.val('');
			},0);
			prnt.before(cln);
		}
		return false;
	});

	//Получение компаний
	$('input[name*=\'company_name\'],input[name=find_company_name]').autocomplete({
		minLength:3,
		source:function(request,response){
			var inpt=this.element,
				type=inpt.parent().find('input[name=company_type_id]').val();
			$.get('/bitrix/admin/estelife_ajax.php',{
				'action':'company',
				'term':request.term,
				'type_id': type
			},function(r){
				if('list' in r){
					if(r.list.length==1){
						var item= r.list.shift(),
							prnt=inpt.parent();

						inpt.val(item.name);
						$('input[name*=\''+inpt.attr('data-input')+'\']',prnt).val(item.id);
						response();

						if(inpt.hasClass('estelife-need-clone')){
							prnt.find('.estelife-more').click();
						}
					}else{
						response($.map(r.list, function(item) {
							return {
								label: item.name,
								value: item.name,
								'id': item.id
							}
						}));
					}
				}
			},'json');
		},
		select:function(e, ui){
			var inpt=$(this),
				prnt=inpt.parent();

			$('input[name*=\''+inpt.attr('data-input')+'\']',inpt.parent()).val(ui.item.id);

			if(inpt.hasClass('estelife-need-clone')){
				prnt.find('.estelife-more').click();
				prnt.parent().prev().find('input[type=text]').val(ui.item.value);
			}
		}
	}).on('paste',function(e){
			var inpt=$(this),
				val=e.originalEvent.clipboardData.getData('Text'),
				prnt=inpt.parent(),
				type=prnt.find('input[name=company_type_id]').val();
			$.get('/bitrix/admin/estelife_ajax.php',{
				'action':'company',
				'term':val,
				'type_id': type
			},function(r){
				if('list' in r){
					var item= r.list.shift();
					inpt.val(item.name);
					$('input[name*=\''+inpt.attr('data-input')+'\']',prnt).val(item.id);

					if(inpt.hasClass('estelife-need-clone')){
						prnt.find('.estelife-more').click();
					}
				}
			},'json');
		});


	//Получение аппаратов
	$('input[name*=\'apparatus_name\'],input[name=find_apparatus_name]').autocomplete({
		minLength:3,
		source:function(request,response){
			var inpt=this.element;
			$.get('/bitrix/admin/estelife_ajax.php',{
				'action':'apparatus',
				'term':request.term
			},function(r){
				if('list' in r){
					if(r.list.length==1){
						var item= r.list.shift(),
							prnt=inpt.parent();

						inpt.val(item.name);
						$('input[name*=\''+inpt.attr('data-input')+'\']',prnt).val(item.id);
						response();

						if(inpt.hasClass('estelife-need-clone')){
							prnt.find('.estelife-more').click();
						}
					}else{
						response($.map(r.list, function(item) {
							return {
								label: item.name,
								value: item.name,
								'id': item.id
							}
						}));
					}
				}
			},'json');
		},
		select:function(e, ui){
			var inpt=$(this),
				prnt=inpt.parent();

			$('input[name*=\''+inpt.attr('data-input')+'\']',inpt.parent()).val(ui.item.id);

			if(inpt.hasClass('estelife-need-clone')){
				prnt.find('.estelife-more').click();
				prnt.parent().prev().find('input[type=text]').val(ui.item.value);
			}
		}
	}).on('paste',function(e){
			var inpt=$(this),
				val=e.originalEvent.clipboardData.getData('Text'),
				prnt=inpt.parent();
			$.get('/bitrix/admin/estelife_ajax.php',{
				'action':'apparatus',
				'term':val
			},function(r){
				if('list' in r){
					var item= r.list.shift();
					inpt.val(item.name);
					$('input[name*=\''+inpt.attr('data-input')+'\']',prnt).val(item.id);

					if(inpt.hasClass('estelife-need-clone')){
						prnt.find('.estelife-more').click();
					}
				}
			},'json');
		});


	//Получение статьи
	$('input[name*=\'articles\']').autocomplete({
		minLength:3,
		source:function(request,response){
			var inpt=this.element;
			$.get('/bitrix/admin/estelife_ajax.php',{
				'action':'articles',
				'term':request.term
			},function(r){
				if('list' in r){
					if(r.list.length==1){
						var item= r.list.shift(),
							prnt=inpt.parent();

						inpt.val(item.name);
						$('input[name*=\''+inpt.attr('data-input')+'\']',prnt).val(item.id);
						response();

						if(inpt.hasClass('estelife-need-clone')){
							prnt.find('.estelife-more').click();
						}
					}else{
						response($.map(r.list, function(item) {
							return {
								label: item.NAME,
								value: item.NAME,
								'id': item.ID
							}
						}));
					}
				}
			},'json');
		},
		select:function(e, ui){
			var inpt=$(this),
				prnt=inpt.parent();

			$('input[name*=\''+inpt.attr('data-input')+'\']',inpt.parent()).val(ui.item.id);

			if(inpt.hasClass('estelife-need-clone')){
				prnt.find('.estelife-more').click();
				prnt.parent().prev().find('input[type=text]').val(ui.item.value);
			}
		}
	});


	//Получение препаратов
	$('input[name*=\'pill_name\'],input[name=find_pill_name]').autocomplete({
		minLength:3,
		source:function(request,response){
			var inpt=this.element;
			$.get('/bitrix/admin/estelife_ajax.php',{
				'action':'pills',
				'term':request.term
			},function(r){
				if('list' in r){
					if(r.list.length==1){
						var item= r.list.shift(),
							prnt=inpt.parent();

						inpt.val(item.name);
						$('input[name*=\''+inpt.attr('data-input')+'\']',prnt).val(item.id);
						response();

						if(inpt.hasClass('estelife-need-clone')){
							prnt.find('.estelife-more').click();
						}
					}else{
						response($.map(r.list, function(item) {
							return {
								label: item.name,
								value: item.name,
								'id': item.id
							}
						}));
					}
				}
			},'json');
		},
		select:function(e, ui){
			var inpt=$(this),
				prnt=inpt.parent();

			$('input[name*=\''+inpt.attr('data-input')+'\']',inpt.parent()).val(ui.item.id);

			if(inpt.hasClass('estelife-need-clone')){
				prnt.find('.estelife-more').click();
				prnt.parent().prev().find('input[type=text]').val(ui.item.value);
			}
		}
	}).on('paste',function(e){
			var inpt=$(this),
				val=e.originalEvent.clipboardData.getData('Text'),
				prnt=inpt.parent();
			$.get('/bitrix/admin/estelife_ajax.php',{
				'action':'pills',
				'term':val
			},function(r){
				if('list' in r){
					var item= r.list.shift();
					inpt.val(item.name);
					$('input[name*=\''+inpt.attr('data-input')+'\']',prnt).val(item.id);

					if(inpt.hasClass('estelife-need-clone')){
						prnt.find('.estelife-more').click();
					}
				}
			},'json');
		});


	$('select[name=specialization_id],select[name=find_specialization_id]').change(function(){
		var srv=$('select[name=service_id],select[name=find_service_id]'),
			mt=$('select[name=method_id],select[name=find_method_id]');

		if(srv.length<=0)
			return;

		var id=$(this).val();
		srv.find('option').not(':first').remove();
		mt.find('option').not(':first').remove();

		$.get('/bitrix/admin/estelife_ajax.php',{
			'action':'get_service',
			'id':id
		},function(r){
			if('list' in r){
				for(var i=0; i< r.list.length; i++){
					srv.append('<option value="'+ r.list[i].id+'">'+ r.list[i].name+'</option>')
				}
			}

			if('methods' in r){
				for(var i=0; i< r.methods.length; i++){
					mt.append('<option value="'+ r.methods[i].id+'">'+ r.methods[i].name+'</option>')
				}
			}
		},'json');
	});

	$('body').on('change','select[name=service_id],select[name=find_service_id]', function(){
		var scrv=$('select[name=specialization_id],select[name=find_specialization_id]'),
			mt=$('select[name=method_id],select[name=find_method_id]');

		if(scrv.length<=0)
			return;

		var id=$(this).val();
		mt.find('option').not(':first').remove();



		$.get('/bitrix/admin/estelife_ajax.php',{
			'action':'get_service_concreate',
			'id':id
		},function(r){
			if('methods' in r){
				for(var i=0; i< r.methods.length; i++){
					mt.append('<option value="'+ r.methods[i].id+'">'+ r.methods[i].name+'</option>')
				}
			}
		},'json');
	});

	if(typeof $().damnUploader=='function'){
		var fileInput=$('input#gallery, input.gallery').damnUploader({
			url: '/bitrix/admin/estelife_file_uploader.php',
			fieldName: 'gallery',
			dataType: 'json'
		});
	}

	$('select[name*=\'country_id\']').change(function(){
		var sl=$(this),
			name=sl.attr('name'),
			city=$('select[name='+name.replace('country_id','city_id')+']');

		if(city.length<=0)
			return;

		var id=$(this).val();
		city.find('option').not(':first').remove();

		$.get('/bitrix/admin/estelife_ajax.php',{
			'action':'get_cities',
			'country_id':id
		},function(r){
			if('list' in r){
				for(var i=0; i< r.list.length; i++){
					city.append('<option value="'+ r.list[i].id+'">'+ r.list[i].name+'</option>')
				}
			}
		},'json');
	});

	$('select[name=city_id],select[name=find_city_id]').change(function(){
		var sl=$(this),
			metro=$('select[name=metro_id],select[name=find_metro_id]');

		if(metro.length<=0)
			return;

		var id=$(this).val();
		metro.find('option').not(':first').remove();
		$.get('/bitrix/admin/estelife_ajax.php',{
			'action':'get_metros',
			'city_id':id
		},function(r){
			if('list' in r){
				for(var i=0; i< r.list.length; i++){
					metro.append('<option value="'+ r.list[i].id+'">'+ r.list[i].name+'</option>')
				}
			}
		},'json');
	});

	$('input[name*=\'clinic_name\']').autocomplete({
		minLength:3,
		source: function(request,response) {
			$.get('/bitrix/admin/estelife_ajax.php',{
				'action':'clinic',
				'term':request.term
			},function(r){
				if('list' in r){
					response($.map(r.list, function(item) {
						return {
							label: item.name,
							value: item.name,
							id: item.id
						}
					}));
				}
			},'json');

			return true;
		},
		select:function(e, ui) {
			var inpt=$(this),
				prnt=inpt.parent();

			$('input[name*=\''+inpt.attr('data-input')+'\']',inpt.parent()).val(ui.item.id);

			if(inpt.hasClass('estelife-need-clone')){
				prnt.find('.estelife-more').click();
				prnt.parent().prev().find('input[type=text]').val(ui.item.value);
			}
		}
	});

	$('.estelife-send a, .one-list a.view').click(function(){
		location.href=$(this).attr('href')
	});

	$('body').on('click', '.estelife-add-gallery', function(){
		var inpt = $('input[name=gallery_name]');

		if (inpt.val().length > 0){
			$.get('/bitrix/admin/estelife_ajax.php',{
				'action':'add_gallery',
				'term':inpt.val()
			},function(r){
				if('list' in r && r.list>0){
					inpt.val('');
				}
			},'json');
		}

	})



});
$(function(){
	if(matches=location.hash.match(/^\#tab([0-9]+)$/i)){
		$('#tab_cont_edit'+matches[1]).click();
	}
});
function geocodeResult(lat,lng,address){
	var context=$('.adm-detail-content:visible'),
		map=context.find('.gmap').data('gmap');

	if(!map)
		return;

	if(address){
		$.get('/bitrix/admin/estelife_ajax.php',{
			'action':'get_cities',
			'term':address.city
		},function(r){
			if('list' in r && r.list.length>0){
				var city=r.list.shift();
				$('input[name*=\'city_id\']',context).val(city.id);
				$('input[name*=\'city_name\']',context).val(city.name);
				$('input[name*=\'metro_name\']',context).prop('readonly',false);
				$('input[name*=\'address\']',context).prop('readonly',false).val(address.street+' '+address.house);

				map.center(lat,lng,14);
				map.markers().clear();
				map.markers().add(new map.marker(lat,lng));
				map.markers().draw();

				$('input[name*=\'latitude\']',context).val(lat);
				$('input[name*=\'longitude\']',context).val(lng);
			}else{
				alert('К сожалению, estelife не знает такого города.')
			}
		},'json');
	}else{
		map.center(lat,lng,14);
		map.markers().clear();
		map.markers().add(new map.marker(lat,lng));
		map.markers().draw();

		$('input[name*=\'latitude\']',context).val(lat);
		$('input[name*=\'longitude\']',context).val(lng);
	}
}

function setEventDate(d){
	var dates=$('.event-dates'),
		li=dates.find('li:last'),
		cln=li.clone(true);

	cln.find('input:first').val(d);
	cln.find('a')
		.removeClass('adm-btn-save')
		.addClass('adm-btn-delete estelife-delete')
		.html('');
	li.before(cln);
}

$(function(){
	(function(){
		var arm=['январь','февраль','март','апрель','май','июнь','июль','август','сентябрь','октябрь','ноябрь','декабрь'],
			dpkl = $("#datepicker1"),
			dpkr = $("#datepicker2"),
			d=new Date(),
			m=d.getMonth(),
			y2=d.getFullYear(),
			y1=(m>0) ? y2 : y2-1,
			ml=(m<1) ? 11 : m-1;

		d.setDate(1);
		d.setMonth((m>0) ? m-1 : 11);
		d.setFullYear(y1);

		changeArrow(ml,y2,'lc');
		changeArrow(m,y2,'rc');

		dpkl.datepicker({
			firstDay : "1",
			defaultDate: d,
			stepMonths:1,
			onSelect:function(d){
				dpkr.find('.ui-state-active').removeClass('ui-state-active');
				setEventDate(d);
			}
		});

		setTimeout(function(){
			dpkl.find('.ui-state-active').removeClass('ui-state-active');
		},100);

		dpkr.datepicker({
			firstDay : "1",
			stepMonths:1,

			onSelect:function(d){
				dpkl.find('.ui-state-active').removeClass('ui-state-active');
				setEventDate(d);
			},
			onChangeMonthYear:function(y,m){
				var date=new Date(dpkr.datepicker('getDate')),
					mn=(m<2) ? (11+m)-1 : m-2,
					y=(mn>m) ? y-1 : y;

				date.setDate(1)
				date.setMonth(mn);
				date.setYear(y);

				dpkl.datepicker('setDate',date);

				changeArrow(mn,y,'lc');
				changeArrow(m-1,y,'rc');
				dpkl.find('.ui-state-active').removeClass('ui-state-active');
			}
		});

		$('.calendar_l a.ar.l,.calendar_l a.ar.r').click(function(){
			var date = new Date(dpkr.datepicker( "getDate" )),
				m=date.getMonth(),
				y=date.getFullYear(),
				am,ym;

			if($(this).hasClass('l')){
				y=(m>0) ? y : y-1;
				m=(m>0) ? m-1 : 11;
			}else{
				y=(m<11) ? y : y+1;
				m=(m<11) ? m+1 : 0;
			}

			date.setMonth(m);
			date.setFullYear(y);
			dpkr.datepicker('setDate',date);

			changeArrow(m,y,'rc');
			return false;
		});

		function changeArrow(m,y,t){
			var lnk=$('#'+t),
				mn=0;

			if(t=='lc'){
				mn=((m>0) ? m-1 : 11);
				y=((m>0) ? y : y-1);
			}else{
				mn=((m<11) ? m+1 : 0);
				y=((m<11) ? y : y+1);
			}

			lnk.html(arm[mn] + "<span> "+y+" </span><i></i>");
		}
	})();

	$('.date_select').datepicker();

	var bd=$('body');

	bd.on('click','.event-dates .estelife-btn',function(){
		var lnk=$(this),
			prnt=lnk.parent();

		if(lnk.hasClass('estelife-delete'))
			prnt.remove();
		else{
			var val=prnt.find('input:first').val(),
				cln=prnt.clone(true);

			if(!val.match(/^[0-9]{2}\s[a-zа-я]+\s[0-9]{4}$/i)){
				alert('Заполните поле даты');
				return false;
			}

			cln.find('a.estelife-btn')
				.html('')
				.removeClass('adm-btn-save')
				.addClass('adm-btn-delete estelife-delete');

			prnt.before(cln)
				.find('input').val('');
		}

		return false;
	});

	bd.on('keydown','.event-dates input.time',function(e){
		var inpt=$(this),
			val=inpt.val(),
			code=e.charCode || e.keyCode,
			av=$.inArray(code,[186,46,8,37,39,9]);

		if(av<=-1 && ((code<48 || code>57) && (code<96 || code>105)))
			return false;

		if(av<=-1){
			temp=val.replace(/[\D]+/,'');

			if(temp.length>3){
				var next=inpt.next();
				if(next.hasClass('time'))
					next.focus();
				return false;
			}

			if(val.indexOf(':')<=-1){
				val=temp.replace(/^([0-9]{2})/,'$1:');
				inpt.val(val);
			}
		}else if(av==2 && val.length==0){
			var prev=inpt.prev();
			if(prev.hasClass('time'))
				prev.focus();
		}
	});

	bd.on('click','.estelife-checklist label:not(.adm-designed-checkbox-label)',function(){
		var li=$(this),
			inpt=li.find('input');

		if(!inpt.prop('checked')){
			li.removeClass('active');
		}else{
			li.addClass('active');
		}
	});

	$('.estelife-checklist label').each(function(){
		var li=$(this),
			inpt=li.find('input');

		if(!inpt.prop('checked')){
			li.removeClass('active');
		}else{
			li.addClass('active');
		}
	});

	bd.on('click','.estelife-prepod a',function(){
		var prnt=$(this).parents('.estelife-prepod');
		prnt.remove();
		return false;
	});


	bd.on('click','.estelife-prepod-more, .estelife-prepod-delete',function(){
		var lnk=$(this),
			prnt=lnk.parents('table:first');

		if(lnk.hasClass('estelife-prepod-delete'))
			prnt.remove();
		else{
			var cln=prnt.clone(true);
			cln.find('.estelife-prepod-more')
				.removeClass('adm-btn-save adm-btn-add estelife-prepod-more')
				.addClass('estelife-prepod-delete').html('Удалить');
			cln.removeClass('estelife-prepod-table-first');
			prnt.before(cln);
			prnt.find('input').val('');
		}

		return false;
	});

	bd.on('click','.estelife-gallery a.photo-delete',function(){
		var prnt=$(this).parents('td:first'),
			id=parseInt(prnt.find('.drop-item').attr('data-id'));

		if(!isNaN(id) && id>0){
			$.post('/bitrix/admin/estelife_ajax.php',{
				'action':'delete_photo',
				'id':id
			},function(r){
				if('error' in r){
					prnt.find('.drop-progress')
						.addClass('error')
						.css({
							'display':'block',
							'width':'100%'
						});
					alert(r.error.text);
				}else{
					prnt.remove();
				}
			},'json');
		}else
			prnt.remove();
		return false;
	});

	$('.estelife-gallery .gallery-item-photos').each(function(){
		var gallery_id=$(this).parents('.gallery-item:first').attr('data-id');
		dropUploader($(this),'/bitrix/admin/estelife_ajax.php',{
			'field':'photo',
			'action':'upload_photo',
			'gallery_id':gallery_id,
			'callbacks':new dropCallbacks()
		});
	});

	bd.on('click','.estelife-gallery .gallery-add a',function(){
		var prnt=$(this).parent(),
			val=prnt.find('input').val(),
			event_id=$('input[name=ID]').val();

		if(val==''){
			alert('Укажите название галлереи');
			return false;
		}else{
			$.post('/bitrix/admin/estelife_ajax.php',{
				'action':'add_gallery',
				'name':val,
				'event_id':event_id
			},function(r){
				if('item' in r){
					var tpl=$('.gallery-item-template').clone(true).removeClass('gallery-item-template');
					tpl.find('h2').html(val);
					tpl.attr('data-id', r.item);
					$('.gallery-list').prepend(tpl);

					dropUploader(tpl.find('.gallery-item-photos'),'/bitrix/admin/estelife_ajax.php',{
						'field':'photo',
						'action':'upload_photo',
						'gallery_id':r.item,
						'callbacks':new dropCallbacks()
					});

					prnt.find('input').val('')
				}
			},'json');
		}

		return false;
	});

	bd.on('click','a.gallery-delete',function(){
		var gl=$(this).parent(),
			id=gl.attr('data-id');

		$.post('/bitrix/admin/estelife_ajax.php',{
			'action':'delete_gallery',
			'id':id
		},function(r){
			if('complete' in r){
				gl.remove();
			}else{
				alert('Ошибка удаления галлереи')
			}
		},'json');

		return false;
	});
});

function dropCallbacks(){
	this.upload=function(r,e,f){
		if('error' in r){
			e.find('.drop-progress')
				.addClass('error')
				.css({
					'display':'block',
					'width':'100%'
				})
		}else if('photo' in r){
			e.find('.drop-item').attr('data-id', r.photo);
		}

		e.find('.drop-item').append('<a href="#" class="estelife-btn adm-btn adm-btn-delete estelife-delete photo-delete"></a>');
	}
}

function dropUploader(c,u,o){
	if(typeof c!='object' ||
		!(c instanceof jQuery))
		throw 'incorrect container object: use jQuery';

	var list=null,
		message=null,
		options={
			'cont':c,
			'url':u,
			'field': o.field||'file',
			'inited':false,
			'file_size':0,
			'callbacks': o.callbacks || {}
		},
		data={};

	if('field' in o)
		delete o.field;

	if('callbacks ' in o)
		delete o.callbacks;

	data=$.extend(data,o);

	function init(){
		message=$('<span class="drop-message"></span>');
		options.cont.append(message);

		if (typeof window.FileReader=='undefined') {
			message.text('Не поддерживается браузером!');
			options.cont.addClass('error');
			return;
		}else{
			message.text('Перетащите сюда Ваши фотки');
			options.inited=true;
		}

		options.cont[0].ondragover = function() {
			options.cont.addClass('hover');
			return false;
		};

		options.cont[0].ondragleave = function() {
			options.cont.removeClass('hover');
			return false;
		};

		options.cont[0].ondrop = function(event) {
			event.preventDefault();
			options.cont.removeClass('hover');
			options.cont.addClass('drop');

			upload(event);
		};

		var table=options.cont.find('.drop-table');
		list=table.find('.drop-list');

		if(table.length<=0){
			table=$('<table class="drop-table"><tbody></tbody></table>')
			list=$('<tr class="drop-list"></tr>');

			table.find('tbody').append(list);
			options.cont.append(table);
		}
	};

	function upload(event){
		var files = event.dataTransfer.files

		$.each(files, function(i, file) {
			if(/[а-я]/i.test(file.name)
				|| !file.type.match(/image.*/))
				return;

			if(options.file_size>0 &&
				file.size>options.file_size)
				return;

			var td=$('<td></td>')
				.appendTo(list)
				.append('<div class="drop-item">' +
					'<div class="drop-image"></div>' +
					'<div class="drop-name"></div>' +
					'<div class="drop-progress"></div></div>');

			var reader=new FileReader(),
				xhr=new XMLHttpRequest();

			xhr.upload.addEventListener("progress",progress,false);
			xhr.onreadystatechange=stateChange;

			reader.onload=function(e) {
				td.find('.drop-image').append('<img src="'+e.target.result+'" />');
				td.find('.drop-name').text(file.name);

				xhr.open("POST", options.url);
				xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");

				var form=new FormData();
				form.append(options.field,file);

				for(prop in data){
					form.append(prop,data[prop]);
				}

				if(xhr.sendAsBinary)
					xhr.sendAsBinary(form);
				else
					xhr.send(form);

				xhr.drop={
					'element':td,
					'file':file
				};
				xhr.upload.drop=xhr.drop;
			};

			reader.readAsDataURL(file);
		});
	};

	function progress(e) {
		if('drop' in e.target){
			var percent=(e.loaded * 100) / e.total,
				progress=e.target.drop.element.find('.drop-progress');

			progress.show();
			progress.css('width',percent+'%');

			if(percent>=100)
				progress.hide();
		}
	};

	function stateChange(e) {
		if(e.target.readyState==4){
			if(e.target.status==200){
				message.text('Загрузка успешно завершена!');
				options.cont.removeClass('drop');

				if('callbacks' in options &&
					'upload' in options.callbacks){
					options.callbacks.upload(
						$.parseJSON(this.responseText),
						this.drop.element,
						this.drop.file
					);
				}
			}else{
				if('drop' in e.target){
					var progress=e.target.drop.element.find('.drop-progress');
					progress.cont
						.addClass('error')
						.css({
							'width':'100%',
							'display':'block'
						});
				}

				if('callbacks' in options &&
					'error' in options.callbacks){
					options.callback.error(e.target.status);
				}
			}
		}
	};

	init();
};
