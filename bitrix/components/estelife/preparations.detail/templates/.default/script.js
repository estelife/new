/**
 * Created with JetBrains PhpStorm.
 * User: dmitriy
 * Date: 28.10.13
 * Time: 11:29
 * To change this template use File | Settings | File Templates.
 */
$(function(){
	EL.loadModule('slider',function(){
		EL.slider($('.gallery .gallery-in .item'));
	});
});