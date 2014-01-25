define(['mvc/Models','mvc/Views'],function(Models,Views){
	var Routers={},
		lctn;
	Routers.Default=Backbone.Router.extend({
		routes: {
			'':'homePage',
			'novosti/(:param/)(:query)':'newsList',
			'podcast/(:param/)(:query)':'podcastList',
			'articles/(:param/)(:query)':'articlesList',
			'search/(.*)':'searchPage',
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
			'ap:number/': 'apparatusesDetail',
			'am:number/': 'apparatusesMakersDetail',
			'cl:number/': 'clinicsDetail',
			'ev:number/': 'eventsDetail',
			'ps:number/': 'preparationsDetail',
			'pm:number/': 'preparationsMakersDetail',
			'sp:number/': 'sponsorsDetail',
			'pr:number/': 'promotionsDetail',
			'tc:number/': 'trainingCentersDetail',
			'tr:number/': 'trainingsDetail',
			'pt:number/': 'podcastDetail',
			'ar:number/': 'articlesDetail',
			'ns:number/': 'newsDetail',
			'*path':  'defaultRoute'
		},
		defaultRoute:function(path){
			(new Models.Inner(null,{
				page: path,
				view:new Views.WrapContent({
					views:[
						new Views.StaticPage()
					]
				}),
				staticPage:true
			})).fetch();
		},
		getShortPages:function(pages, pageNum){
			var newPages=[];

			if (lctn==location.pathname){
				if (pageNum instanceof Array){
					for(var i=0; i<pageNum.length; i++){
						newPages[i] = pages[pageNum[i]];
					}
				}else{
					newPages.push(pages[pageNum]);
				}
			}else{
				newPages = pages;
			}

			lctn=location.pathname;
			return newPages;
		},

		newsList:function(param){
			this.articlesList(param,'novosti/')
		},

		podcastList:function(param){
			this.articlesList(param,'podcast/')
		},

		articlesList:function(param,page){
			page=(page ? page : 'articles/')+(param ? param+'/' : '') + EL.query().toString();
			(new Models.Inner(null,{
				pages:[
					page,
					'banner/'
				],
				view:new Views.WrapContent({
					views:[
						new Views.SEO(),
						new Views.Content({
							views:[
								new Views.Inner({
									views:[
										new Views.Crumb(),
										new Views.Title(),
										new Views.List({
											template:'articles_list'
										}),
										new Views.Nav()
									]
								}),
								new Views.AdvertDelay({
									className:'adv adv-out right',
									dataKey:'BANNER'
								})
							]
						})
					]
				})
			})).fetch();
		},

		searchPage:function(){
			(new Models.Inner(null,{
				pages:[
					'search/'+EL.query().toString(),
					'banner/'
				],
				view:new Views.WrapContent({
						views:[
							new Views.SEO(),
							new Views.Content({
								views:[
									new Views.Inner({
										views:[
											new Views.Crumb(),
											new Views.Title(),
											new Views.Component({
												template:'search_page',
												dataKey:'SEARCH_PAGE'
											}),
											new Views.Nav()
										]
									}),
									new Views.AdvertDelay({
										className:'adv adv-out right',
										dataKey:'BANNER'
									})
								]
							})
						]
					}
				)
			})).fetch();
		},

		homePage:function(){
			(new Models.Inner(null,{
				page:'home/',
				view:new Views.WrapContent({
					views:[
						new Views.SEO(),
						new Views.Content({
							views:[
								new Views.Component({
									template:'home_podcasts',
									dataKey:'PODCASTS'
								}),
								new Views.Advert({
									className:'adv adv-out right',
									dataKey:'BANNER_RIGHT'
								}),
								new Views.Advert({
									className:'adv top',
									dataKey:'BANNER_TOP'
								}),
								new Views.Component({
									template:'home_experts',
									dataKey:'EXPERTS'
								}),
								new Views.Component({
									template:'home_promotions',
									dataKey:'PROMOTIONS'
								}),
								new Views.Component({
									template:'home_articles',
									dataKey:'NEWS'
								}),
								new Views.Advert({
									className:'adv bottom',
									dataKey:'BANNER_BOTTOM'
								})
							]
						}),
						new Views.Component({
							template:'home_media',
							dataKey:'PHOTOGALLERY'
						}),
						new Views.Content({
							views:[
								new Views.Component({
									template:'home_articles',
									dataKey:'ARTICLES'
								})
							]
						})
					]
				})
			})).fetch();
		},

		clinicList: function(){
			(new Models.Inner(null,{
				pages: this.getShortPages(
					[
						'clinics/'+EL.query().toString(),
						'clinics_filter/'+EL.query().toString(),
						'banner/'
					],
					[0,1]
				),
				view:new Views.WrapContent({
					views:[
						new Views.SEO(),
						new Views.Content({
							views:[
								new Views.Inner({
									views:[
										new Views.Crumb(),
										new Views.Title(),
										new Views.List({
											template:'clinics_list'
										}),
										new Views.Nav()
									]
								}),
								new Views.Filter({
									template:'clinics_filter'
								}),
								new Views.AdvertDelay({
									className:'adv adv-out right',
									dataKey:'BANNER'
								})
							]
						})
					]
				})
			})).fetch();
		},

		promotionList: function(){
			var model=new Models.Inner(null,{
				pages:this.getShortPages(
					[
						'promotions/'+EL.query().toString(),
						'promotions_filter/'+EL.query().toString(),
						'banner/'
					],
					[0,1]
				),
				view:new Views.WrapContent({
					views:[
						new Views.SEO(),
						new Views.Content({
							views:[
								new Views.Inner({
									views:[
										new Views.Crumb(),
										new Views.Title(),
										new Views.List({
											template:'promotions_list'
										}),
										new Views.Nav()
									]
								}),
								new Views.Filter({
									template:'promotions_filter'
								}),
								new Views.AdvertDelay({
									className:'adv adv-out right',
									dataKey:'BANNER'
								})
							]
						})
					]
				})
			});
			model.fetch();
		},

		preparationsMakersList: function(){
			var model=new Models.Inner(null,{
				pages:this.getShortPages(
					[
						'preparations-makers/'+EL.query().toString(),
						'preparations_makers_filter/'+EL.query().toString(),
						'banner/'
					],
					[0,1]
				),
				view:new Views.WrapContent({
					views:[
						new Views.SEO(),
						new Views.Content({
							views:[
								new Views.Inner({
									views:[
										new Views.Crumb(),
										new Views.Title(),
										new Views.List({
											template:'preparations_makers_list'
										}),
										new Views.Nav()
									]
								}),
								new Views.Filter({
									template:'preparations_makers_filter'
								}),
								new Views.AdvertDelay({
									className:'adv adv-out right',
									dataKey:'BANNER'
								})
							]
						})
					]
				})
			});
			model.fetch();
		},

		apparatusesMakersList: function(){
			var model=new Models.Inner(null,{
				pages:this.getShortPages(
					[
						'apparatuses-makers/'+EL.query().toString(),
						'apparatuses_makers_filter/'+EL.query().toString(),
						'banner/'
					],
					[0,1]
				),
				view:new Views.WrapContent({
					views:[
						new Views.SEO(),
						new Views.Content({
							views:[
								new Views.Inner({
									views:[
										new Views.Crumb(),
										new Views.Title(),
										new Views.List({
											template:'apparatuses_makers_list'
										}),
										new Views.Nav()
									]
								}),
								new Views.Filter({
									template:'apparatuses_makers_filter'
								}),
								new Views.AdvertDelay({
									className:'adv adv-out right',
									dataKey:'BANNER'
								})
							]
						})
					]
				})
			});
			model.fetch();
		},

		preparationsList: function(){
			var model=new Models.Inner(null,{
				pages:this.getShortPages(
					[
						'preparations/'+EL.query().toString(),
						'preparations_filter/'+EL.query().toString(),
						'banner/'
					],
					[0,1]
				),
				view:new Views.WrapContent({
					views:[
						new Views.SEO(),
						new Views.Content({
							views:[
								new Views.Inner({
									views:[
										new Views.Crumb(),
										new Views.Title(),
										new Views.List({
											template:'preparations_list'
										}),
										new Views.Nav()
									]
								}),
								new Views.Filter({
									template:'preparations_filter'
								}),
								new Views.AdvertDelay({
									className:'adv adv-out right',
									dataKey:'BANNER'
								})
							]
						})
					]
				})
			});
			model.fetch();
		},

		apparatusesList: function(){
			var model=new Models.Inner(null,{
				pages:this.getShortPages(
					[
						'apparatuses/'+EL.query().toString(),
						'apparatuses_filter/'+EL.query().toString(),
						'banner/'
					],
					[0,1]
				),
				view:new Views.WrapContent({
					views:[
						new Views.SEO(),
						new Views.Content({
							views:[
								new Views.Inner({
									views:[
										new Views.Crumb(),
										new Views.Title(),
										new Views.List({
											template:'apparatuses_list'
										}),
										new Views.Nav()
									]
								}),
								new Views.Filter({
									template:'apparatuses_filter'
								}),
								new Views.AdvertDelay({
									className:'adv adv-out right',
									dataKey:'BANNER'
								})
							]
						})
					]
				})
			});
			model.fetch();
		},

		eventsList: function(){
			var model=new Models.Inner(null,{
				pages:this.getShortPages(
					[
						'events/'+EL.query().toString(),
						'events_filter/'+EL.query().toString(),
						'banner/'
					],
					[0,1]
				),
				view:new Views.WrapContent({
					views:[
						new Views.SEO(),
						new Views.Content({
							views:[
								new Views.Inner({
									views:[
										new Views.Crumb(),
										new Views.Title(),
										new Views.List({
											template:'events_list'
										}),
										new Views.Nav()
									]
								}),
								new Views.Filter({
									template:'events_filter'
								}),
								new Views.AdvertDelay({
									className:'adv adv-out right',
									dataKey:'BANNER'
								})
							]
						})
					]
				})
			});
			model.fetch();
		},

		sponsorsList: function(){
			var model=new Models.Inner(null,{
				pages:this.getShortPages(
					[
						'sponsors/'+EL.query().toString(),
						'sponsors_filter/'+EL.query().toString(),
						'banner/'
					],
					[0,1]
				),
				view:new Views.WrapContent({
					views:[
						new Views.SEO(),
						new Views.Content({
							views:[
								new Views.Inner({
									views:[
										new Views.Crumb(),
										new Views.Title(),
										new Views.List({
											template:'sponsors_list'
										}),
										new Views.Nav()
									]
								}),
								new Views.Filter({
									template:'sponsors_filter'
								}),
								new Views.AdvertDelay({
									className:'adv adv-out right',
									dataKey:'BANNER'
								})
							]
						})
					]
				})
			});
			model.fetch();
		},

		trainingCentersList: function(){
			var model=new Models.Inner(null,{
				pages:this.getShortPages(
					[
						'training-centers/'+EL.query().toString(),
						'training_centers_filter/'+EL.query().toString(),
						'banner/'
					],
					[0,1]
				),
				view:new Views.WrapContent({
					views:[
						new Views.SEO(),
						new Views.Content({
							views:[
								new Views.Inner({
									views:[
										new Views.Crumb(),
										new Views.Title(),
										new Views.List({
											template:'training_centers_list'
										}),
										new Views.Nav()
									]
								}),
								new Views.Filter({
									template:'training_centers_filter'
								}),
								new Views.AdvertDelay({
									className:'adv adv-out right',
									dataKey:'BANNER'
								})
							]
						})
					]
				})
			});
			model.fetch();
		},

		trainingsList: function(){
			var model=new Models.Inner(null,{
				pages:this.getShortPages(
					[
						'trainings/'+EL.query().toString(),
						'trainings_filter/'+EL.query().toString(),
						'banner/'
					],
					[0,1]
				),
				view:new Views.WrapContent({
					views:[
						new Views.SEO(),
						new Views.Content({
							views:[
								new Views.Inner({
									views:[
										new Views.Crumb(),
										new Views.Title(),
										new Views.List({
											template:'trainings_list'
										}),
										new Views.Nav()
									]
								}),
								new Views.Filter({
									template:'trainings_filter'
								}),
								new Views.AdvertDelay({
									className:'adv adv-out right',
									dataKey:'BANNER'
								})
							]
						})
					]
				})
			});
			model.fetch();
		},

		newsDetail:function(id){
			this.articlesDetail(id,'ns');
		},

		podcastDetail:function(id){
			this.articlesDetail(id,'pt');
		},

		articlesDetail:function(id,type){
			type=(!type) ? 'ar' : type;
			(new Models.Inner(null,{
				pages:[
					type+id+'/',
					'banner/'
				],
				view:new Views.WrapContent({
					views:[
						new Views.SEO(),
						new Views.Content({
							views:[
								new Views.Inner({
									views:[
										new Views.Crumb(),
										new Views.Detail({
											template:'articles_detail'
										})
									]
								}),
								new Views.AdvertDelay({
									className:'adv adv-out right',
									dataKey:'BANNER'
								})
							]
						})
					]
				})
			})).fetch();
		},

		apparatusesDetail: function(id){
			var model=new Models.Inner(null,{
				pages:[
					'ap'+id+'/',
					'apparatuses_filter/'+EL.query().toString(),
					'banner/'
				],
				view:new Views.WrapContent({
					views:[
						new Views.SEO(),
						new Views.Content({
							views:[
								new Views.Inner({
									views:[
										new Views.Crumb(),
										new Views.Detail({
											template:'apparatuses_detail'
										})
									]
								}),
								new Views.Filter({
									template:'apparatuses_filter'
								}),
								new Views.AdvertDelay({
									className:'adv adv-out right',
									dataKey:'BANNER'
								})
							]
						})
					]
				})
			});
			model.fetch();
		},

		apparatusesMakersDetail: function(id){
			var model=new Models.Inner(null,{
				pages:[
					'am'+id+'/',
					'apparatuses_makers_filter/'+EL.query().toString(),
					'banner/'
				],
				view:new Views.WrapContent({
					views:[
						new Views.SEO(),
						new Views.Content({
							views:[
								new Views.Inner({
									views:[
										new Views.Crumb(),
										new Views.Detail({
											template:'apparatuses_makers_detail'
										})
									]
								}),
								new Views.Filter({
									template:'apparatuses_makers_filter'
								}),
								new Views.AdvertDelay({
									className:'adv adv-out right',
									dataKey:'BANNER'
								})
							]
						})
					]
				})
			});
			model.fetch();
		},

		clinicsDetail: function(id){
			var model=new Models.Inner(null,{
				pages:[
					'cl'+id+'/',
					'clinics_filter/'+EL.query().toString(),
					'banner/'
				],
				view:new Views.WrapContent({
					views:[
						new Views.SEO(),
						new Views.Content({
							views:[
								new Views.Inner({
									views:[
										new Views.Crumb(),
										new Views.DetailWithMap({
											template:'clinics_detail'
										})
									]
								}),
								new Views.Filter({
									template:'clinics_filter'
								}),
								new Views.AdvertDelay({
									className:'adv adv-out right',
									dataKey:'BANNER'
								})
							]
						})
					]
				})
			});
			model.fetch();
		},

		eventsDetail: function(id){
			var model=new Models.Inner(null,{
				pages:[
					'ev'+id+'/',
					'events_filter/'+EL.query().toString(),
					'banner/'
				],
				view:new Views.WrapContent({
					views:[
						new Views.SEO(),
						new Views.Content({
							views:[
								new Views.Inner({
									views:[
										new Views.Crumb(),
										new Views.Detail({
											template:'events_detail'
										})
									]
								}),
								new Views.Filter({
									template:'events_filter'
								}),
								new Views.AdvertDelay({
									className:'adv adv-out right',
									dataKey:'BANNER'
								})
							]
						})
					]
				})
			});
			model.fetch();
		},

		preparationsDetail: function(id){
			var model=new Models.Inner(null,{
				pages:[
					'ps'+id+'/',
					'preparations_filter/'+EL.query().toString(),
					'banner/'
				],
				view:new Views.WrapContent({
					views:[
						new Views.SEO(),
						new Views.Content({
							views:[
								new Views.Inner({
									views:[
										new Views.Crumb(),
										new Views.Detail({
											template:'preparations_detail'
										})
									]
								}),
								new Views.Filter({
									template:'preparations_filter'
								}),
								new Views.AdvertDelay({
									className:'adv adv-out right',
									dataKey:'BANNER'
								})
							]
						})
					]
				})
			});
			model.fetch();
		},

		preparationsMakersDetail: function(id){
			var model=new Models.Inner(null,{
				pages:[
					'pm'+id+'/',
					'preparations_makers_filter/'+EL.query().toString(),
					'banner/'
				],
				view:new Views.WrapContent({
					views:[
						new Views.SEO(),
						new Views.Content({
							views:[
								new Views.Inner({
									views:[
										new Views.Crumb(),
										new Views.Detail({
											template:'preparations_makers_detail'
										})
									]
								}),
								new Views.Filter({
									template:'preparations_makers_filter'
								}),
								new Views.AdvertDelay({
									className:'adv adv-out right',
									dataKey:'BANNER'
								})
							]
						})
					]
				})
			});
			model.fetch();
		},

		sponsorsDetail: function(id){
			var model=new Models.Inner(null,{
				pages:[
					'sp'+id+'/',
					'sponsors_filter/'+EL.query().toString(),
					'banner/'
				],
				view:new Views.WrapContent({
					views:[
						new Views.SEO(),
						new Views.Content({
							views:[
								new Views.Inner({
									views:[
										new Views.Crumb(),
										new Views.Detail({
											template:'sponsors_detail'
										})
									]
								}),
								new Views.Filter({
									template:'sponsors_filter'
								}),
								new Views.AdvertDelay({
									className:'adv adv-out right',
									dataKey:'BANNER'
								})
							]
						})
					]
				})
			});
			model.fetch();
		},

		promotionsDetail: function(id){
			var model=new Models.Inner(null,{
				pages:[
					'pr'+id+'/',
					'promotions_filter/'+EL.query().toString(),
					'banner/'
				],
				view:new Views.WrapContent({
					views:[
						new Views.SEO(),
						new Views.Content({
							views:[
								new Views.Inner({
									views:[
										new Views.Crumb(),
										new Views.DetailWithMap({
											template:'promotions_detail'
										})
									]
								}),
								new Views.Filter({
									template:'promotions_filter'
								}),
								new Views.AdvertDelay({
									className:'adv adv-out right',
									dataKey:'BANNER'
								})
							]
						})
					]
				})
			});
			model.fetch();
		},

		trainingCentersDetail: function(id){
			var model=new Models.Inner(null,{
				pages:[
					'tc'+id+'/',
					'training_centers_filter/'+EL.query().toString(),
					'banner/'
				],
				view:new Views.WrapContent({
					views:[
						new Views.SEO(),
						new Views.Content({
							views:[
								new Views.Inner({
									views:[
										new Views.Crumb(),
										new Views.DetailWithMap({
											template:'training_centers_detail'
										})
									]
								}),
								new Views.Filter({
									template:'training_centers_filter'
								}),
								new Views.AdvertDelay({
									className:'adv adv-out right',
									dataKey:'BANNER'
								})
							]
						})
					]
				})
			});
			model.fetch();
		},

		trainingsDetail: function(id){
			var model=new Models.Inner(null,{
				pages:[
					'tr'+id+'/',
					'trainings_filter/'+EL.query().toString(),
					'banner/'
				],
				view:new Views.WrapContent({
					views:[
						new Views.SEO(),
						new Views.Content({
							views:[
								new Views.Inner({
									views:[
										new Views.Crumb(),
										new Views.DetailWithMap({
											template:'trainings_detail'
										})
									]
								}),
								new Views.Filter({
									template:'trainings_filter'
								}),
								new Views.AdvertDelay({
									className:'adv adv-out right',
									dataKey:'BANNER'
								})
							]
						})
					]
				})
			});
			model.fetch();
		}

	});

	return Routers;
});