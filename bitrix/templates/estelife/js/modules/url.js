/**
 * Created with JetBrains PhpStorm.
 * User: dmitriy
 * Date: 28.10.13
 * Time: 16:50
 * To change this template use File | Settings | File Templates.
 */
Estelife.prototype.url=function(url_address){
	var current=url_address||location.href,
		query,pathes,block_push;

	function init(){
		var temp=current.split('?'),
			index=0;

		temp=temp[0].split('/');

		if(/^https?:\/\//.test(current))
			index=3;

		pathes=[];

		for(; index<temp.length; index++){
			if(temp[index]!=''){
				pathes.push(decodeURIComponent(temp[index]));
			}
		}
	};


	this.getQuery=function(){
		if(!query){
			query=current.split('?');
			var result={};

			if(query.length==2 && query[1] !=''){
				var temp=null;
				query=query[1].split('&');

				for(var i=0; i<query.length; i++){
					temp=query[i].split('=');

					if(temp.length==1)
						temp[1]='';

					result[temp[0]]=decodeURIComponent(temp[1]);
				}
			}

			query=result;
		}

		return query;
	};

	this.setQuery=function(field,value){
 		var temp={};

		if(typeof field=='object'){
			for(prop in field){
				if(typeof field[prop]=='function')
					continue;

				temp[prop]=field[prop];
			}
		}else{
			temp=this.getQuery();
			temp[field]=value;
		}

		query=temp;
		return query;
	};

	this.queryToString=function(){
		var temp=this.getQuery(),s=[];

		for(prop in temp)
			s.push(prop+'='+encodeURIComponent(temp[prop]));

		return s.join('&');
	};

	this.get=function(i){
		return (i!=0 && !i) ?
			pathes :
			((typeof pathes[i]!='undefined') ? pathes[i] : false);
	};

	this.current=function(){
		return '/'+pathes.join('/')+'/';
	};

	this.ln=function(){
		return pathes.length;
	};

	this.each=function(callback){
		if(!callback || typeof callback!='function')
			return false;

		for(var i=0; i<pathes.length; i++)
			callback(pathes[i]);

		return true;
	};

	this.set=function(path){
		if(typeof path=='object'){
			if(!(path instanceof EL.url))
				throw 'incorrect object for set into url: use EL.url';

			current=path.current();
			this.setQuery(path.getQuery());
		}else{
			current=path;
			query=null;
		}

		init();
	};

	this.push=function(info,replace){
		if(block_push){
			block_push=false;
			return;
		}

		var s=this.queryToString(),
			temp=(s!='') ?
				this.current().replace(/(\?(.*))*$/,'?'+s) :
				this.current(),
			state={
				'path':temp
			};

		if(info && typeof info=='object')
			$.extend(state,info);

		if(replace)
			history.replaceState(state,'',temp);
		else
			history.pushState(state,'',temp);
	};

	this.blockPush=function(block){
		block_push=block;
	};

	this.check=function(expression){
		if(typeof expression=='object' && !(expression instanceof RegExp))
			throw 'incorrect regexp for url check';
		else if(typeof expression!='string' && typeof expression!='object')
			throw 'incorrect regext for url check';

		expression=(typeof expression=='string') ?
			new RegExp(expression) : expression;

		var s=this.queryToString(),
			temp=(s!='') ?
				this.current().replace(/(\?(.*))*$/,'?'+s) :
				this.current();

		return expression.test(temp);
	};

	init();
}