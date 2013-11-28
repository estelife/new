/**
 * Created with JetBrains PhpStorm.
 * User: dmitriy
 * Date: 28.10.13
 * Time: 11:44
 * To change this template use File | Settings | File Templates.
 */
Estelife.prototype.autocompleteFilter=function(list, action){

	function init(){
		if('autocomplete' in jQuery.fn){
			$(list).autocomplete({
				minLength:3,
				source: function(request,response) {
					var inpt=this.element;
					$.get('/api/estelife_ajax.php',{
						'action':action,
						'term':request.term
					},function(r){
						if('list' in r){
								response($.map(r.list, function(item) {
									return {
										label: item.name,
										value: item.name,
										translit: item.translit
									}
								}));
						}
					},'json');

					return true;
				},
				select:function(e, ui) {
					if(showDetail && typeof showDetail=='function'){
						getUrl(ui.item.translit);
					}else{
						$(this).change();
					}
				}
			})
		}
	}

	function getUrl(translit){
		if ($('body').hasClass('el-ditem')){
			var href = location.href;
		}else{
			var href = $('.el-get-detail').attr('href');
		}
		var matches=href.replace(/([^\/]+)\/?$/, translit+'/');
		showDetail(matches);
	}


	init();
};