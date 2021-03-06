/**
 * Created with JetBrains PhpStorm.
 * User: dmitriy
 * Date: 28.10.13
 * Time: 11:32
 * To change this template use File | Settings | File Templates.
 */
var Estelife=function(s){
	var browser=null,
		loaded=[],
		settings={
			'path':(typeof s=='object' && 'path' in s) ?
				s.path : '/'
		},
		moduleEvents={};

	(function init(){
		// Array Remove - By John Resig (MIT Licensed)
		Array.prototype.remove = function(from, to) {
			var rest = this.slice((to || from) + 1 || this.length);
			this.length = from < 0 ? this.length + from : from;
			return this.push.apply(this, rest);
		};

		// Find in Array - By Kevin van Zonneveld (http://kevin.vanzonneveld.net)
		Array.prototype.inArray=function(needle,strict) {
			var key,found = -1,
				haystack=this;
			strict = !!strict;

			for (key in haystack) {
				if ((strict && haystack[key] === needle) || (!strict && haystack[key] == needle)) {
					found=key;
					break;
				}
			}

			return found;
		};

		String.prototype.addslashes=function(){
			return (this+'').replace(/([^\w\d\s])/ig,'\\$1');
		};
	})();

	this.loadModule=function(name,callback){
		return (function(){
			try{
				var interval;

				if(typeof name=='object' &&
					name instanceof Array){
					for(var i=0; i<name.length; i++){
						$.getScript(
							settings.path+'/modules/'+name[i]+'.js',
							(function(n){
								return function(data,textStatus,jqxhr){
									if(jqxhr.status==200 && (n) in EL)
										loaded.push(n);
									else{
										clearInterval(interval);
										throw 'estelife module load error: '+n;
									}
								}
							})(name[i])
						)
					}
					interval=setInterval(function(){
						var l=0;

						for(var i=0; i<name.length; i++){
							if($.inArray(name[i],loaded)>-1)
								l++;
						}

						if(l>=name.length){
							clearInterval(interval);
							callback();
						}
					},200);
				}else{
					if($.inArray(name,loaded)>-1){
						callback();
					}else{
						$.getScript(
							settings.path+'/modules/'+name+'.js',
							function(data,textStatus,jqxhr){
								if(jqxhr.status!=200){
									throw 'estelife module not found: '+name;
								}else if(!(name) in EL){
									throw 'estelife module not register in estelife manager: '+name;
								};

								loaded.push(name);
								callback();
							}
						)
					}
				}
			}catch(e){
				if('console' in window){
					console.log(e);
				}else{
					alert(e);
				}
			}
		})();
	};

	this.formBlock=function(){
		return {
			'create':function(el){
				var tagName=el[0].tagName.toLowerCase();

				if(tagName!='form'){
					el=el.parents('form:first');
					if(el.length<=0)
						return;
				}

				var block=$('.el-form-block');

				if(block.length<1){
					block=$('<div></div>').addClass('el-form-block');
				}else{
					block.show();
				}

				el.append(block).css('position','relative');
			},
			'destroy':function(el){
				$('.el-form-block').hide();
			}
		}
	};

	this.goto=function(toElement, fromAllPosition, noAnimated){
		var target=(this.browser().webkit) ? $('body') : $('html');

		if(toElement && toElement.length>0){
			var top=toElement.offset().top - 10;
			target.scrollTop(top);
		}else{
			(!noAnimated) ?
				target.animate({'scrollTop':'0px'},200) :
				target.scrollTop(0);
		}
	};

	this.browser=function(){
		if(!browser){
			browser={};

			if(!jQuery.browser){
				var ua=navigator.userAgent.toLowerCase();

				var match = /(chrome)[ \/]([\w.]+)/.exec( ua ) ||
					/(webkit)[ \/]([\w.]+)/.exec( ua ) ||
					/(opera)(?:.*version|)[ \/]([\w.]+)/.exec( ua ) ||
					/(msie) ([\w.]+)/.exec( ua ) ||
					ua.indexOf("compatible") < 0 && /(mozilla)(?:.*? rv:([\w.]+)|)/.exec( ua ) ||
					[];

				var matched={
					browser: match[1] || "",
					version: match[2] || "0"
				};

				if(matched.browser){
					browser[matched.browser]=true;
					browser.version=matched.version;
				}

				if(browser.chrome){
					browser.webkit=true;
				}else if(browser.webkit){
					browser.safari=true;
				}
			}else{
				browser=jQuery.browser;
			}

			browser.addFavorite=function(a){
				var title=document.title,
					url=document.location;

				try{
					window.external.AddFavorite(url, title);
				}catch (e){
					try{
						window.sidebar.addPanel(title, url, "");
					}catch (e){
						if (typeof(opera)=="object") {
							a.rel="sidebar";
							a.title=title;
							a.url=url;
							return true;
						}else {
							alert('Нажмите Ctrl-D чтобы добавить страницу в закладки');
						}
					}
				}

				return false;
			}
		}

		return browser;
	};

	this.storage=function(){
		return {
			setItem:function(key,item){
				if(this.supports()){
					localStorage.setItem(
						key,
						(typeof item=='object' ? JSON.stringify(item) : item)
					);
				}
			},
			getItem:function(key,is_object){
				var item=null;

				if(this.supports() && (item=localStorage.getItem(key))){
					item=(is_object) ?
						JSON.parse(item) : item;
				}

				return item;
			},
			removeItem:function(key){
				if(this.supported())
					localStorage.removeItem(key);
			},
			supports:function(){
				try {
					return 'localStorage' in window && window['localStorage'] !== null;
				} catch (e) {
					return false;
				}
			}
		};
	};

	this.spellAmount=function(val, sEnds){
		var arEnds={
			'1':'',
			'2-5':'а',
			'def':'ов'
		};

		if(typeof(sEnds)=='string')
		{
			var sEndsTmp=sEnds.split(',');
			arEnds={
				'1':sEndsTmp[0],
				'2-5':sEndsTmp[1],
				'def':sEndsTmp[2]
			};
		}
		if(val>1000000) val=val%1000000;
		if(val>100000) val=val%100000;
		if(val>10000) val=val%10000;
		if(val>1000) val=val%1000;
		if(val>100) val=val%100;
		if(val==0) return arEnds['def'];
		if(val==1) return arEnds['1'];
		if(val<20)
		{
			if(val<5) return arEnds['2-5'];
			else return arEnds['def'];
		}
		else
		{
			var minor=val%10;
			if(minor==1) return arEnds['1'];
			if(minor==0) return arEnds['def'];
			if(minor<5) return arEnds['2-5'];
		}
		return arEnds['def'];

	}

	this.geolocation = function(success, error){
		if (!success || typeof success != 'function'){
			throw 'Success in not function in geolocation';
		}
		if (error && typeof error!='function'){
			throw 'Error in not function in geolocation';
		}
		if (navigator.geolocation){
			navigator.geolocation.getCurrentPosition(success, error);
		}else{
			error();
		}
	};

	this.query=function(url){
		var current=url||location.href,
			query=current.split('?');

		query=query[1]||'';

		return {
			toObject:function(){
				var toObject={};

				if(query.length>0){
					var temp=null;
					query=query.split('&');

					for(var i=0; i<query.length; i++){
						temp=query[i].split('=');

						if(temp.length==1)
							temp[1]='';

						toObject[temp[0]]=decodeURIComponent(temp[1]);
					}
				}

				return toObject;
			},
			setParam:function(field,value){

				var temp=this.toObject();
				temp[field]=value;
				query=this.toString(temp).substr(1);
				return this;
			},
			toString:function(q){
				var temp=query;

				if(typeof q=='object'){
					temp=[];
					for(key in q)
						temp.push(key+'='+encodeURIComponent(q[key]));
					temp=temp.join('&')
				}

				return (temp.length>0) ? '?'+temp : '';
			}
		}
	};

	this.cookie={
		_get:function (name) {
			var matches = document.cookie.match(new RegExp(
				"(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
			));
			return matches ? decodeURIComponent(matches[1]) : null;
		},

		_set:function (name, value, expires, path, domain, secure) {
			// Send a cookie
			// original by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)

			expires instanceof Date ?
				expires = expires.toGMTString() :
				typeof(expires) == 'number' && (expires = (new Date(+(new Date) + expires * 1e3)).toGMTString());

			var r = [name + "=" + encodeURIComponent(value)], s, i;

			for(i in s = {expires: expires, path: path, domain: domain}){
				s[i] && r.push(i + "=" + s[i]);
			}

			return secure && r.push("secure"), document.cookie = r.join(";"), true;
		},

		_del:function (name) {
			setCookie(name, null, -1)
		}
	};

	this.Range=function(field){
		if(typeof field!='object' || !(field instanceof jQuery) || field.length<=0)
			throw 'incorrect field for EL.range';

		var ff=field.get(0);

		this.caretPosition = function() {
			ff.focus();

			if(ff.selectionStart)
				return ff.selectionStart;
			else if(document.selection){
				var sel = document.selection.createRange();
				var clone = sel.duplicate();
				sel.collapse(true);
				clone.moveToElementText(ff);
				clone.setEndPoint('EndToEnd', sel);
				return clone.text.length;
			}

			return 0;
		};

		this.setSelection = function(start, end) {
			if(ff.selectionStart){
				ff.setSelectionRange(start,end);
				ff.focus();
			}else if (ff.createTextRange){
				var r=ff.createTextRange();
				r.moveStart('character',start);
				r.moveEnd('character',end);
				r.select();
			}
		};
	};

	this.keyCode=function(e,code){
		var keyCode=(window.event) ? window.event.keyCode : e.which;
		return (!code) ? keyCode : (code==keyCode);
	};

	this.fromCharCode = function(code) {
		var codes = [48,49,50,51,52,53,54,55,56,57,65,66,67,68,69,70,71,72,73,74,75,76,77,78,79,80,81,82,83,84,85,86,87,88,89,90,96,97,98,99,100,101,102,103,104,105,106,107,109,110,111,186,187,188,189,190,191,192,219,220,221,222],
			values = [0,1,2,3,4,5,6,7,8,9,'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',0,1,2,3,4,5,6,7,8,9,'*','+','-','.','/',';','=',',','-','.','/','~','[','\\',']','\''],
			key = null;

		key = codes.inArray(code);

		if (key < 0 || key >= values.length)
			return null;

		return values[key];
	};
};
Estelife.prototype.profile=function(t){
	var title=t||'profile:',
		start=0;

	return {
		start:function(){
			start=(new Date()).getTime();
			return this;
		},
		end:function(t){
			var result=(new Date()).getTime()-start;

			if(t=='sec')
				result/=1000;

			if(window.console)
				console.log(title+' '+result);
			else
				alert(title+' '+result);
		}
	}
};
Estelife.prototype.SystemSettings=(function(){
	var settings;

	(function init(){
		$.getJSON('/api/estelife_ajax.php',{
			'action':'get_system_settings'
		},function(r){
			settings=(typeof r=='object') ? r : {};
		});
	})();

	function ready(callback){
		if(settings){
			if(callback && typeof callback=='function')
				callback(settings);
		}else
			setTimeout(ready,100);
	}

	return {
		ready:ready
	}
})();
Estelife.prototype.help=function(fromTop){
	var help,endTop,startTop,
		hideTimeout,showTimeout,
		marginTop=25,
		event;

	(function init(){
		help=$('<div></div>').addClass('help');
		help.html('<span></span><i></i>');

		$('body').append(help);

		if(fromTop)
			help.addClass('top-orient');

		$(document).mousemove(function(e){
	event = window.event;
		}).click(function(){
			_hide();
		});
	})();

	function _getMousePosition(){
		if (event.pageX == null && event.clientX != null ) {
			var html = document.documentElement,
				body = document.body;



			event.pageX = event.clientX + (html && html.scrollLeft || body && body.scrollLeft || 0) - (html.clientLeft || 0)
			event.pageY = event.clientY + (html && html.scrollTop || body && body.scrollTop || 0) - (html.clientTop || 0)
		}

		return {
			x:event.pageX,
			y:event.pageY
		}
	}

	function _show(text){
		help.find('span')
			.text(text);
		help.css({
			'display':'block',
			'visibility':'hidden'
		});

		var helpWidth=help.outerWidth(),
			helpHeight=help.outerHeight();

		help.css({
			'display':'none',
			'visibility':'visible'
		});

		var mousePosition=_getMousePosition(),
			left=mousePosition.x-helpWidth/ 2,
			right=left+helpWidth,
			winWidth=$(window).width();

		if(right>=winWidth){
			left=left-(right-winWidth-marginTop);
		}else if(left<marginTop){
			left=marginTop;
		}

		if(fromTop){
			startTop=(mousePosition.y-helpHeight*2)-marginTop;
			endTop=startTop+helpHeight;
		}else{
			endTop=mousePosition.y+marginTop;
			startTop=endTop+helpHeight;
		}

		help.css({
			'left':left+'px',
			'top':startTop+'px',
			'opacity':1
		}).show();
		help.stop().animate({
			'opacity':1,
			'top':endTop+'px'
		},150);
	}

	function _hide(){
		if(showTimeout){
			clearTimeout(showTimeout);
			return;
		}

		hideTimeout=null;

		help.stop().animate({
			'opacity':0,
			'top':startTop+'px'
		},80,'swing',function(){
			help.hide();
		});
	}

	this.show=function(text){
		if(hideTimeout){
			clearTimeout(hideTimeout);
			return;
		}

		showTimeout=setTimeout(function(){
			showTimeout=null;
			_show(text);
		},500);
	};

	this.hide=function(){
		_hide();
	};
};

