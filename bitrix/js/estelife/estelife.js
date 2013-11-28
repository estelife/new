$(function(){
	if(typeof VMap!='function')
		return;

	var bd=$('body');
	var map=new VMap();
	map.markers().icons({
		'default':'/bitrix/themes/.default/icons/estelife/point_green.png'
	});
	$('#map').show(0,function(){
		var lat=$('input[name=latitude]').val(),
			lng=$('input[name=longitude]').val();

		if(lat!='' && lng!=''){

			map.create($(this),lat,lng);
			map.zoom(14);
			map.markers().clear();
			map.markers().add(new map.marker(lat,lng));
			map.markers().draw();
		}else{
			map.create($(this),59.9395237,30.312020599999983);
			map.zoom(10);
		}

		google.maps.event.addListener(map.map(),'dblclick',function(e){
			var lat=e.latLng.lb,
				lng=e.latLng.mb;
			map.geocode(e.latLng,map.latlng);
		});
	});
	map.on('geocode_true',function(lat,lng,address){
		if(address){
			$.get('/bitrix/admin/estelife_ajax.php',{
				'action':'get_cities',
				'term':address.city
			},function(r){
				if('list' in r && r.list.length>0){
					var city=r.list.shift();
					$('input[name=city_id]').val(city.id);
					$('input[name=city_name]').val(city.name);
					$('input[name=metro_name]').prop('readonly',false);
					$('input[name=address]').prop('readonly',false).val(address.street+' '+address.house);

					map.center(lat,lng,14);
					map.markers().clear();
					map.markers().add(new map.marker(lat,lng));
					map.markers().draw();

					$('input[name=latitude]').val(lat);
					$('input[name=longitude]').val(lng);
				}else{
					alert('К сожалению, estelife не знает такого города.')
				}
			},'json');
		}else{
			map.center(lat,lng,14);
			map.markers().clear();
			map.markers().add(new map.marker(lat,lng));
			map.markers().draw();

			$('input[name=latitude]').val(lat);
			$('input[name=longitude]').val(lng);
		}
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

		if((index=$.inArray(action,['service','service_concreate']))>-1){
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
				(cls=='service' ?
					'get_service_concreate' :
					'set');

		if(action=='set'){
			var temp=[],
				active=$('.estelife-services .lists a.active'),
				sl=$('.estelife-services .selected');
			$.merge(active,lnk);

			active.each(function(){
				temp.push($(this).html())
			});

			var spec=active.eq(0).attr('data-id'),
				serv=active.eq(1).attr('data-id'),
				cserv=active.eq(2).attr('data-id');

			if(sl.find('[data-cservice='+cserv+']').length>0)
				return false;

			sl.show();
			sl.append('<li data-spec="'+spec+'" data-service="'+serv+'" data-cservice="'+cserv+'">'+temp.join(', ')+'<input type="hidden" name="services[]" value="'+cserv+'" /><a href="#">x</a></li>');
			lnk.addClass('active');
		}else{
			$.get('/bitrix/admin/estelife_ajax.php',{
				'action':action,
				'id':id
			},function(r){
				if('list' in r){
					var ul=prnt.next(),
						frm_prnt=forms.find('[data-action='+cls+']').next(),
						currents=[];

					ul.empty();
					if(cls=='specialization')
						ul.next().empty();

					if(cls=='service'){
						var sl=$('.estelife-services .selected');
						sl.find('li').each(function(){
							currents.push($(this).attr('data-cservice'))
						});
					}

					for(var i=0; i<r.list.length; i++){
						ul.append('<li><a href="#" data-id="'+ r.list[i].id+'">'+ r.list[i].name+'</a></li>');

						if($.inArray(r.list[i].id,currents)>-1)
							ul.find('li:last a').addClass('active');
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
				}else if('error' in r){
					alert(r.error)
				}
			},'json');
		}

		return false;
	});

	bd.on('click','.estelife-more',function(){
		var prnt=$(this).parents('tr:first'),
			cln=prnt.clone(true);
		cln.find('.estelife-more').remove();
		prnt.before(cln);
		return false;
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
	}).on('click','td',function(){
		var cur=$(this),
			prnt=cur.parent(),
			all_td=prnt.find('td'),
			active=prnt.find('.active');

		cur.removeClass('bh-active');

		if(cur.hasClass('active')){
			if(active.length==1){
				cur.removeClass('active').find('input').prop('checked',false);
			}else if(active.length>1){
				var currindex=all_td.index(cur)+1,
					maxindex=all_td.index(active.eq(active.length-1));

				for(var i=currindex; i<=maxindex; i++){
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

				if(activeindex<currindex){
					start=activeindex;
					end=currindex;
				}

				for(var i=start;i<=end;i++)
					all_td.eq(i).removeClass('bh-active').addClass('active').find('input').prop('checked',true);
			}
		}
	});

	bh.find('input:checked').parent().addClass('active');

	$('input[name=city_name],input[name=metro_name]').autocomplete({
		minLength:3,
		source: function(request,response) {
			var action='get_cities';

			if(this.element.attr('name')=='metro_name'){
				var city=parseInt($('input[name=city_id]').val());
				if(isNaN(city) || city<=0){
					alert('Выберите город')
					return false;
				}
				action='get_metros';
			}

			$.get('/bitrix/admin/estelife_ajax.php',{
				'action':action,
				'term':request.term,
				'city_id':city
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
			$('input[name='+$(this).attr('data-input')+']').val(ui.item.id)

			if($(this).attr('name')=='city_name'){
				$('input[name=metro_name]').prop('readonly',false);
				$('input[name=address]').prop('readonly',false);
			}
		}
	});

	$('input[name=address]').autocomplete({
		minLength:3,
		source: function(request,response) {
			var city=$('input[name=city_name]').val();

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
					response($.map(r.list, function(item) {
						return {
							label: item.name,
							value: item.name,
							address: item.address
						}
					}));
				}
			},'json');

			return true;
		},
		select:function(e, ui) {
			map.geocode(ui.item.value);
		}
	});
});
$(function(){
	$('select[name=specialization_id],select[name=find_specialization_id]').change(function(){
		var srv=$('select[name=service_id],select[name=find_service_id]');

		if(srv.length<=0)
			return;

		var id=$(this).val();
		srv.find('option').not(':first').remove();
		$.get('/bitrix/admin/estelife_ajax.php',{
			'action':'get_service',
			'id':id
		},function(r){
			if('list' in r){
				for(var i=0; i< r.list.length; i++){
					srv.append('<option value="'+ r.list[i].id+'">'+ r.list[i].name+'</option>')
				}
			}
		},'json');
	});
});