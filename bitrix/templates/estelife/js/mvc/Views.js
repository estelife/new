define(['tpl/Template'],function(Template){
	var Views={},
		Events=[];

	/**
	 * Дефолтный view для реализации общих действий
	 * @type {*}
	 */
	Views.Default=Backbone.View.extend({
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
				this.template=new Template({
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
	Views.Complex=Backbone.View.extend({
		views:[],
		initialize:function(params){
			if(params.views && _.isArray(params.views)){
				this.views=_.filter(params.views,function(view){
					return (view instanceof Backbone.View);
				})
			}

			if(this.views.length<0)
				throw 'undefined views for complex view';
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
					var el=view.render().$el;
					ob.$el.append(el);
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
	Views.List=Views.Default.extend({
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
	Views.Detail=Views.Default.extend({
		el:document.createElement('div'),
		detailRender:function(){
			if(_.isObject(this.data) && this.data.hasOwnProperty('detail')){
				var ob=this;
				this.$el.addClass('wrap_item')
					.empty();

				this.template.ready(function(){
					ob.template.set('detail', ob.data.detail);

					if(ob.data.hasOwnProperty('same_data'))
						ob.template.set('same_data',ob.data.same_data);

					ob.$el.append(ob.template.render());
				});
			}

			return this;
		},
		render:function(){
			return this.detailRender();
		}
	});

	Views.DetailWithMap=Views.Detail.extend({
		render:function(){
			this.detailRender();
			Events.push({
				target:this.$el,
				type:'showMap'
			});
			return this;
		}
	});

	/**
	 * Представление для постраничной навигации
	 * @type {*}
	 */
	Views.Nav=Views.Default.extend({
		el:document.createElement('ul'),
		render:function(){
			if(_.isObject(this.data) && 'nav' in this.data){
				var nav=this.data.nav;
				this.$el.addClass('nav').empty();

				if(nav.endPage && nav.endPage>1){
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
	Views.Crumb=Views.Default.extend({
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
	Views.Title=Views.Default.extend({
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
	Views.Filter=Views.Default.extend({
		el:document.createElement('form'),
		render:function(){
			if(_.isObject(this.data)){
				var ob=this;

				this.template.ready(function(){
					ob.$el=$(ob.template.render(ob.data));
					ob.el=ob.$el[0];

					Events.push({
						'target':ob.$el,
						'type':'update'
					})
				});
			}

			return this;
		}
	});

	/**
	 * Представленеие для рекламного баннера справа
	 * @type {*}
	 */
	Views.Advert=Views.Default.extend({
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

	/**
	 * Представление для компонентов домашеней страницы
	 * @type {*}
	 */
	Views.Component=Views.Default.extend({
		el:null,//document.createElement('div'),
		render:function(){
			if(_.isObject(this.data)){
				var ob=this;
				this.$el=$('<div class="ajax-content"></div>');

				this.template.ready(function(){
					if(ob.dataKey=='NEWS')
						ob.dataKey='ARTICLES';
					ob.template.set(ob.dataKey,ob.data);
					ob.$el.append($(ob.template.render()));
				});

				ob.el=ob.$el[0];
			}

			return this;
		}
	});

	/**
	 * Представление для внутренней страницы
	 * @type {*}
	 */
	Views.Inner=Views.Complex.extend({
		el:document.createElement('div'),
		render:function(){
			this.$el.addClass('inner');
			return this.mainRender();
		}
	});

	/**
	 * Представления, представляющие из себя контентные области сайта
	 * @type {*}
	 */
	Views.Content=Views.Complex.extend({
		el:null,
		render:function(){
			this.$el=$('<div class="content"></div>');
			this.el=this.$el[0];
			return this.mainRender();
		}
	});

	/**
	 * Главное представление все контентной области сайта. Пришлось для завернуть все в div.
	 * @type {*}
	 */
	Views.WrapContent=Views.Complex.extend({
		el:'div.wrap-content',
		render:function(){
			var ob=this;
			this.$el.empty();

			if(this.views && this.views.length>0){
				_.each(this.views,function(view){
					ob.$el.append(view.render().$el);
				});

				if(!_.isEmpty(Events)){
					_.each(Events,function(event){
						if(event.hasOwnProperty('target') && event.hasOwnProperty('type'))
							event.target.trigger(event.type);
					});
					Events=[];
				}
			}

			return this;
		}
	});

	return Views;
});