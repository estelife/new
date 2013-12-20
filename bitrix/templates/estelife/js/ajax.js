window.App={
	Routers:{},
	Models:{},
	Views:{},
	Collections:{}
};

EL.loadModule('templates',function(){
	// MODELS
	/**
	 * Модель комплексной страницы, умеет тянуть свои данный с сервера
	 * @type {*}
	 */
	App.Models.Complex=Backbone.Model.extend({
		viewCollection:{},
		view:null,
		page:null,

		initialize:function(data,params){
			var viewCollection=params.viewCollection||{},
				view=params.view||null,
				page=params.page||null;

			if(!_.isString(page))
				throw 'invalid page';

			if(!(view instanceof App.Views.Complex))
				throw 'invalid view';

			this.view=view;
			this.viewCollection=viewCollection;
			this.page=page;

			this.bind(
				'change',
				this.updateStateEvent,
				this
			);
		},

		updateStateEvent:function(){
			var model=this;

			_.map(this.viewCollection,function(view){
				view.setData(model.toJSON());
				return view;
			});

			this.view.setChildViews(this.viewCollection);
			this.view.render();
		},

		sync:function(){
			var model=this;

			$.get('/rest/'+this.page,{},function(response){
				try{
					response=$.parseJSON(response);
					model.set(response);
				}catch(e){}
			});
		}
	});

	App.Models.Inner=App.Models.Complex.extend({
		defaults:{
			'crumb':null,
			'title':null,
			'nav':null,
			'list':null,
			'item':null
		}
	});

	// VIEWS
	/**
	 * Дефолтный view для реализации общих действий
	 * @type {*}
	 */
	App.Views.Default=Backbone.View.extend({
		el:null,
		template:null,
		data:null,
		initialize: function(params){
			params=params||{};

			this.template=(params.template) ?
				params.template : this.template;

			if(this.template && typeof this.template!='object')
				this.template=new EL.templates({
					'template':this.template,
					'path':'/api/estelife_ajax.php',
					'params':{
						'action':'get_template'
					}
				});
		},
		setData:function(data){
			if(_.isObject(data))
				this.data=data;
		}
	});

	/**
	 * Базовое представление для комплексных представлений
	 * @type {*}
	 */
	App.Views.Complex=Backbone.View.extend({
		viewCollection:{},
		setChildViews:function(viewCollection){
			if(_.isObject(viewCollection))
				this.viewCollection=viewCollection;
		}
	});

	/**
	 * Представление для списка записей
	 * @type {*}
	 */
	App.Views.List=App.Views.Default.extend({
		el:document.createElement('div'),
		render:function(){
			if(_.isObject(this.data) && 'list' in this.data){
				var ob=this;
				this.$el.addClass('items');

				this.template.ready(function(){
					ob.template.set('list', ob.data.list);
					ob.$el.append(ob.template.render());
				});
			}

			return this;
		}
	});

	/**
	 * Представление для постраничной навигации
	 * @type {*}
	 */
	App.Views.Nav=App.Views.Default.extend({
		el:document.createElement('ul'),
		render:function(){
			if(_.isObject(this.data) && 'nav' in this.data){
				var nav=this.data.nav;

				if(nav.endPage && nav.endPage>0){
					if(nav.startPage>1){
						this.$el.append('<li><a href="'+nav.urlPath+'?PAGEN_'+nav.navNum+'=1'+nav.queryString+'">1</a></li>')
							.append('<li><span>...</span></li>');
					}

					var tempStart=nav.startPage;

					while(tempStart<=nav.endPage)
					{
						if(tempStart==nav.pageNomer)
							this.$el.append('<li><b>'+tempStart+'</b></li>');
						else
							this.$el.append('<li><a href="'+nav.urlPath+'?PAGEN_'+nav.navNum+'='+tempStart+nav.queryString+'">'+tempStart+'</a></li>');

						tempStart++;
					}

					if(nav.endPage<nav.pageCount){
						this.$el.append('<li><span>...</span></li>')
							.append('<li><a href="'+nav.urlPath+'?PAGEN_'+nav.navNum+'='+nav.pageCount+nav.queryString+'">'+nav.pageCount+'</a></li>');
					}


				}

				this.$el.addClass('nav');
			}

			return this;
		}
	});

	/**
	 * Представление для хлебной дорожки
	 * @type {*}
	 */
	App.Views.Crumb=App.Views.Default.extend({
		el:document.createElement('ul'),
		render:function(){
			if(_.isObject(this.data) && 'crumb' in this.data){
				var ob=this,
					data=this.data.crumb,
					last=data.pop();

				this.$el.addClass('crumb');

				_.each(data,function(item){
					ob.$el.append('<li><a href="'+item.link+'">'+item.name+'</a></li>');
				});

				this.$el.append('<li><b>'+last.name+'</b></li>');
			}

			return this;
		}
	});

	/**
	 * Представление для заголовка страницы
	 * @type {*}
	 */
	App.Views.Title=App.Views.Default.extend({
		el:document.createElement('div'),
		render:function(){
			if(_.isObject(this.data) && 'title' in this.data){
				var data=this.data.title,
					html='<h1>'+data.name+'</h1>';

				this.$el.addClass('title')
					.empty()
					.html(html);
			}

			return this;
		}
	});

	/**
	 * Представление для внутренней страницы
	 * @type {*}
	 */
	App.Views.Inner=App.Views.Complex.extend({
		el:'.inner',
		render:function(){
			this.$el.empty();

			if(this.viewCollection.viewCrumb)
				this.$el.append(this.viewCollection.viewCrumb.render().el);

			if(this.viewCollection.viewTitle)
				this.$el.append(this.viewCollection.viewTitle.render().el);

			if(this.viewCollection.viewList)
				this.$el.append(this.viewCollection.viewList.render().$el);
			else if(this.viewCollection.viewItem)
				this.$el.append(this.viewCollection.viewItem.render().el);

			if(this.viewCollection.viewNav)
				this.$el.append(this.viewCollection.viewNav.render().el);

			return this;
		}
	});

	App.Views.ClinicList=App.Views.List.extend({
		template:'clinics_list'
	});

	// ROUTERS
	App.Routers.Default=new (Backbone.Router.extend({
		routes: {
			'clinics/(.*)': 'clinicList',
			'cl:number/': 'clinicDetail'
		},

		clinicList: function(){
			var model=new App.Models.Inner(null,{
				'page':'clinics/'+EL.query().toString(),
				'viewCollection':{
					'viewTitle':new App.Views.Title(),
					'viewCrumb':new App.Views.Crumb(),
					'viewList':new App.Views.ClinicList(),
					'viewNav':new App.Views.Nav()
				},
				'view':new App.Views.Inner()
			});
			model.fetch();
		}
	}));

	// BULLSHIT
	$(function(){
		Backbone.history.start({
			'pushState':true,
			'hashChange': false
		});

		$('.items .item a').click(function(e){
			var link=$(this),
				href=link.attr('href')||'';

			if(href.length>0){
				App.Routers.Default.navigate(
					href.replace(/^\//,''),
					{trigger: true }
				);
				e.preventDefault();
			}
		});

		$('.head .menu a').click(function(){
			var link=$(this),
				href=link.attr('href')||'';

			if(href.length>0 && href!='#'){
				App.Routers.Default.navigate(
					href.replace(/^\//,''),
					{trigger: true }
				);
				e.preventDefault();
			}
		});
	});
});