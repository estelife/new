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
			'threads-makers/(.*)': 'threadsMakersList',
			'preparations/(.*)': 'preparationsList',
			'implants/(.*)': 'implantsList',
			'threads/(.*)': 'threadsList',
			'apparatuses/(.*)': 'apparatusesList',
			'events/(.*)': 'eventsList',
			'organizers/(.*)': 'sponsorsList',
			'training-centers/(.*)': 'trainingCentersList',
			'trainings/(.*)': 'trainingsList',
			'professionals/(.*)': 'professionalsList',
			'ap:number/': 'apparatusesDetail',
			'am:number/': 'apparatusesMakersDetail',
			'cl:number/(:param/)': 'clinicsDetail',
			'ev:number/': 'eventsDetail',
			'ps:number/': 'preparationsDetail',
			'th:number/': 'threadsDetail',
			'im:number/': 'implantsDetail',
			'pm:number/': 'preparationsMakersDetail',
			'tm:number/': 'threadsMakersDetail',
			'sp:number/': 'sponsorsDetail',
			'pr:number/': 'promotionsDetail',
			'tc:number/': 'trainingCentersDetail',
			'tr:number/': 'trainingsDetail',
			'pt:number/': 'podcastDetail',
			'ar:number/': 'articlesDetail',
			'ns:number/': 'newsDetail',
			'ex:number/': 'expertsDetail',
			'pf:number/': 'professionalsDetail',
			'ev:number/program/': 'eventsProgram',
			'ev:number/(:param)/': 'eventsHall',
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
			lightMenu();
			this.articlesList(param,'novosti/')
		},

		podcastList:function(param){
			lightMenu();
			this.articlesList(param,'podcast/')
		},

		articlesList:function(param,page){
			lightMenu();
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
									className:'',
									dataKey:'BANNER'
								})
							]
						})
					]
				})
			})).fetch();
		},

		searchPage:function(){
			lightMenu();
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
											new Views.List({
												template:'search_page'
//												dataKey:'SEARCH_PAGE'
											}),
											new Views.Nav()
										]
									}),
									new Views.AdvertDelay({
										className:'',
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
			lightMenu();
			(new Models.Inner(null,{
				page:'home/'+EL.query().toString(),
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
			lightMenu();
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
									className:'',
									dataKey:'BANNER'
								})
							]
						})
					]
				})
			})).fetch();
		},

		promotionList: function(){
			lightMenu();
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
									className:'',
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
			lightMenu();
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
									className:'',
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
			lightMenu();
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
									className:'',
									dataKey:'BANNER'
								})
							]
						})
					]
				})
			});
			model.fetch();
		},

		threadsMakersList: function(){
			lightMenu();
			var model=new Models.Inner(null,{
				pages:this.getShortPages(
					[
						'threads-makers/'+EL.query().toString(),
						'threads_makers_filter/'+EL.query().toString(),
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
											template:'threads_makers_list'
										}),
										new Views.Nav()
									]
								}),
								new Views.Filter({
									template:'threads_makers_filter'
								}),
								new Views.AdvertDelay({
									className:'',
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
			lightMenu();
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
									className:'',
									dataKey:'BANNER'
								})
							]
						})
					]
				})
			});
			model.fetch();
		},

		implantsList: function(){
			lightMenu();
			var model=new Models.Inner(null,{
				pages:this.getShortPages(
					[
						'implants/'+EL.query().toString(),
						'implants_filter/'+EL.query().toString(),
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
											template:'implants_list'
										}),
										new Views.Nav()
									]
								}),
								new Views.Filter({
									template:'implants_filter'
								}),
								new Views.AdvertDelay({
									className:'',
									dataKey:'BANNER'
								})
							]
						})
					]
				})
			});
			model.fetch();
		},

		threadsList: function(){
			lightMenu();
			var model=new Models.Inner(null,{
				pages:this.getShortPages(
					[
						'threads/'+EL.query().toString(),
						'threads_filter/'+EL.query().toString(),
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
											template:'threads_list'
										}),
										new Views.Nav()
									]
								}),
								new Views.Filter({
									template:'threads_filter'
								}),
								new Views.AdvertDelay({
									className:'',
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
			lightMenu();
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
									className:'',
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
			lightMenu();
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
									className:'',
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
			lightMenu();
			var model=new Models.Inner(null,{
				pages:this.getShortPages(
					[
						'organizers/'+EL.query().toString(),
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
									className:'',
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
			lightMenu();
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
									className:'',
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
			lightMenu();
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
									className:'',
									dataKey:'BANNER'
								})
							]
						})
					]
				})
			});
			model.fetch();
		},

		professionalsList: function(){
			lightMenu();
			var model=new Models.Inner(null,{
				pages:this.getShortPages(
					[
						'professionals/'+EL.query().toString(),
						'professionals_filter/'+EL.query().toString(),
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
											template:'professionals_list'
										}),
										new Views.Nav()
									]
								}),
								new Views.Filter({
									template:'professionals_filter'
								}),
								new Views.AdvertDelay({
									className:'',
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
			lightMenu();
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
									className:'',
									dataKey:'BANNER'
								})
							]
						})
					]
				})
			})).fetch();
		},
		expertsDetail:function(id,type){
			lightMenu();
			type=(!type) ? 'ex' : type;
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
											template:'experts_detail'
										})
									]
								}),
								new Views.Advert({
									className:'',
									dataKey:'BANNER'
								})
							]
						})
					]
				})
			})).fetch();
		},


		apparatusesDetail: function(id){
			lightMenu();
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
									className:'',
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
			lightMenu();
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
									className:'',
									dataKey:'BANNER'
								})
							]
						})
					]
				})
			});
			model.fetch();
		},

		clinicsDetail: function(id, path){
			lightMenu();
			var model=new Models.Inner(null,{
				pages:[
					'cl'+id+'/'+(path && path!='undefined' ? path + '/' : ''),
					'clinics_filter/'+EL.query().toString(),
					'review_list/?clinic_id='+id,
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
										new Views.DetailWithMapAndGallery({
											template:'clinics_detail'
										})
									]
								}),
								new Views.Filter({
									template:'clinics_filter'
								}),
								new Views.AdvertDelay({
									className:'',
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
			lightMenu();
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
									className:'',
									dataKey:'BANNER'
								})
							]
						})
					]
				})
			});
			model.fetch();
		},

		eventsProgram: function(id){
			lightMenu();
			var model=new Models.Inner(null,{
				pages:[
					'ev'+id+'/program/'+EL.query().toString()
				],
				view:new Views.WrapContent({
					views:[
						new Views.SEO(),
						new Views.Content({
							views:[
								new Views.Crumb(),
								new Views.Detail({
									template:'events_program'
								})
							]
						})
					]
				})
			});
			model.fetch();
		},

		eventsHall: function(id, param){
			lightMenu();
			var model=new Models.Inner(null,{
				pages:[
					'ev'+id+'/'+param+'/'
				],
				view:new Views.WrapContent({
					views:[
						new Views.SEO(),
						new Views.Content({
							views:[
								new Views.Crumb(),
								new Views.Detail({
									template:'events_hall'
								})
							]
						})
					]
				})
			});
			model.fetch();
		},

		preparationsDetail: function(id){
			lightMenu();
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
									className:'',
									dataKey:'BANNER'
								})
							]
						})
					]
				})
			});
			model.fetch();
		},

		threadsDetail: function(id){
			lightMenu();
			var model=new Models.Inner(null,{
				pages:[
					'th'+id+'/',
					'threads_filter/'+EL.query().toString(),
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
											template:'threads_detail'
										})
									]
								}),
								new Views.Filter({
									template:'threads_filter'
								}),
								new Views.AdvertDelay({
									className:'',
									dataKey:'BANNER'
								})
							]
						})
					]
				})
			});
			model.fetch();
		},

		implantsDetail: function(id){
			lightMenu();
			var model=new Models.Inner(null,{
				pages:[
					'im'+id+'/',
					'implants_filter/'+EL.query().toString(),
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
											template:'implants_detail'
										})
									]
								}),
								new Views.Filter({
									template:'implants_filter'
								}),
								new Views.AdvertDelay({
									className:'',
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
			lightMenu();
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
									className:'',
									dataKey:'BANNER'
								})
							]
						})
					]
				})
			});
			model.fetch();
		},

		threadsMakersDetail: function(id){
			lightMenu();
			var model=new Models.Inner(null,{
				pages:[
					'tm'+id+'/',
					'threads_makers_filter/'+EL.query().toString(),
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
											template:'threads_makers_detail'
										})
									]
								}),
								new Views.Filter({
									template:'threads_makers_filter'
								}),
								new Views.AdvertDelay({
									className:'',
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
			lightMenu();
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
									className:'',
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
			lightMenu();
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
									className:'',
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
			lightMenu();
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
										new Views.DetailWithMapAndGallery({
											template:'training_centers_detail'
										})
									]
								}),
								new Views.Filter({
									template:'training_centers_filter'
								}),
								new Views.AdvertDelay({
									className:'',
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
			lightMenu();
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
									className:'',
									dataKey:'BANNER'
								})
							]
						})
					]
				})
			});
			model.fetch();
		},

		professionalsDetail: function(id){
			lightMenu();
			var model=new Models.Inner(null,{
				pages:[
					'pf'+id+'/',
					'professionals_filter/'+EL.query().toString(),
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
											template:'professionals_detail'
										})
									]
								}),
								new Views.Filter({
									template:'professionals_filter'
								}),
								new Views.AdvertDelay({
									className:'',
									dataKey:'BANNER'
								})
							]
						})
					]
				})
			});
			model.fetch();
		},

		reviewList: function(clinicId, problemId, specialistId) {
			var query = ['clinic_id='+clinicId];

			if (problemId)
				query.push('problem_id='+problemId);

			if (specialistId)
				query.push('specialist_id='+specialistId);

			var model=new Models.Component(null,{
				pages:[
					'review_list/?'+query.join('&')
				],
				view: new Views.Reviews({
					template:'review_list'
				})
			});
			model.fetch();
		},

		reviewForm: function(clinic_id) {
			var model=new Models.Component(null,{
				pages:[
					'review_form/?clinic_id='+clinic_id
				],
				view: new Views.Reviews({
					template:'review_form'
				})
			});
			model.fetch();
		}
	});

	return Routers;
});

