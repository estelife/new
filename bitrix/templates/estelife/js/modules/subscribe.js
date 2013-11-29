/**
 * Created with JetBrains PhpStorm.
 * User: dmitriy
 * Date: 28.10.13
 * Time: 11:44
 * To change this template use File | Settings | File Templates.
 */
Estelife.prototype.subscribe=function(submit, email, ch){

	var id = new Array();


	function init(){}

	function click_s(){
		var emailVal = email.val();

		ch.each(function(k,v){
			if (v.checked){
				id[k] = $(this).val();
			}
		});

		var arIds = id.join('_');

		$.get('/api/estelife_ajax.php',{
			'action':'subscribe',
			'term':arIds,
			'email':emailVal
		},function(r){
			if('subscribe' in r){
				if (r.subscribe == 'subscribe_delete'){
					EL.notice.complete('Подписка удалена');
				}else if(r.subscribe == 'subscribe_insert'){
					EL.notice.complete('Подписка добавлена');
				}else if (r.subscribe == 'subscribe_update'){
					EL.notice.complete('Подписка обновлена');
				}else{
					EL.notice.error(r.ERRORS);
				}
			}
		},'json');

		return false;
	}

	submit.click(click_s);

};