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
							$('.more_promotions').attr('href','/promotions/?city='+city);
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
			$('.text.date',form).each(function(){
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

			$('input[type=checkbox]',form).each(function(){
				var inpt=$(this),
					form=$(this.form),
					link=$('<a href="#"></a>'),
					id=inpt.attr('id'),
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
				link.data('Input',inpt).addClass('checkbox');

				if(inpt.is(':checked'))
					link.addClass('active');

				link.click(function(e){
					var link=$(this);

					if(link.hasClass('active')){
						link.removeClass('active');
						link.data('Input').prop('checked',false);
					}else{
						link.addClass('active');
						link.data('Input').prop('checked',true);
					}
					e.preventDefault();
				});
			});
		}
	}
});