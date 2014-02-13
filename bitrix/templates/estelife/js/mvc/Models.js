define(['mvc/Views'],function(Views){
	var Models={};
	
	Models.Default=Backbone.Model.extend({
		page:null,
		pages:[],
		staticPage:null,

		initialize:function(data,params){
			var page=params.page||null,
				pages=params.pages||null;

			if(!_.isString(page) && !_.isArray(pages))
				throw 'invalid page';

			this.pages=pages;
			this.page=page;
			this.staticPage=params.staticPage||false;

			if(!this.staticPage){
				if(this.page)
					this.page='rest/'+this.page;
				if(this.pages)
					this.pages=_.map(this.pages,function(page){
						return 'rest/'+page;
					});
			}
		},

		sync:function(){
			var model=this;

			if(this.pages && 0<this.pages.length<10){
				var data={},
					numRequests=0,
					maxTimeouts=0;

				_.each(this.pages,function(page){
					$.get('/'+page,{},function(response){
						try{
							response=$.parseJSON(response);
							_.extend(data,response);
						}catch(e){
							if(model.staticPage){
								data.page.push(response);
							}else if(window.console)
								console.error(e,page);
						}
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
				var page=this.page;
				$.get('/'+page,{},function(response){
					try{
						response=$.parseJSON(response);
						model.set(response);
					}catch(e){
						if(model.staticPage){
							model.set({
								'page':response
							});
						}else if(window.console)
							console.error(e,page);
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
	Models.Inner=Models.Complex.extend({});

	/**
	 * Модель для комментариев
	 * @type {*}
	 */
	Models.Static=Backbone.Model.extend({
		view:null,

		initialize:function(data,params){
			var view=params.view||null;

			if(!(view instanceof Views.Comments))
				throw 'invalid view';

			this.view=view;
			this.bind(
				'change',
				this.updateStateEvent,
				this
			);

			if(_.isObject(data))
				this.updateStateEvent();
		},

		updateStateEvent:function(){
			this.view.setData(this.toJSON());
			this.view.render();
		}
	});

	return Models;
});