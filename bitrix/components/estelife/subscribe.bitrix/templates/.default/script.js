
$(document).ready(function(){
	EL.loadModule('subscribe',function(){
		EL.subscribe($('.fl_sub'), $('input[name=EMAIL]'), $('.sub_check'));
	});

})