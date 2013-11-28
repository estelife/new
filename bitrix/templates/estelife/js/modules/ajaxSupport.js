/**
 * Created with JetBrains PhpStorm.
 * User: dmitriy
 * Date: 08.11.13
 * Time: 17:24
 * To change this template use File | Settings | File Templates.
 */
Estelife.prototype.ajaxSupport=function(url){
	if(typeof url!='object' || !(url instanceof EL.url))
		throw 'incorrect url object for ajax support: use Estelife.url';

	var type,
		current,
		supported={
			'promotions':{
				'list':/^\/promotions\/(\?[^\?]+)?$/,
				'detail':/\/promotions\/[\w\d\-_]+\//
			},
			'clinics':{
				'list':/^\/clinic\/(\?[^\?]+)?$/,
				'detail':/\/clinic\/[\w\d\-_]+\//
			},
			'trainings':{
				'list':/^\/trainings\/(\?[^\?]+)?$/,
				'detail':/\/trainings\/[\w\d\-_\.]+\//
			},
			'events':{
				'list':/^\/events\/(\?[^\?]+)?$/,
				'detail':/\/events\/[\w\d\-_]+\//
			},
			'preparations_makers':{
				'list':/^\/preparations-makers\/(\?[^\?]+)?$/,
				'detail':/\/preparations-makers\/[\w\d\-_]+\//
			},
			'apparatuses_makers':{
				'list':/^\/apparatuses-makers\/(\?[^\?]+)?$/,
				'detail':/\/apparatuses-makers\/[\w\d\-_]+\//
			},
			'preparations':{
				'list':/^\/preparations\/(\?[^\?]+)?$/,
				'detail':/\/preparations\/[\w\d\-_]+\//
			},
			'apparatuses':{
				'list':/^\/apparatuses\/(\?[^\?]+)?$/,
				'detail':/\/apparatuses\/[\w\d\-_]+\//
			},
			'training_centers':{
				'list':/^\/training-centers\/(\?[^\?]+)?$/,
				'detail':/\/training-centers\/[\w\d\-_]+\//
			},
			'sponsors':{
				'list':/^\/sponsors\/(\?[^\?]+)?$/,
				'detail':/\/sponsors\/[\w\d\-_]+\//
			}
		};

	(function init(){
		var list,detail;

		for(key in supported){
			if(!('list' in supported[key]) && !('detail' in supported[key]))
				return;

			if((list=url.check(supported[key].list)) ||
				(detail=url.check(supported[key].detail))){
				current=key;
				type=(list) ? 'list' : 'detail';
				break;
			}
		}
	})();

	this.listAction=function(){
		return (!current || !('list' in supported[current])) ?
			false :
			current+'_list';
	};

	this.detailAction=function(){
		return (!current || !('detail' in supported[current])) ?
			false :
			current+'_detail';
	};

	this.check=function(){
		return (
			current &&
			('list' in supported[current] ||
				'detail' in supported[current])
		);
	};

	this.type=function(){
		return (this.check()) ?
			type : false;
	};

	this.name=function(){
		return current;
	}
};