function lightMenu(){
	EL.SystemSettings.ready(function(s){
		$(".main_menu a").parent().removeClass('main').removeClass('active');
		$(".submenu a ").parent().removeClass('second_active');

		$(".main_menu a").each(function() {
			var href = $(this).attr("href"),
				path_name = document.location.pathname.split('/'),
				reg,matches;

			if(!_.isEmpty(path_name[1])){
				reg=new RegExp('^.*'+path_name[1]+'.*$');
				matches=href.match(reg);
			}

			if (matches) {
				$(this).closest(".main_menu>li").addClass("active").addClass('main');
				return false;
			}else{
				var mass = s.directions;
				path_name = path_name[1];
				reg=new RegExp('^([a-z]{2})[0-9]+$');
				matches=path_name.match(reg);

				if (matches && mass[matches[1]].length>0){
					reg=new RegExp('^.*'+mass[matches[1]]+'.*$');
					var href_matches=href.match(reg);

					if (href_matches && !$(".main_menu>li.active").length){
						$(this).closest(".main_menu>li").addClass("active").addClass('main');
						return false;
					}
				}
			}
		});

		//подсветка урлов второго уровня
		$(".submenu a").each(function(){
			var href = $(this).attr("href"),
				reg=new RegExp('^('+href+')(\\?.*)?$'),
				path_name = document.location.href.split('/').slice(3).join('/');
			path_name = '/'+path_name;
			matches =path_name.match(reg);

			if (matches || path_name == '/apparatuses-makers/') {
				if (path_name == '/apparatuses-makers/'){
					$('.submenu li a[href="/preparations-makers/"]').parent().addClass("second_active").parent().parent().addClass("active").addClass('main');
				}else{
					$(this).closest(".submenu>li").addClass("second_active");
				}
				return false;
			}else{

				var mass = s.directions,
					reg=new RegExp('^([a-z]{2})[0-9]+$');
				path_name = document.location.pathname.split('/').slice(1, -1).pop();

				if (path_name){
					var matches=path_name.match(reg);

					if ((matches && mass[matches[1]].length>0) || (matches && matches[1]=='am')){

						if (matches[1]=='am'){
							$('.submenu li a[href="/preparations-makers/"]').parent().addClass("second_active").parent().parent().addClass("active").addClass('main');
						}else{
							reg=new RegExp('^\/'+mass[matches[1]]+'\/$');
							var href_matches=href.match(reg);

							if (href_matches){
								$(this).closest(".submenu>li").addClass("second_active");
								return false;
							}
						}
					}
				}
			}
		});
	});
}