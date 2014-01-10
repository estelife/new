define(['tpl/Rendering'],function(Rendering){
	return function(s){
		var settings=s||{},
			html,gen,timer,
			data={},
			readyTimeouts=0;

		(function init(){
			if(!('params' in settings) ||
				typeof settings.params!='object')
				settings.params={};

			settings.params= $.extend(settings.params,{
				maxReadyTimeouts:100
			});

			if(!'template' in settings){
				if(timer)
					clearTimeout(timer);

				if('console' in window)
					console.error('template not set in settings');

				return;
			}

			if(typeof t=='object'){
				if(!(t instanceof jQuery)){
					if(timer)
						clearTimeout(timer);

					if('console' in window)
						console.error('incorrect template object: use jQuery object or string of template name');

					return;
				}

				html=t.prop('outerHTML');
				gen=new Rendering(
					html,
					settings.template
				);
			} else {
				checkLocalTemplate(function(response,from_server){
					if(!('template' in response) ||
						!response.template ||
						response.template.length<=0){
						if(timer)
							clearTimeout(timer);

						if('console' in window)
							console.error('load template error: '+settings.template);

						return;
					}

					html=response.template;
					gen=new Rendering(html,settings.template);

					if(from_server){
						EL.storage().setItem(
							'tpl_'+settings.template,
							html
						);

						if(!('time' in response) || response.time==0)
							return;

						EL.storage().setItem(
							'tpl_time_'+settings.template,
							{
								'time': response.time,
								'check': ((new Date()).getTime()/1000)
							}
						);
					}
				});
			}
		})();

		function checkLocalTemplate(callback){
			var t=EL.storage().getItem(
					'tpl_time_'+settings.template,
					true
				),
				now=((new Date()).getTime()/1000),
				current=now-1200;

			if(!t){
				loadTemplate(callback);
			}else if(t.check>current){
				callback({
					'template':EL.storage().getItem('tpl_'+settings.template)
				},false);
			}else{
				loadTemplateTime(function(r){
					if('time' in r && r.time==t.time){
						EL.storage().setItem(
							'tpl_time_'+settings.template,
							{
								'time': r.time,
								'check':now
							}
						);
						callback({
							'template':EL.storage().getItem('tpl_'+settings.template)
						},false);
					}else
						loadTemplate(callback);
				});
			}
		}

		function loadTemplateTime(callback){
			var path=('path' in settings) ?
				settings.path : '/';

			var params=$.extend(
				{
					'get_template_time':true,
					'template': s.template
				},
				settings.params
			);

			$.ajax({
				url:path,
				cache:false,
				data:params,
				type:'GET',
				async:true,
				success:function(r){
					callback(r);
				},
				dataType:'json'
			});
		}

		function loadTemplate(callback){
			var path=('path' in settings) ?
				settings.path : '/';

			var params=$.extend(
				{'template': settings.template},
				settings.params
			);

			$.ajax({
				url:path,
				cache:false,
				data:params,
				type:'GET',
				async:true,
				success:function(r){
					callback(r,true);
				},
				dataType:'json'
			});
		}

		this.set=function(key,value){
			data[key]=value;
		};

		this.render=function(d){
			data=(d && typeof d=='object') ?
				$.extend(data,d) :
				data;
			var result=(gen) ? gen.aggregate(data) : '';
			data={};
			return result;
		};

		this.ready=function(callback){
			if(gen){
				callback(this);
			}else if(readyTimeouts>=settings.params.maxReadyTimeouts){
				if(window.console)
					console.error('templates timeout error');
			}else{
				var ob=this;
				timer=setTimeout(function(){
					ob.ready(callback);
				},50);
				readyTimeouts++;
			}
		};
	}
});