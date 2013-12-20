window.App={
	Routers:{},
	Models:{},
	Views:{},
	Collections:{}
};

EL.loadModule('templates',function(){
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

			if(!this.template || (typeof this.template=='object' && !(this.template instanceof EL.templates)))
				throw 'incorrect template in view';

			if(!(this.collection instanceof Backbone.Collection) && !(this.model instanceof Backbone.Model))
				throw 'invalid model or collection';

			if(typeof this.template!='object')
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
	 * Представление для списка записей, пофиг какого и где используемого,
	 * если надо зафигачить список, переопредяляем его или тупо меняем
	 * при инициализации свойство el.
	 * Добавляет дополнительную эффективность, так как template.ready вызывается
	 * только здесь и в связанные view передает уже готовые объект шаблонизатора
	 * @type {*}
	 */
	App.Views.List=App.Views.Default.extend({
		el:'.items',
		itemView:null,
		initialize:function(data){
			App.Views.Default.prototype.initialize(data);
			this.itemView=(data.itemView) ?
				data.itemView : null;

			if(!this.collection || typeof this.collection!='object' || !(this.collection instanceof Backbone.Collection))
				throw 'incorrect collection in List View';

			if(!this.itemView)
				throw 'not set itemView class';
		},
		render:function(){
			var ob=this,
				itemViewClass=this.itemView;

			this.template.ready(function(){
				ob.$el.empty();

				ob.collection.each(function(model){
					var view=new itemViewClass({
						template:ob.template,
						model:model
					});
					ob.$el.append(view.render().el)
				});
			});

			return this;
		}
	});

	/**
	 * Представление одной записи в списке, сюда можно докручивать какие либо условия.
	 * Можно использовать отдельно
	 * @type {*}
	 */
	App.Views.ListItem=App.Views.Default.extend({
		el:null,
		render: function(){
			this.el=this.template.make(this.model.toJSON());
			return this;
		}
	});

	// MODELS
	/**
	 * Модель для списков. На данный момент ни как не расширена, но
	 * определение используется в коллекции ниже
	 * @type {*}
	 */
	App.Models.List=Backbone.Model.extend({});

	// COLLECTIONS
	/**
	 * Класс коллекуций для моделей списков
	 * @type {*}
	 */
	App.Collections.List=Backbone.Collection.extend({
		model: App.Models.List
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
					if('list' in response && response.list.length>0){
						var view=new App.Views.List({
							collection:new App.Collections.List(response.list),
							itemView:App.Views.ListItem,
							template:'clinics_list'
						});
						view.render();
					}
					if('nav' in response && reseponse.nav.length>0){

					}
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