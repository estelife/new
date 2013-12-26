require(['mvc/Routers'],function(Routers){
	var Router=new Routers.Default();
	
	EL.loadModule('templates',function(){
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
					Router.navigate(href,{trigger: true});
					e.preventDefault();
				}
			});

			$('.main_menu a').click(function(e){
				var link=$(this),
					href=link.attr('href')||'',
					parent=link.parents('ul:first'),
					menu=$('.main_menu');

				if(href.length>0 && href!='#'){
					Router.navigate(href,{trigger: true});
					menu.find('.main,.active,.second_active')
						.removeClass('main active second_active');

					if(parent.hasClass('main_menu')){
						link.parent().addClass('main active')
					}else{
						parent=link.parents('li');
						parent.eq(0).addClass('second_active');
						parent.eq(1).addClass('main active');
					}
					e.preventDefault();
				}
			});

			$('body').on('click','.nav a', function(e){
				var href=$(this).attr('href');

				if(href && href.length>0){
					EL.goto($('.main_menu'));
					Router.navigate(
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

				Router.navigate(
					page+EL.query().toString(data),
					{trigger: true}
				);
				e.preventDefault();
			}).on('click','form.filter a.clear',function(e){
				var href=$(this).attr('href');
				Router.navigate(
					href,
					{trigger: true}
				);
				e.preventDefault();
			}).on('click','.logo',function(e){
				Router.navigate(
					$(this).attr('href'),
					{trigger: true}
				);
				$('.main_menu').find('.main,.active,.second_active')
					.removeClass('main active second_active');
				e.preventDefault();
			}).on('submit','form[name=search]',function(e){
				var frm=$(this),
					href=frm.attr('action'),
					text=frm.find('input[name=q]').val();

				if(text.length>0){
					Router.navigate(
						href+'?q='+text,
						{trigger: true}
					);
					$('.main_menu').find('.main,.active,.second_active')
						.removeClass('main active second_active');
				}else{
					alert('Укажите поисковый запрос')
				}

				e.preventDefault();
			});
		});
	});
});