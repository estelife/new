/**
 * Created with JetBrains PhpStorm.
 * User: dmitriy
 * Date: 29.10.13
 * Time: 12:02
 * To change this template use File | Settings | File Templates.
 */
Estelife.prototype.filter=function(o){
	if(typeof o!='object')
		o={};

	var options={
			'send_path': o.send_path||'/',
			'fields': o.fields||[]
		},
		events,filter,block;

	(function init(){
		events={};
		filter={};
		block=false;

		if('url' in EL){
			var url=new EL.url(),
				query=url.getQuery();

			for(f in query){
				filter[f]=query[f];
			}
		}
	})();

	this.set=function(key,value){
		filter[key]=value;
	};

	this.del=function(key){
		var temp={};

		for(p in filter){
			if(p!=key)
				temp[p]=filter[p];
		}

		filter=temp;
	};

	this.clear=function(){
		filter={};

		if('clear' in events)
			events.clear(this);
	};

	this.get=function(){
		return filter;
	};

	this.value=function(key){
		return (key in filter) ?
			filter[key] :
			false;
	};

	this.send=function(user_params){
		if(block)
			return;

		block=true;

		if(user_params && (typeof user_params=='object')){
			$.extend(
				filter,
				user_params
			);
		}

		if('before_send' in events && !events.before_send(filter))
			return;

		$.ajax({
			url:options.send_path,
			cache:false,
			data:filter,
			type:'POST',
			async:true,
			success:function(r){
				if('error' in r && 'send_error' in events){
					events.send_error(r.error);
				}else if('complete' in r && 'after_send' in events){
					events.after_send(r.complete);
				}

				block=false;
			},
			dataType:'json'
		});
	};

	this.block=function(b){
		block=b;
	}

	this.on=function(event,callback){
		if(typeof callback=='function')
			events[event]=callback;
		else
			delete events[event];
	}
};