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
		dataKey:null,

		initialize: function(params){
			params=params||{};

			this.dataKey=params.dataKey||null;
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
			if(!_.isObject(data) || (this.dataKey && !(this.dataKey in data)))
				return;

			this.data=(this.dataKey) ?
				data[this.dataKey] : data;
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
		mainRender:function(){
			var ob=this;
			this.$el.empty();

			if(this.views && this.views.length>0){
				_.each(this.views,function(view){
					ob.$el.append(view.render().$el);
				});
			}

			return this;
		},
		render:function(){
			return this.mainRender();
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
		el:document.createElement('ul'),
		render:function(){
			if(_.isObject(this.data) && 'nav' in this.data){
				var nav=this.data.nav;
				this.$el.addClass('nav').empty();

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
		el:document.createElement('ul'),
		render:function(){
			if(_.isObject(this.data) && 'crumb' in this.data){
				var ob=this,
					data=this.data.crumb,
					last=data.pop();

				this.$el.addClass('crumb');

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
		el:document.createElement('div'),
		render:function(){
			if(_.isObject(this.data) && 'title' in this.data){
				this.$el.addClass('title');

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
		el:document.createElement('form'),
		render:function(){
			if(_.isObject(this.data)){
				var ob=this;

				this.template.ready(function(){
					ob.$el=$(ob.template.render(ob.data));
					initFilter(ob.$el);
					ob.el=ob.$el[0]
				});
			}

			return this;
		}
	});

	/**
	 * Представленеие для рекламного баннера справа
	 * @type {*}
	 */
	App.Views.Advert=App.Views.Default.extend({
		el:null,
		render:function(){
			if(_.isString(this.data)){
				this.$el=$('<div></div>').addClass('adv');

				if(this.className)
					this.$el.addClass(this.className);

				this.$el.append(this.data);
				this.el=this.$el[0];
			}
			return this;
		}
	});

	App.Views.HomeComponent=App.Views.Default.extend({
		el:document.createElement('div'),
		render:function(){
			if(_.isObject(this.data)){
				var ob=this;
				this.template.ready(function(){
					if(ob.dataKey=='NEWS')
						ob.dataKey='ARTICLES';

					ob.template.set(ob.dataKey,ob.data);
					ob.$el=$(ob.template.render());
					ob.el=ob.$el[0];
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
		el:document.createElement('div'),
		render:function(){
			this.$el.addClass('inner');
			return this.mainRender();
		}
	});

	App.Views.Content=App.Views.Complex.extend({
		el:null,
		render:function(){
			this.$el=$('<div class="content"></div>');
			this.el=this.$el[0];
			return this.mainRender();
		}
	});

	App.Views.WrapContent=App.Views.Complex.extend({
		el:'div.wrap-content',
		render:function(){
			var ob=this;
			this.$el.empty();

			if(this.views && this.views.length>0){
				_.each(this.views,function(view){
					ob.$el.append(view.render().$el);
				});
			}

			return this;
		}
	});

	// ROUTERS
	App.Routers.Default=new (Backbone.Router.extend({
		routes: {
			'':'homePage',
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

		homePage:function(){
			(new App.Models.Inner(null,{
				page:'home/',
				view:new App.Views.WrapContent({
					views:[
						new App.Views.Content({
							views:[
								new App.Views.HomeComponent({
									template:'home_podcasts',
									dataKey:'PODCASTS'
								}),
								new App.Views.Advert({
									className:'adv adv-out right',
									dataKey:'BANNER_RIGHT'
								}),
								new App.Views.Advert({
									className:'adv top',
									dataKey:'BANNER_TOP'
								}),
								new App.Views.HomeComponent({
									template:'home_experts',
									dataKey:'EXPERTS'
								}),
								new App.Views.HomeComponent({
									template:'home_promotions',
									dataKey:'PROMOTIONS'
								}),
								new App.Views.HomeComponent({
									template:'home_articles',
									dataKey:'ARTICLES'
								})
							]
						}),
						new App.Views.HomeComponent({
							template:'home_media',
							dataKey:'PHOTOGALLERY'
						}),
						new App.Views.Content({
							views:[
								new App.Views.HomeComponent({
									template:'home_articles',
									dataKey:'NEWS'
								})
							]
						})
					]
				})
			})).fetch();
		},

		clinicList: function(){
			(new App.Models.Inner(null,{
				pages:[
					'clinics/'+EL.query().toString(),
					'clinics_filter/'+EL.query().toString()
				],
				view:new App.Views.WrapContent({
					views:[
						new App.Views.Content({
							views:[
								new App.Views.Inner({
									views:[
										new App.Views.Crumb(),
										new App.Views.Title(),
										new App.Views.List({
											template:'clinics_list'
										}),
										new App.Views.Nav()
									]
								}),
								new App.Views.Filter({
									template:'clinics_filter'
								})
							]
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
				view:new App.Views.WrapContent({
					views:[
						new App.Views.Content({
							views:[
								new App.Views.Inner({
									views:[
										new App.Views.Crumb(),
										new App.Views.Title(),
										new App.Views.List({
											template:'promotions_list'
										}),
										new App.Views.Nav()
									]
								}),
								new App.Views.Filter({
									template:'promotions_filter'
								})
							]
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
					'viewList':new App.Views.List({
						template:'preparations_makers_list'
					}),
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
					'viewList':new App.Views.List({
						template:'apparatuses_makers_list'
					}),
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
					'viewList':new App.Views.List({
						template:'preparations_list'
					}),
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
					'viewList':new App.Views.List({
						template:'apparatuses_list'
					}),
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
					'viewList':new App.Views.List({
						template:'events_list'
					}),
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
					'viewList':new App.Views.List({
						template:'sponsors_list'
					}),
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
					'viewList':new App.Views.List({
						template:'training_centers_list'
					}),
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
					'viewList':new App.Views.List({
						template:'trainings_list'
					}),
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
					'viewDetail':new App.Views.Detail({
						template:'preparations_makers_detail'
					})
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
					'viewDetail':new App.Views.Detail({
						template:'apparatuses_detail'
					})
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
						new App.Views.Detail({
							template:'apparatuses_makers_detail'
						})
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
						new App.Views.Detail({
							template:'clinics_detail'
						})
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
					'viewDetail':new App.Views.Detail({
						template:'events_detail'
					})
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

			if(parent.hasClass('main_menu')){
				link.parent().addClass('main')
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
		}).on('click','.logo',function(e){
			App.Routers.Default.navigate(
				$(this).attr('href'),
				{trigger: true}
			);
			e.preventDefault();
		});
	});
});