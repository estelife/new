/**
 * Created with JetBrains PhpStorm.
 * User: dmitriy
 * Date: 28.10.13
 * Time: 12:58
 * To change this template use File | Settings | File Templates.
 */
Estelife.prototype.colorbox=function(){
	if(!('colorboxInit' in this)){
		$('<link>')
			.appendTo($('head'))
			.attr({type : 'text/css', rel : 'stylesheet'})
			.attr('href', '/bitrix/templates/web20/css/colorbox.css');
		$.getScript(
			'/bitrix/templates/web20/js/jquery.colorbox-min.js',
			function(){
				init();
			}
		);
		this.colorboxInit=this;
	}else
		init();

	function init(){
		$('a.colorbox').colorbox({
			photo:true,
			maxWidth:900,
			maxHeight:640,
			current:'Изображение {current} из {total}'
		});
	}
};