Estelife.prototype.helpMaker=function(elements){
	if(!elements || !(elements instanceof jQuery) || elements.length==0)
		return null;

	var helpObject;

	if(!this.helpObject){
		this.helpObject=new this.help();
		helpObject=this.helpObject;
	}else
		helpObject=this.helpObject;

	elements.on('mouseover mouseout',function(event){
		var element=$(this),
			text;

		if(!element.data('help-text')){
			text=element.attr('data-help')||element.attr('title');

			if(!text || text.length<=0)
				return;

			element.attr('data-help',text);
			element.removeAttr('title');
			element.data('help-text',text);
		}else{
			text=element.data('help-text');
		}

		event.type=='mouseout' ?
			helpObject.hide() :
			helpObject.show(text);
	});
};

Estelife.prototype.notice=function(){
	if(!this.noticeElement){
		this.noticeElement=$('<div class="notice"><a href="#" class="close">Закрыть</a><div class="notice-message"></div></div>');
		this.noticeElement.click(function(){
			return false;
		});
		$('body').append(this.noticeElement);
		$(document).click(function(){
			_hide();
		});
		this.noticeElement.find('a').click(function(e){
			_hide();
			e.preventDefault();
		});
	}

	var notice=this.noticeElement,
		message=notice.find('.notice-message'),
		title, src;

	(function init(){
		var notices=$('.notices .item');
		if(notices.length>0){
			var items=[];
			notices.each(function(){
				items.push($(this).html());
			});
			_show(items);
			$('.notices').remove();
		}
	})();

	function _show(items){
		if(!items)
			throw 'empty items for notice';

		message.empty();

		if(items instanceof Array){
			$.map(items,function(item){
				message.append('<div class="notice-item">'+item+'</div>');
			});
		}else if (items instanceof Object){
			if (items.attr('alt') != undefined)
				title = items.attr('alt');

			if (items.attr('src').length <= 0)
				throw 'empty image item for notice';
			else
				src = items.attr('src');

			message.append('<div class="notice-title">'+title+'</div><div class="notice-item"><img src="'+src+'" alt="'+title+'" ></div>');
		}else{
			message.append('<div class="notice-item">'+items+'</div>');
		}

		notice.css({
			display:'block',
			visibility:'hidden'
		});

		var height=notice.outerHeight();
		notice.css('margin-top','-'+(height/2)+'px');

		notice.css({
			display:'none',
			visibility:'visible'
		});

		notice.show();
		$('.wrap').addClass('blur');
	}

	function _hide(){
		notice.hide();
		$('.wrap').removeClass('blur');
	}

	return {
		hide:_hide,
		show:_show
	}
};

