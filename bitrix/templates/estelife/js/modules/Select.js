define(function(){
	var SelectFactory,
		Select;

	SelectFactory={
		selects:[],
		pull_remaked:[],
		register:function(select){
			if(typeof select=='object' && select instanceof Select)
				this.selects.push(select);
		},
		hide:function(){
			var select;
			for(var i=0; i<this.selects.length; i++){
				select=this.selects.pop();
				select.closeOptions();
			}
		},
		remake:function(){
			if(this.pull_remaked.length>0){
				$.map(this.pull_remaked,function(fn){
					fn();
				});
			}
			var ob=this;
			setTimeout(function(){
				ob.remake();
			},200);
		},
		make:function(je,nf){
			return new Select(je,nf);
		}
	};
	
	Select=function(je,nf){
		var jSelect=je,
			needFilter=nf||false,
			created,visible,select,value,
			list,arrow,startValue,keyupInit,
			current=this,
			events={};

		(function(){
			(function create(){
				if(!created){
					if(jSelect.length<=0)
						return;
					else if(jSelect.data('Select'))
						jSelect.data('Select').remove();
					else if(jSelect.next().hasClass('select')){
						jSelect.next().remove();
					}

					select=$('<div></div>').addClass('select');
					var jSelectClass=jSelect.attr('class'),
						selected=$('<div></div>').addClass('selected');

					value=(!needFilter) ?
						$('<a href="javascript:void(0)"><span></span></a>').addClass('value') :
						$('<input type="text" />');

					if(jSelectClass!='')
						select.addClass(jSelectClass);

					arrow=$('<a href="javascript:void(0)"></a>').addClass('arrow');
					list=$('<div class="items"></div>');

					select.get(0).currrentSelectbox=jSelect;
					select.append(selected.append(value).append(arrow)).append(list);

					if(!needFilter)
						value=value.find('span');

					list.on('click','.item:not(.group)',function(e){
						e.stopPropagation();
						selectOption($(this));
						endFilter();
						return false;
					});

					$('.arrow,.value',select).click(function(e){
						e.stopPropagation();

						if(list.is(':hidden')){
							openOptions();
						}else if(!$(this).hasClass('value')){
							closeOptions();
							endFilter(true)
						}

						if(needFilter)
							startFilter();

						return false;
					});

					jSelect.css('display','none');
					jSelect.after(select);

					if('jScrollPane' in jQuery.fn){
						list.jScrollPane({
							hideFocus:true,
							verticalDragMaxHeight:100,
							verticalDragMinHeight:50,
							autoReinitialise:true,
							autoReinitialiseDelay:200,
							verticalGutter:0,
							mouseWheelSpeed:30
						});
					}

					makeOptions();

					$(document).click(function(){
						closeOptions();
						endFilter(true);
					});

					created=true;
					visible=true;
					jSelect.data('Select',current);

					SelectFactory.pull_remaked.push((function(){
						return remake;
					})());
				}
			})();

			function openOptions(){
				SelectFactory.hide();
				SelectFactory.register(current);
				list.css('zIndex',101).fadeIn(100);
				arrow.addClass('active')
			}

			function closeOptions(){
				list.css('zIndex',99).fadeOut(100);
				arrow.removeClass('active')
			}

			function selectOption(item){
				list.find('em.active').removeClass('active');
				item.addClass('active');
				jSelect.val(item.data('option').val());
				jSelect.change();

				var opt=item.find('a'),
					s=opt.html();

				if(!needFilter){
					value.attr({
						'data-value':opt.attr('data-value'),
						'class':opt.attr('class')||''
					})
						.html(s)
				}else
					value.val(s);

				startValue=s;
				closeOptions();
			}

			function startFilter(){
				if(!startValue)
					startValue=value.val();

				value.val('').focus();
				list.find('em').show();

				if(!('keyupInit' in this)){
					this.value.keyup(function(){
						var reg=new RegExp('('+value.toLowerCase()+')'),
							link=null,
							li=null;

						list.find('em').each(function(){
							li=$(this);
							link=li.find('a');

							if(link.length<=0)
								return;

							if(!reg.test(li.find('a').html().toLowerCase()))
								li.hide();
							else
								li.show();
						})
					});
					keyupInit=true
				}
			}

			function endFilter(returnStartValue){
				value.unbind('keyup');

				if(returnStartValue && startValue)
					value.val(startValue);
			}

			function remake(){
				var options=jSelect.find('option'),
					items=list.find('.item a'),
					needUpdate=false;

				if(options.length==items.length){
					for(var i=0; i<options.length;i++){
						if(options.eq(i).attr('value')!=items.eq(i).attr('data-value')){
							needUpdate=true;
							break;
						}
					}
				}else{
					needUpdate=true;
				}

				if(!needUpdate)
					return;

				list.find('.item').remove();
				makeOptions();
			}

			function parseOptHtml(html){
				return (html) ?
					html.replace(/\[(\/)?([\w\d\s=\"\'\/]+)\]/gi,'<$1$2>') :
					'';
			}

			function makeOptions(){
				var options=jSelect.find('option'),
					index=jSelect.get(0).selectedIndex,
					currentOption=options.eq(index),
					lastGroup='',
					height=0,
					curGroup,optPrnt,item,link,isPane,
					prnt=list.find('.jspPane'),
					optionValue;

				if(prnt.length>0){
					isPane=true;
					list.css({
						'visibility':'hidden',
						'display':'block'
					});
				}else{
					prnt=list;
				}

				if(!needFilter){
					optionValue=currentOption.attr('value');
					value.attr({
						'data-value':optionValue
					});

					if(optionValue!='' && optionValue!=0)
						value.addClass('has-value v'+optionValue);

					value.html(parseOptHtml(currentOption.html()));
				}else
					value.val(parseOptHtml(currentOption.html()));

				for(var i=0; i<options.length; i++){
					currentOption=options.eq(i);
					optPrnt=currentOption.parent();

					if(optPrnt.get(0).tagName=='OPTGROUP'){
						curGroup=optPrnt.attr('label');

						if(curGroup!=lastGroup){
							lastGroup=curGroup;
							prnt.append('<em class="group">'+curGroup+'</em>');
						}
					}

					optionValue=currentOption.attr('value');
					item=$('<em></em>').addClass('item');
					link=$('<a></a>')
						.attr({
							'data-value':optionValue,
							'href':'javascript:void(0)'
						})
						.html(parseOptHtml(currentOption.html()));

					if(optionValue!='' && optionValue!=0)
						link.addClass('has-value v'+optionValue);

					if(index==i)
						item.addClass('active');

					item.data('option',currentOption);
					prnt.append(item.append(link));

					if(i<10 && isPane)
						height+=item.height();
				}

				if(isPane){
					list.css({
						'visibility':'visible',
						'display':'none'
					});
				}

				if(isPane && height>0)
					list.height(height)
			}

			current.closeOptions=function(){
				closeOptions();
			}
		})();

		this.hide=function(){
			if(visible){
				select.fadeOut(100);
				visible=false;
			}
		};

		this.show=function(){
			if(!visible){
				select.fadeIn(100);
				visible=true;
			}
		};

		this.remove=function(){
			select.remove();
			jSelect.data('Select',null);
		};

		this.set=function(val){
			list.find('.item').each(function(i){
				var item=$(this);
				if(item.data('option').val()==val)
					item.click();
			});
		};

		this.selectedIndex=function(index){
			index=parseInt(index);
			if(!isNaN(index))
				list.find('.item').eq(index).click();
		};

		this.selected=function(){
			return jSelect.find('option:selected').attr('value');
		};

		this.on=function(event,callback){
			if(typeof callback=='function')
				events[event]=callback;
		}
	};

	setTimeout(function(){
		SelectFactory.remake()
	},200);

	return SelectFactory;
});