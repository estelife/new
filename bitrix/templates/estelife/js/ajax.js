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
				this.$el.addClass('items')
					.empty();

				this.template.ready(function(){
					ob.template.set('list', ob.data.list);
					ob.$el.append(ob.template.render());
				});
			}

			return this;
		}
	});

	/**
	 * Представление для детальной страницы
	 * @type {*}
	 */
	App.Views.Detail=App.Views.Default.extend({
		el:document.createElement('div'),
		render:function(){
			if(_.isObject(this.data) && 'detail' in this.data){
				var ob=this;
				this.$el.addClass('item detail '+ob.data.class)
					.empty();

				this.template.ready(function(){
					ob.template.set('detail', ob.data.detail);
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
		el:'ul.nav',
		render:function(){
			if(_.isObject(this.data) && 'nav' in this.data){
				var nav=this.data.nav;
				this.$el.empty();

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
			}

			return this;
		}
	});

	/**
	 * Представление для хлебной дорожки
	 * @type {*}
	 */
	App.Views.Crumb=App.Views.Default.extend({
		el:'ul.crumb',
		render:function(){
			if(_.isObject(this.data) && 'crumb' in this.data){
				var ob=this,
					data=this.data.crumb,
					last=data.pop();

				if(this.$el.length>0)
					this.$el.empty();

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
		el:'.inner div.title',
		render:function(){
			if(_.isObject(this.data) && 'title' in this.data){
				var data=this.data.title,
					html='<h1>'+data.name+'</h1>';


				if (data.menu){
					html+='<ul class="menu">';
					_.each(data.menu, function(item){
						html+='<li><a href="'+item.link+'" class="'+item.class+'">'+item.name+'</a></li>';
					});
					html+='</ul>';
				}

				this.$el.empty()
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
			else if(this.viewCollection.viewDetail)
				this.$el.append(this.viewCollection.viewDetail.render().el);

			if(this.viewCollection.viewNav)
				this.$el.append(this.viewCollection.viewNav.render().el);

			return this;
		}
	});

	App.Views.ClinicList=App.Views.List.extend({
		template:'clinics_list'
	});
	App.Views.PromotionList=App.Views.List.extend({
		template:'promotions_list'
	});
	App.Views.PreparationsMakersList=App.Views.List.extend({
		template:'preparations_makers_list'
	});
	App.Views.ApparatusesMakersList=App.Views.List.extend({
		template:'apparatuses_makers_list'
	});
	App.Views.PreparationsList=App.Views.List.extend({
		template:'preparations_list'
	});
	App.Views.ApparatusesList=App.Views.List.extend({
		template:'apparatuses_list'
	});
	App.Views.EventsList=App.Views.List.extend({
		template:'events_list'
	});
	App.Views.SponsorsList=App.Views.List.extend({
		template:'sponsors_list'
	});
	App.Views.TrainingCentersList=App.Views.List.extend({
		template:'training_centers_list'
	});
	App.Views.TrainingsList=App.Views.List.extend({
		template:'trainings_list'
	});
	App.Views.ApparatusesDetail=App.Views.Detail.extend({
		template:'apparatuses_detail'
	});
	App.Views.ApparatusesMakersDetail=App.Views.Detail.extend({
		template:'apparatuses_makers_detail'
	});
	App.Views.ClinicsDetail=App.Views.Detail.extend({
		template:'clinics_detail'
	});
	App.Views.EventsDetail=App.Views.Detail.extend({
		template:'events_detail'
	});


	App.Views.PreparationsMakersDetail=App.Views.Detail.extend({
		template:'preparations_makers_detail'
	});



	// ROUTERS
	App.Routers.Default=new (Backbone.Router.extend({
		routes: {
			'clinics/(.*)': 'clinicList',
			'promotions/(.*)':'promotionList',
			'preparations-makers/(.*)': 'preparationsMakersList',
			'apparatuses-makers/(.*)': 'apparatusesMakersList',
			'preparations/(.*)': 'preparationsList',
			'apparatuses/(.*)': 'apparatusesList',
			'events/(.*)': 'eventsList',
			'sponsors/(.*)': 'sponsorsList',
			'training-centers/(.*)': 'trainingCentersList',
			'trainings/(.*)': 'trainingsList',
			'pm:number/': 'preparationsMakersDetail',
			'ap:number/': 'apparatusesDetail',
			'am:number/': 'apparatusesMakersDetail',
			'cl:number/': 'clinicsDetail',
			'ev:number/': 'eventsDetail'
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
		},

		promotionList: function(){
			var model=new App.Models.Inner(null,{
				'page':'promotions/'+EL.query().toString(),
				'viewCollection':{
					'viewTitle':new App.Views.Title(),
					'viewCrumb':new App.Views.Crumb(),
					'viewList':new App.Views.PromotionList(),
					'viewNav':new App.Views.Nav()
				},
				'view':new App.Views.Inner()
			});
			model.fetch();
		},

		preparationsMakersList: function(){
			var model=new App.Models.Inner(null,{
				'page':'preparations-makers/'+EL.query().toString(),
				'viewCollection':{
					'viewTitle':new App.Views.Title(),
					'viewCrumb':new App.Views.Crumb(),
					'viewList':new App.Views.PreparationsMakersList(),
					'viewNav':new App.Views.Nav()
				},
				'view':new App.Views.Inner()
			});
			model.fetch();
		},

		apparatusesMakersList: function(){
			var model=new App.Models.Inner(null,{
				'page':'apparatuses-makers/'+EL.query().toString(),
				'viewCollection':{
					'viewTitle':new App.Views.Title(),
					'viewCrumb':new App.Views.Crumb(),
					'viewList':new App.Views.ApparatusesMakersList(),
					'viewNav':new App.Views.Nav()
				},
				'view':new App.Views.Inner()
			});
			model.fetch();
		},

		preparationsList: function(){
			var model=new App.Models.Inner(null,{
				'page':'preparations/'+EL.query().toString(),
				'viewCollection':{
					'viewTitle':new App.Views.Title(),
					'viewCrumb':new App.Views.Crumb(),
					'viewList':new App.Views.PreparationsList(),
					'viewNav':new App.Views.Nav()
				},
				'view':new App.Views.Inner()
			});
			model.fetch();
		},

		apparatusesList: function(){
			var model=new App.Models.Inner(null,{
				'page':'apparatuses/'+EL.query().toString(),
				'viewCollection':{
					'viewTitle':new App.Views.Title(),
					'viewCrumb':new App.Views.Crumb(),
					'viewList':new App.Views.ApparatusesList(),
					'viewNav':new App.Views.Nav()
				},
				'view':new App.Views.Inner()
			});
			model.fetch();
		},

		eventsList: function(){
			var model=new App.Models.Inner(null,{
				'page':'events/'+EL.query().toString(),
				'viewCollection':{
					'viewTitle':new App.Views.Title(),
					'viewCrumb':new App.Views.Crumb(),
					'viewList':new App.Views.EventsList(),
					'viewNav':new App.Views.Nav()
				},
				'view':new App.Views.Inner()
			});
			model.fetch();
		},

		sponsorsList: function(){
			var model=new App.Models.Inner(null,{
				'page':'sponsors/'+EL.query().toString(),
				'viewCollection':{
					'viewTitle':new App.Views.Title(),
					'viewCrumb':new App.Views.Crumb(),
					'viewList':new App.Views.SponsorsList(),
					'viewNav':new App.Views.Nav()
				},
				'view':new App.Views.Inner()
			});
			model.fetch();
		},

		trainingCentersList: function(){
			var model=new App.Models.Inner(null,{
				'page':'training-centers/'+EL.query().toString(),
				'viewCollection':{
					'viewTitle':new App.Views.Title(),
					'viewCrumb':new App.Views.Crumb(),
					'viewList':new App.Views.TrainingCentersList(),
					'viewNav':new App.Views.Nav()
				},
				'view':new App.Views.Inner()
			});
			model.fetch();
		},

		trainingsList: function(){
			var model=new App.Models.Inner(null,{
				'page':'trainings/'+EL.query().toString(),
				'viewCollection':{
					'viewTitle':new App.Views.Title(),
					'viewCrumb':new App.Views.Crumb(),
					'viewList':new App.Views.TrainingsList(),
					'viewNav':new App.Views.Nav()
				},
				'view':new App.Views.Inner()
			});
			model.fetch();
		},

		preparationsMakersDetail: function(id){
			var model=new App.Models.Inner(null,{
				'page':'pm'+id+'/',
				'viewCollection':{
					'viewCrumb':new App.Views.Crumb(),
					'viewDetail':new App.Views.PreparationsMakersDetail()
				},
				'view':new App.Views.Inner()
			});
			model.fetch();
		},

		apparatusesDetail: function(id){
			var model=new App.Models.Inner(null,{
				'page':'ap'+id+'/',
				'viewCollection':{
					'viewCrumb':new App.Views.Crumb(),
					'viewDetail':new App.Views.ApparatusesDetail()
				},
				'view':new App.Views.Inner()
			});
			model.fetch();
		},

		apparatusesMakersDetail: function(id){
			var model=new App.Models.Inner(null,{
				'page':'am'+id+'/',
				'viewCollection':{
					'viewCrumb':new App.Views.Crumb(),
					'viewDetail':new App.Views.ApparatusesMakersDetail()
				},
				'view':new App.Views.Inner()
			});
			model.fetch();
		},

		clinicsDetail: function(id){
			var model=new App.Models.Inner(null,{
				'page':'cl'+id+'/',
				'viewCollection':{
					'viewCrumb':new App.Views.Crumb(),
					'viewDetail':new App.Views.ClinicsDetail()
				},
				'view':new App.Views.Inner()
			});
			model.fetch();
		},

		eventsDetail: function(id){
			var model=new App.Models.Inner(null,{
				'page':'ev'+id+'/',
				'viewCollection':{
					'viewCrumb':new App.Views.Crumb(),
					'viewDetail':new App.Views.EventsDetail()
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

		$('body').on("click",".items .item a", function(e){
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

		$('.main_menu a').click(function(e){
			var link=$(this),
				href=link.attr('href')||'',
				parent=link.parents('ul:first'),
				menu=$('.main_menu');

			if(href.length>0 && href!='#'){
				App.Routers.Default.navigate(
					href.replace(/^\//,''),
					{trigger: true}
				);
				e.preventDefault();
			}

			menu.find('.main,.active,.second_active')
				.removeClass('main active second_active');

			if(parent.hasClass('.main_menu')){
				link.addClass('main')
			}else{
				parent=link.parents('li');
				parent.eq(0).addClass('second_active');
				parent.eq(1).addClass('main');
			}
		});
	});
});