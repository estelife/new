$(document).ready(function(){
	EL.loadModule('select',function(){
		$('select').each(function(){
			new EL.select($(this));
		});
	});
});