window.App={
	Routers:{},
	Models:{},
	Views:{},
	Collections:{}
};

EL.loadModule('templates',function(){
	// MODELS
	/**
	 * @type {*}
	 */
	App.Models.ListItem=Backbone.Model.extend({});
	App.Models.PageNav=Backbone.Model.extend({});
	App.Models.Title=Backbone.Model.extend({});
	App.Models.BreadCrumb=Backbone.Model.extend({});
	App.Models.Detail=Backbone.Model.extend({});

	/**
	 * Модель внутренней страницы, умеет тянуть свои данный с сервера
	 * @type {*}
	 */
	App.Models.Inner=Backbone.Model.extend({});

	// COLLECTIONS
	App.Collections.BreadCrumb=Backbone.Collection.extend({
		model:App.Models.BreadCrumb
	});
	App.Collections.ListItems=Backbone.Collection.extend({
		model:App.Models.ListItem
	});

	// VIEWS
	/**
	 * Дефолтный view для реализации общих действий
	 * @type {*}
	 */
	App.Views.Default=Backbone.View.extend({
		el:null,
		template:null,
		model:null,
		collection:null,
		initialize: function(data){
			data=data||{};

			this.template=(data.template) ?
				data.template : this.template;

			this.model=(data.model) ?
				data.model : this.model;

			this.collection=(data.collection) ?
				data.collection : this.collection;

			if(!(this.collection instanceof Backbone.Collection) && !(this.model instanceof Backbone.Model))
				throw 'invalid model or collection';

			if(this.template && typeof this.template!='object')
				this.template=new EL.templates({
					'template':this.template,
					'path':'/api/estelife_ajax.php',
					'params':{
						'action':'get_template'
					}
				});
		}
	});

	/**
	 * Представление для списка записей
	 * @type {*}
	 */
	App.Views.ListItems=App.Views.Default.extend({
		el:document.createElement('div'),
		collection:App.Collections.ListItems,
		render:function(){
			var ob=this;
			this.$el.addClass('items');

			this.template.ready(function(){
				ob.template.set('list', ob.collection.toJSON());
				ob.$el.append(ob.template.render());
			});

			return this;
		}
	});

	/**
	 * Представление для постраничной навигации
	 * @type {*}
	 */
	App.Views.PageNav=App.Views.Default.extend({
		el:'ul.nav',
		template:new EL.templates({
			'template':'pagenav',
			'path':'/api/estelife_ajax.php',
			'params':{
				'action':'get_template'
			}
		}),
		render:function(){
			var ob=this;

			this.template.ready(function(){
				ob.$el.replaceWith(ob.template.make(ob.model.toJSON()));
			});

			return this;
		}
	});

	/**
	 * Представление для хлебной дорожки
	 * @type {*}
	 */
	App.Views.BreadCrumb=App.Views.Default.extend({
		el:document.createElement('ul'),
		render:function(){
			var ob=this,
				last=this.collection.pop();

			this.$el.addClass('crumb');

			this.collection.each(function(model){
				ob.$el.append('<li><a href="'+model.get('link')+'">'+model.get('name')+'</a></li>');
			});

			this.$el.append('<li><b>'+last.get('name')+'</b></li>');
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
			var name=this.model.get('name'),
				html='<h1>'+name+'</h1>';

			this.$el.addClass('title')
				.empty()
				.html(html);
			return this;
		}
	});

	App.Views.Inner=Backbone.View.extend({
		el:'.inner',
		viewBreadCrumb:null,
		viewPageNav:null,
		viewListItems:null,
		viewDetailItem:null,
		viewTitle:null,

		initialize:function(data){
			data=data||{};
			this.viewBreadCrumb=data.viewBreadCrumb||App.Views.BreadCrumb;
			this.viewPageNav=data.viewPageNav||App.Views.PageNav;
			this.viewListItems=data.viewListItems||App.Views.ListItems;
			this.viewDetailItem=data.viewDetailItem||null;
			this.viewTitle=data.viewTitle||App.Views.Title;
		},

		render:function(){
			this.$el.empty();
			var view;

			if(this.viewBreadCrumb && this.model.has('crumb')){
				view=new this.viewBreadCrumb({
					collection:new App.Collections.BreadCrumb(this.model.get('crumb'))
				});
				this.$el.append(view.render().el);
			}

			if(this.viewTitle && this.model.has('title')){
				view=new this.viewTitle({
					model:new App.Models.Title(this.model.get('title'))
				});
				this.$el.append(view.render().el);
			}

			if(this.viewListItems && this.model.has('list')){
				view=new this.viewListItems({
					collection:new App.Collections.ListItems(this.model.get('list'))
				});
				this.$el.append(view.render().$el);
			}else if(this.viewDetailItem && this.model.has('detail')){
				view=new this.viewDetailItem({
					model:new App.Models.Detail(this.model.get('detail'))
				});
				this.$el.append(view.render().el);
			}

			if(this.viewPageNav && this.model.has('nav')){
				view=new this.viewPageNav({
					model:new App.Models.PageNav(this.model.get('nav'))
				});
				this.$el.append(view.render().el);
			}

			return this;
		}
	});

	App.Views.ClinicList=App.Views.ListItems.extend({
		template:'clinics_list'
	});

	// ROUTERS
	App.Routers.Boss=new (Backbone.Router.extend({
		routes: {
			'clinics/(.*)': 'clinicList',
			'cl:number/': 'clinicDetail'
		},

		clinicList: function(){
			var params=this.getQuery();
			params.action='clinics_list';

			$.getJSON(
				'/api/estelife_ajax.php',
				params,
				function(response){
					var view=new App.Views.Inner({
						viewListItems:App.Views.ClinicList,
						model:new App.Models.Inner(response)
					});
					view.render();
				}
			)
		},

		getQuery:function(){
			var query=location.href.split('?'),
				result={};

			if(query.length==2 && query[1] !=''){
				var temp=null;
				query=query[1].split('&');

				for(var i=0; i<query.length; i++){
					temp=query[i].split('=');

					if(temp.length==1)
						temp[1]='';

					result[temp[0]]=decodeURIComponent(temp[1]);
				}
			}

			return result;
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
				EstelifeRouter.navigate(
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
				EstelifeRouter.navigate(
					href.replace(/^\//,''),
					{trigger: true }
				);
				e.preventDefault();
			}
		});
	});
});