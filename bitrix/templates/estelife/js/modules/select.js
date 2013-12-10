var SELECTS_REGISTRY={
	aselects:new Array(),
	pull_remaked:[],
	register:function(oselect){
		if(typeof oselect=='object' && oselect instanceof EL.select)
			this.aselects.push(oselect);
	},
	hide:function(){
		for(var i=0; i<this.aselects.length; i++){
			select=this.aselects.pop();
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
	}
};

setTimeout(function(){
	SELECTS_REGISTRY.remake()
},200);

Estelife.prototype.select=function(jselect,need_filter){
	var jselect=jselect,
		created,visible,select,value,list,arrow,startValue,keyupInit,
		need_filter=need_filter||false,
		current=this,
		events={};

	(function(){
		(function create(){
			if(!created){
				if(jselect.length<=0)
					return;

				select=$('<div></div>').addClass('select');
				var jselect_class=jselect.attr('class'),
					selected=$('<div></div>').addClass('selected');

				value=(!need_filter) ?
					$('<a href="javascript:void(0)"><span></span></a>').addClass('value') :
					$('<input type="text" />');

				if(jselect_class!='')
					select.addClass(jselect_class);

				arrow=$('<a href="javascript:void(0)"></a>').addClass('arrow');
				list=$('<div class="items"></div>');

				select.get(0).currrentSelectbox=jselect;
				select.append(selected.append(value).append(arrow)).append(list);

				if(!need_filter)
					value=value.find('span');

				makeOptions();

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

					if(need_filter)
						startFilter();

					return false;
				});

				jselect.css('display','none');
				jselect.after(select);

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

				$(document).click(function(){
					closeOptions();
					endFilter(true);
				});

				created=true;
				visible=true;
				jselect.data('vapi_select',current);

				SELECTS_REGISTRY.pull_remaked.push((function(){
					return remake;
				})());
			}
		})();

		function openOptions(){
			SELECTS_REGISTRY.hide();
			SELECTS_REGISTRY.register(current);
			list.css('zIndex',101).fadeIn(100);
			arrow.addClass('active')
		};

		function closeOptions(){
			list.css('zIndex',99).fadeOut(100);
			arrow.removeClass('active')
		}

		function selectOption(item){
			list.find('em.active').removeClass('active');
			item.addClass('active');
			jselect.val(item.data('option').val());
			jselect.change();

			var opt=item.find('a'),
				s=opt.html();

			if(!need_filter){
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
			var options=jselect.find('option'),
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
			var options=jselect.find('option'),
				index=jselect.get(0).selectedIndex,
				currentOption=options.eq(index),
				lastGroup='',
				height=0,
				curGroup,optPrnt,item,link,
				prnt=list.find('.jspPane'),
				optionValue;

			if(prnt.length<=0)
				prnt=list;
			else{
				list.css({
					'visibility':'hidden',
					'display':'block'
				});
			}

			if(!need_filter){
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

				if(i<10 && prnt.hasClass('jspPane')){
					height+=item.height();
				}
			}

			if(prnt.hasClass('jspPane')){
				list.css({
					'visibility':'visible',
					'display':'none'
				});
			}

			if(height>0)
				prnt.parent().height(height);
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
		return jselect.find('option:selected').attr('value');
	};

	this.on=function(event,callback){
		if(typeof callback=='function')
			events[event]=callback;
	}
};