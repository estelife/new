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

	if('jScrollPane' in jQuery.fn){
		$('.production').jScrollPane({
			hideFocus:true,
			verticalDragMaxHeight:100,
			verticalDragMinHeight:50,
			autoReinitialise:true,
			autoReinitialiseDelay:200,
			verticalGutter:0,
			mouseWheelSpeed:30
		});
	}
});