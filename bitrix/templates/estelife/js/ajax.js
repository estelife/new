window.App={
	Routers:{},
	Models:{},
	Views:{},
	Collections:{}
};

EL.loadModule('templates',function(){
	// MODELS
	App.Models.Default=Backbone.Model.extend({
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
						maxTimeouts++;
					});
				});

				var timeout=function(){
					if(numRequests<=model.pages.length && maxTimeouts<10){
						setTimeout(timeout,100);
						maxTimeouts++;
					}else{
						model.set(data);
					}
				};
				timeout();
			}else if(this.page){
				$.get('/rest/'+this.page,{},function(response){
					try{
						response=$.parseJSON(response);
						model.set(response);
					}catch(e){}
				});
			}
		}
	});

	/**
	 * Модель комплексной страницы, умеет тянуть свои данный с сервера
	 * @type {*}
	 */
	App.Models.Complex=App.Models.Default.extend({
		views:[],

		initialize:function(data,params){
			App.Models.Default.prototype.initialize(data,params);
			var view=params.view||null;

			if(!(view instanceof App.Views.Complex))
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
	App.Models.Component=App.Models.Default.extend({
		initialize:function(data,params){
			App.Models.Default.prototype.initialize(data,params);
			var view=params.view||null;

			if(!(view instanceof App.Views.Default))
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
		views:[],
		initialize:function(params){
			var views=params.views||null;

			if(views && _.isArray(views))
				this.views=views;
		},
		setData:function(data){
			if(data && _.isObject(data)){
				_.each(this.views,function(view){
					view.setData(data);
				})
			}
		},
		render:function(){
			var ob=this;
			this.$el.empty();

			var start=(new Date()).getTime(),
				end=0;

			if(this.views && this.views.length>0){
				_.each(this.views,function(view){
					ob.$el.append(view.render().el);
				});
			}

			end=(new Date()).getTime();
			console.log('profile: '+((end-start)));

			return this;
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
	 * Представление для фильтра
	 * @type {*}
	 */
	App.Views.Filter=App.Views.Default.extend({
		el:'form.filter',
		render:function(){
			if(_.isObject(this.data)){
				var ob=this;
				this.template.ready(function(){
					var form=$(ob.template.render(ob.data));
					ob.$el.empty().append(form.html());
					initFilter(ob.$el);
				});
			}

			return this;
		}
	});

	/**
	 * Представление для внутренней страницы
	 * @type {*}
	 */
	App.Views.Inner=App.Views.Complex.extend({
		el:'.inner'
	});
	App.Views.Content=App.Views.Complex.extend({
		el:'.content:first'
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
			(new App.Models.Inner(null,{
				pages:[
					'clinics/'+EL.query().toString(),
					'clinics_filter/'+EL.query().toString()
				],
				view:new App.Views.Content({
					views:[
						new App.Views.Inner({
							views:[
								new App.Views.Crumb(),
								new App.Views.Title(),
								new App.Views.ClinicList(),
								new App.Views.Nav(),
							]
						}),
						new App.Views.Filter({
							template:'clinics_filter'
						})
					]
				})
			})).fetch();
		},

		promotionList: function(){
			var model=new App.Models.Inner(null,{
				pages:[
					'promotions/'+EL.query().toString(),
					'promotions_filter/'+EL.query().toString()
				],
				view:new App.Views.Content({
					views:[
						new App.Views.Inner({
							views:[
								new App.Views.Crumb(),
								new App.Views.Title(),
								new App.Views.PromotionList(),
								new App.Views.Nav()
							]
						}),
						new App.Views.Filter({
							template:'promotions_filter'
						})
					]
				})
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
				page:'am'+id+'/',
				view:new App.Views.Inner({
					views:[
						new App.Views.Crumb(),
						new App.Views.ApparatusesMakersDetail()
					]
				})
			});
			model.fetch();
		},

		clinicsDetail: function(id){
			var model=new App.Models.Inner(null,{
				page:'cl'+id+'/',
				view:new App.Views.Inner({
					views:[
						new App.Views.Crumb(),
						new App.Views.ClinicsDetail()
					]
				})
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
			(new App.Models.Inner(null,{
				pages:[
					'promotions/'+EL.query().toString(),
					'promotions_filter/'+EL.query().toString()
				],
				view:new App.Views.Content({
					views:[
						new App.Views.Inner({
							views:[
								new App.Views.Crumb(),
								new App.Views.Title(),
								new App.Views.PromotionList(),
								new App.Views.Nav(),
							]
						}),
						new App.Views.Filter({
							template:'promotions_filter'
						})
					]
				})
			})).fetch();
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
				App.Routers.Default.navigate(href,{trigger: true});
				e.preventDefault();
			}
		});

		$('.main_menu a').click(function(e){
			var link=$(this),
				href=link.attr('href')||'',
				parent=link.parents('ul:first'),
				menu=$('.main_menu');

			if(href.length>0 && href!='#'){
				App.Routers.Default.navigate(href,{trigger: true});
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

		$('body').on('click','.nav a', function(e){
			var href=$(this).attr('href');

			if(href && href.length>0){
				EL.goto($('.main_menu'));
				App.Routers.Default.navigate(
					href.replace(/^\/rest/,''),
					{trigger: true}
				);
				e.preventDefault();
			}
		}).on('submit','form.filter',function(e){
			var frm=$(this),
				page=frm.attr('action');

			if(!page || page.length<=0)
				throw 'invalid form action';

			var data={};

			frm.find('input,select').each(function(){
				var inpt=$(this),
					type=inpt.attr('type')||'select',
					name=inpt.attr('name'),
					val='';

				if(type=='text' || type=='select'){
					val=inpt.val();
				}else{
					val=frm.find('input[name='+name+']:checked')
						.attr('value')||0;
				}

				if(val!='' && val!=0 && val!='0')
					data[name]=val;
			});

			App.Routers.Default.navigate(
				page+EL.query().toString(data),
				{trigger: true}
			);
			e.preventDefault();
		}).on('click','form.filter a.clear',function(e){
			var href=$(this).attr('href');
			App.Routers.Default.navigate(
				href,
				{trigger: true}
			);
			e.preventDefault();
		});
	});
});