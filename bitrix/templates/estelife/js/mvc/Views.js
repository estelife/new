define(['tpl/Template'],function(Template){
	var Views={};

	var Events=(function(){
		var eventsCache=[];

		(function fireEvents(){
			if(eventsCache.length>0){
				_.each(eventsCache,function(event){
					if(event.hasOwnProperty('target') && event.hasOwnProperty('type'))
						event.target.trigger(event.type);
				});
				eventsCache=[];
			}
			setTimeout(arguments.callee,100);
		})();

		return {
			fromArray:function(ar){
				if(ar instanceof Array && ar.length>0){
					for(var i=0;i<ar.length; i++){
						this.push(ar[i]);
					}
				}
			},
			push:function(event){
				if(event.hasOwnProperty('target') && event.hasOwnProperty('type'))
					eventsCache.push(event);
			}
		};
	})();

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
	 * Представление для списка комментариев
	 * @type {*}
	 */
	Views.Comments=Views.Default.extend({
		el:'div.comments-ajax',
		render:function(){
			if(_.isObject(this.data) && this.data.hasOwnProperty('comments')){
				var ob=this;

				if(this.$el.length==0){
					this.$el=$('<div class="comments-ajax"></div>');
					this.el=this.$el[0];
				}

				this.$el.empty();

				this.template.ready(function(){
					ob.template.set('comments', ob.data.comments);
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
						ob.template.set('same_data', ob.data.same_data);

					ob.$el.append(ob.template.render());

					var commentsView=new Views.Comments({
						template:'comments_list'
					});
					commentsView.setData(ob.data);
					commentsView.render();

					ob.$el.find('.comments-ajax')
						.replaceWith(commentsView.$el);

					var reviewsView=new Views.Reviews({
						template:'review_list'
					});
					reviewsView.setData(ob.data);
					reviewsView.render();

					ob.$el.find('.reviews')
						.replaceWith(reviewsView.$el);

					Events.push({
						target:$('body'),
						type:'updateContent'
					});

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

	Views.DetailWithMapAndGallery=Views.Detail.extend({
		render:function(){
			this.detailRender();
			Events.push({
				target:this.$el,
				type:'showMap'
			});
			Events.push({
				target:this.$el.find('.gallery'),
				type:'updateGallery'
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

//				$('title').html(data.name);

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
	 * Представление для обозначения сео данных
	 * @type {*}
	 */
	Views.SEO=Views.Default.extend({
		el:null,
		render:function(){
			if(_.isObject(this.data) && this.data.hasOwnProperty('seo')){
				var data=this.data.seo;

				if(data.hasOwnProperty('title'))
					$('title').html(data.title);

				if(data.hasOwnProperty('description'))
					$('meta[name=description]').attr('content',data.description);

				if(data.hasOwnProperty('keywords'))
					$('meta[name=keywords]').attr('content',data.keywords);
			}

			return this;
		}
	});

	/**
	 * Представление для фильтра
	 * @type {*}
	 */
	Views.Filter=Views.Default.extend({
		el:document.createElement('div'),
		render:function(){
			if(_.isObject(this.data)){
				var ob=this;
				this.$el.addClass('ajax-filter');

				setTimeout(function(){
					ob.template.ready(function(){
						ob.$el.empty()
							.append(ob.template.render(ob.data));
						Events.push({
							'target':ob.$el.find('form'),
							'type':'updateFilter'
						})
					});
				},0);
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
			if(_.isString(this.data) && this.data!=null){
				this.$el=$('<div></div>').addClass('ajax_banner');

				if(this.className)
					this.$el.addClass(this.className);

				this.$el.append(this.data);
			}

			return this;
		}
	});
	Views.AdvertDelay=Views.Default.extend({
		el:document.createElement('div'),
		render:function(){
			var ob=this;

			if(_.isString(this.data) && this.data!=null){
				setTimeout(function(){
					var className='ajax_banner';

					if(ob.className)
						className+=' '+ob.className;

					ob.$el.addClass(className);
					ob.$el.empty().append(ob.data);
				},100);
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
	 * Статичная страница
	 * @type {*}
	 */
	Views.StaticPage=Views.Default.extend({
		el:null,
		render:function(){
			if(this.data && this.data.hasOwnProperty('page')){
				this.$el=$('<div></div>').addClass('static-page');

				if(this.className)
					this.$el.addClass(this.className);

				this.$el.append(this.data.page);
				this.el=this.$el[0];

				Events.push({
					'target':this.$el.find('form'),
					'type':'updateForm'
				})
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
			}

			EL.goto($('.main_menu'));
			EL.loader.setPercent(100);
			Events.push({
				target:$('body'),
				type:'showHelp'
			});

			return this;
		}
	});

	Views.Reviews=Views.Default.extend({
		el:'div.reviews',
		render:function(){
			if(_.isObject(this.data) && this.data.hasOwnProperty('reviews')){
				EL.loader.setPercent(80);
				var ob=this;

				if(this.$el.length==0){
					this.$el=$('<div class="reviews"></div>');
					this.el=this.$el[0];
				}

				this.$el.empty();

				this.template.ready(function(){
					ob.template.set('reviews', ob.data.reviews);
					ob.$el.append($(ob.template.render()));

					Events.push({
						'target':ob.$el.find('form'),
						'type':'updateForm'
					});

					EL.loader.setPercent(100);
				});

				ob.el=ob.$el[0];
			} else {
				EL.loader.setPercent(100);
			}

			return this;
		}
	});

	return Views;
});