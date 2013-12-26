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

	this.bigLoader=function(){
		return {
			'create':function(){
				var loader=$('.el-big-loader');

				if(loader.length<1){
					loader=$('<div></div>').addClass('el-big-loader');
					$('body').append(loader);
				}

				loader.stop().fadeTo(200,0.9);
			},
			'destroy':function(){
				$('.el-big-loader').stop().fadeTo(200,0);
			}
		}
	};

	this.smallLoader=function(){
		return {
			'create':function(el){
				var find=-1,
					tag=el[0].tagName.toLowerCase();

				if((find=$.inArray(tag,['select','input','textarea']))>-1){
					if(find==0){
						var temp=el.next();
						if(temp.hasClass('select'))
							el=temp;
					}

					var loader=$('.el-small-loader');

					if(loader.length<1){
						loader=$('<div></div>').addClass('el-small-loader');
						$('body').append(loader);
					}else{
						loader.show();
					}

					loader.css({
						'position':'absolute',
						'top':el.offset().top+4,
						'left':el.offset().left+el.width()-(find==0 ? 50 : 12)
					});
				}
			},
			'destroy':function(){
				$('.el-small-loader').hide();
			}
		}
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

	this.goto=function(el){
		if(el.length>0){
			var target=(this.browser().webkit) ? $('body') : $('html'),
				top=el.offset().top,
				scrl=target.scrollTop();

			if(scrl>top){
				target.animate({'scrollTop':top+'px'},200);
			}
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
					query=query[1].split('&');

					for(var i=0; i<query.length; i++){
						temp=query[i].split('=');

						if(temp.length==1)
							temp[1]='';

						toObject[temp[0]]=decodeURIComponent(temp[1]);
					}
				}

				return toObject;
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

	this.notice=new function(){
		var notice=null,
			type=null,
			message=null,
			timeout=null;

		$(function(){
			if(!notice){
				notice=$('<div class="notice"></div>');
				type=$('<span></span>');
				message=$('<div></div>');
				notice.append($('<div class="message"></div>').append(type,message));
				$('body').append(notice);

				$('.notifies').each(function(){
					var nf=$(this),
						mess=nf.html();
					(nf.hasClass('er')) ?
						error(mess) :
						complete(mess);
					nf.remove()
				});
			}
		});

		function complete(m){
			type.html('Успешно выполнено');
			notice.removeClass('error');
			message.html(m);
			show();
		}

		function error(m){
			type.html('Произошла ошибка');
			notice.addClass('error');
			message.html(m);
			show();
		}

		function show(){
			if(timeout){
				clearTimeout(timeout);
				timeout=null;
			}

			notice.show();
			notice.animate({
				'width':'250px'
			},100);
			timeout=setTimeout(function(){
				hide();
			},5000);
		};

		function hide(){
			notice.animate({
				'width':0
			},100,'swing',function(){
				notice.hide();
			});
		};

		this.complete=complete;
		this.error=error;
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
var EL=new Estelife({
	'path':'/bitrix/templates/estelife/js'
});