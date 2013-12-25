define(['mvc/Views'],function(Views){
	var Models={};
	
	Models.Default=Backbone.Model.extend({
		page:null,
		pages:[],

		initialize:function(data,params){
			var page=params.page||null,
				pages=params.pages||null;

			if(!_.isString(page) && !_.isArray(pages))
				throw 'invalid page';

			this.pages=pages;
			this.page=page;
		},

		sync:function(){
			var model=this;

			if(this.pages && 0<this.pages.length<10){
				var data={},
					numRequests=0,
					maxTimeouts=0;

				_.each(this.pages,function(page){
					$.get('/rest/'+page,{},function(response){
						try{
							response=$.parseJSON(response);
							_.extend(data,response);
						}catch(e){}
						numRequests++;
						maxTimeouts++;
					});
				});

				var timeout=function(){
					if(numRequests>=model.pages.length || maxTimeouts>=1000){
						model.set(data);
					}else{
						setTimeout(timeout,10);
						maxTimeouts++;
					}
				};
				timeout();
			}else if(this.page){
				$.get('/rest/'+this.page,{},function(response){
					try{
						response=$.parseJSON(response);
						model.set(response);
					}catch(e){
						if(window.console)
							console.error(e);
					}
				});
			}
		}
	});

	/**
	 * Модель комплексной страницы, умеет тянуть свои данный с сервера
	 * @type {*}
	 */
	Models.Complex=Models.Default.extend({
		views:[],

		initialize:function(data,params){
			Models.Default.prototype.initialize(data,params);
			var view=params.view||null;

			if(!(view instanceof Views.Complex))
				throw 'invalid view';

			this.view=view;
			this.bind(
				'change',
				this.updateStateEvent,
				this
			);
		},

		updateStateEvent:function(){
			this.view.setData(this.toJSON());
			this.view.render();
		}
	});

	/**
	 * Модель для отдельного компонента
	 * @type {*}
	 */
	Models.Component=Models.Default.extend({
		initialize:function(data,params){
			Models.Default.prototype.initialize(data,params);
			var view=params.view||null;

			if(!(view instanceof Views.Default))
				throw 'invalid view';

			this.view=view;
			this.bind(
				'change',
				this.updateStateEvent,
				this
			);
		},

		updateStateEvent:function(){
			this.view.setData(this.toJSON());
			this.view.render();
		}
	});

	/**
	 * Модель для внутренней страницы
	 * @type {*}
	 */
	Models.Inner=Models.Complex.extend({
		defaults:{
			'crumb':null,
			'title':null,
			'nav':null,
			'list':null,
			'item':null
		}
	});

	return Models;
});