Estelife.prototype.touchEvent = (function(){
	function _getMousePosition(event){
		if(event.type == 'touchstart' || event.type == 'touchend'){
			var touch = event.originalEvent.touches[0] || event.originalEvent.changedTouches[0];
			event.pageX = touch.pageX;
			event.pageY = touch.pageY;
		} else if (event.pageX == null && event.clientX != null ) {
			var html = document.documentElement,
				body = document.body;

			event.pageX = event.clientX + (html && html.scrollLeft || body && body.scrollLeft || 0) - (html.clientLeft || 0)
			event.pageY = event.clientY + (html && html.scrollTop || body && body.scrollTop || 0) - (html.clientTop || 0)
		}

		return {
			x:event.pageX,
			y:event.pageY
		}
	}

	function _validateEvent(event) {
		if(event.type){
			var target = $(event.currentTarget),
				mouseCoords = _getMousePosition(event),
				deal = 500;

			switch(event.type) {
				case 'click':
					return true;
					break;
				case 'touchstart':
					$('[data-touched]').removeAttr('data-touched');

					target.attr({
						'data-touched':(new Date().getTime()),
						'data-touch-x':mouseCoords.x,
						'data-touch-y':mouseCoords.y
					});
					break;
				case 'touchend':
					if(target.is('[data-touched]')){
						var now = (new Date().getTime()),
							t = target.attr('data-touched'),
							x = target.attr('data-touch-x'),
							y = target.attr('data-touch-y');

						if((now - t) > deal)
							return false;

						$('[data-touched]').removeAttr('data-touched')
							.removeAttr('data-touch-x')
							.removeAttr('data-touch-y');

						if(x == mouseCoords.x && y == mouseCoords.y)
							return true;
					}
					break;
			}
		}

		return false;
	}

	$(document).bind('touchend', function(){
		$('[data-touched]').removeAttr('data-touched');
	});

	return {
		eventTrigger: 'click touchstart touchend',
		callback: function(callback){
			return function(event){
				if(_validateEvent(event))
					callback(event, $(event.currentTarget));
			};
		}
	}
})();

