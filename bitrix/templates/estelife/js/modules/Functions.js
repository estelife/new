define(['tpl/Template','modules/Select'],function(Template,Select){
	return {
		getPromotions:function(city){
			var tpl=new Template({
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
				if(r.list){
					tpl.ready(function(){
						tpl.set('list',r.list);
						var h = tpl.render();
						if (h.length>0){
							$('.promotions.announces h2').html(r.title.name);
							$('.promotions.announces .items').html(h);
							var th=$('.more_promotions');
							if (r.list.active==1){
								th.attr('href','/clinics/?city='+city).html('Больше клиник');
							}else{
								th.attr('href','/promotions/?city='+city).html('Больше акций');
							}
						}else{
							console.log('Ошибка получения html')
						}
					});
				}else{
					console.log('Ошибка получения городов')
				}
			},'json');
		},
		initFilter:function(form){
			this.initFormFields(form);

			$('body').on('change', 'select[data-rules]', function(){
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

								child.trigger('updateOptions');
							};
						})(temp[1]),
						'json'
					);
				}
			});

			var input=$('input[name=name]',form);
		},
		initFormFields:function(form){
			$('.text.date, .field.date',form).each(function(){
				var current=$(this),
					img=current.find('i'),
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

			Select.bindEvent('createItem',function(context,data){
				data.item
					.find('a')
					.attr('data-help',data.html);
			});
			$('select',form).each(function(){
				var sl=Select.make($(this));
			});

			$('input[type=checkbox], input[type=radio]',form).each(function(){
				var inpt=$(this),
					isRadio = inpt.attr('type') == 'radio',
					form = $(this.form),
					link = $('<a href="#"></a>'),
					id = inpt.attr('id'),
					label;

				if(id && id.length>0){
					label=form.find('label[for='+id+']');

					if(label.length>0){
						label.hide();
						label=label.html();
					}else
						label='';
				}else{
					label=inpt.attr('title')||'';
				}

				inpt.after(link).hide();
				link.html('<i></i>'+label);
				link.data('Input', inpt)
					.addClass('checkbox')
					.attr('data-name', inpt.attr('name'));

				if (isRadio)
					link.addClass('radio');

				if(inpt.is(':checked'))
					link.addClass('active');

				link.click(function(e){
					var link=$(this);

					if(link.hasClass('active') && !isRadio){
						link.removeClass('active');
						link.data('Input').prop('checked', false);
					}else{
						if (isRadio) {
							var name = link.attr('data-name'),
								active = $('.checkbox.radio[data-name='+name+']');

							active.each(function(){
								var link = $(this);
								link.removeClass('active');
								link.data('Input').prop('checked', false);
							});
						}

						link.addClass('active');
						link.data('Input').prop('checked', true);
					}

					e.preventDefault();
				});
			});

			$('input.phone').bind(
				'keydown input',
				this.inputPhoneEventCallback
			);
		},

		// Используется в сочетании с событиями keydown input
		inputPhoneEventCallback: function(e){
			var code = EL.keyCode(e),
				inpt = $(e.target),
				origin = inpt.val(),
				val = origin.replace(/[^\d]+/gi,''),
				avay = $.inArray(code,[46,8,37,39,116,35,36]);

			if (code && (code < 48 || (code > 57 && code < 96) || code > 105)) {
				if (avay < 0)
					return false;
			}

			if (avay < 2) {
				var range = new EL.Range(inpt),
					pos = range.caretPosition() + 1,
					temp, temp_pos;

				if (avay < 0) {
					if (val.length >= 11)
						return false;

					if (pos > origin.length) {
						pos += 1;
						val += EL.fromCharCode(code);
					} else {
						temp = origin.split('');
						temp_pos = pos-1;
						var ln = origin.length;

						temp.splice(temp_pos, 0, EL.fromCharCode(code));
						val = temp.join('').replace(/[^\d]+/gi,'');
					}
				}else if($.trim(origin) != ''){
					pos -= 2;
					temp = origin.split('');
					temp_pos = pos;
					var chr = origin[temp_pos];

					if (!chr.match(/^[\d]$/))
						temp_pos -= 1;

					delete temp[temp_pos];
					origin = temp.join('');
					val = origin.replace(/[^\d\s]+/gi,'');
				}

				//if(e.type!='input' && val.length>10)
				//	val=val.slice(0,11);
				//else if(e.type=='input' && val.length>11)
				val = val.slice(0,11);

				var newVal = '';

				for (var i= 0, y=i; i<val.length; i++, y++) {
					newVal += val[i];

					if (i == 0) {
						newVal = '+'+newVal+'(';
						y+=2;
					}else if(i == 3){
						newVal += ')';
						y++;
					}else if(i == 6 || i == 8){
						newVal += '-';
						y++;
					}
					if ((i == 0 || i == 3 || i == 6 || i == 8) && pos == y)
						pos += 1;
				}

				inpt.val(newVal);
				range.setSelection(pos, pos);
				return false;
			}

			return true;
		}
	}
});