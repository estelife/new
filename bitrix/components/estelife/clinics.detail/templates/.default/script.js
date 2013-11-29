/**
 * Created with JetBrains PhpStorm.
 * User: dmitriy
 * Date: 28.10.13
 * Time: 11:29
 * To change this template use File | Settings | File Templates.
 */
$(function(){
	EL.loadModule('bayan',function(){
		EL.bayan($('.el-gallery .image'));
	});
	EL.loadModule('colorbox',function(){
		EL.colorbox();
	});
	EL.loadModule('miniSlider',function(){
		EL.miniSlider($('.el-scroll .slider_content .el-scroll-in'), '.el-scroll .left', '.el-scroll .right');
	});
[]
});