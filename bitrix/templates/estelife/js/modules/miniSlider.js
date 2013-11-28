/**
 * Created with JetBrains PhpStorm.
 * User: dmitriy
 * Date: 28.10.13
 * Time: 11:44
 * To change this template use File | Settings | File Templates.
 */
Estelife.prototype.miniSlider=function(list, left, right){

	var parent,
		el;

	function init(){
		if ('console' in window){
			if ($(list).length == 0){
				console.error('list not found');
			}
		}

		parent = list.parent();
		parent.css('height', list.eq(0).height());
		el=list.eq(0);
	}

	function click_r(){
		var el = list.next(),
			height = el.height();

		parent.animate({scrollTop:'+='+height},400);
	}

	function click_l(){
		var el = list.next(),
			height = el.height();

		parent.animate({scrollTop:'-='+height},400);
	}

	$(left).click(click_l);
	$(right).click(click_r);
	init();
};