Estelife.prototype.Form = function(f) {
	if (_.isString(f))
		form = $(f);
	else if(!_.isObject(f) || !(f instanceof jQuery))
		throw 'Incorrect form element';

	if (!f.length)
		throw 'Form element not found in page';

	var form = f,
		afterSend,
		beforeSend;

	this.getData = function() {
		var data={},
			keys={};

		form.find('input,select,textarea').each(function(){
			var inpt=$(this),
				type=inpt.attr('type') || 'textarea',
				name=inpt.attr('name'),
				val='';

			if(['text', 'select', 'hidden', 'textarea'].inArray(type) > -1){
				val=inpt.val();
			}else if(['checkbox','radio'].inArray(type) > -1 && inpt.prop('checked')){
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

		return data;
	};

	this.sendData = function(params) {
		var def = {
			method: form.attr('method'),
			action: form.attr('action')
		};
		params = $.extend(def, params);
		var formData = this.getData();

		if (beforeSend && !beforeSend(formData, params))
			return;

		$.ajax({
			cache: false,
			data: formData,
			dataType: 'json',
			type: params.method.toLowerCase(),
			url: params.action,
			success: function(data) {
				afterSend && afterSend(data);
			}
		});
	};

	this.registerBeforeSend = function(callback) {
		if (callback && typeof callback=='function')
			beforeSend = callback;
	};

	this.registerAfterSend = function(callback) {
		if (callback && typeof callback=='function')
			afterSend = callback;
	};

	this.getTarget = function() {
		return form;
	}
};

Estelife.prototype.loader = (function() {
	var loader,
		percentStarted;

	function _create() {
		if (!loader) {
			loader = $('<div class="loader"></div>');
			$('body').append(loader);
		}
	}

	return {
		startWithPercent: function() {
			_create();
			loader.width(0).show();
			percentStarted = true;
		},
		setPercent:function(percent) {
			if (!percentStarted)
				return;

			var windowWidth = $(window).width();
			percent = parseFloat(percent);

			if (isNaN(percent) || percent < 0)
				percent = 0;
			else if (percent > 100)
				percent = 100;

			var percentWidth = windowWidth * (percent / 100);
			loader.stop().animate({width: percentWidth + 'px'}, 200, 'swing', function(){
				if (percent >= 100) {
					percentStarted = false;
					setTimeout(function(){
						loader.hide();
					}, 100)
				}
			});
		},
		start: function() {
			_create();
			loader.width(0)
				.show()
				.animate({width: $(window).width() + 'px'}, 500, 'swing', function(){
					loader.hide();
				});
		}
	};
})();

var EL=new Estelife({
	'path':'/bitrix/templates/estelife/js'
});