/**
 * Created with JetBrains PhpStorm.
 * User: dmitriy
 * Date: 29.10.13
 * Time: 16:48
 * To change this template use File | Settings | File Templates.
 */
Estelife.prototype.templates=function(s){
	var settings=s||{},
		html,gen;

	function init(){
		if(!'template' in settings)
			throw 'template not set in settings';

		if(typeof t=='object'){
			if(!(t instanceof jQuery))
				throw 'incorrect template object: use jQuery object or string of template name';

			html=t.prop('outerHTML');
			gen=new EL.templateAggregator(html);
		}else{
			var temp;

			if(temp=EL.storage().getItem('tpl_'+settings.template)){
				html=temp;
				gen=new EL.templateAggregator(html);
			}else{
				var path=('path' in settings) ?
						settings.path : '/',
					params=('params' in settings) ?
						settings.params : {};

				$.extend(params,{
					'template': s.template
				});

				$.get(
					path,
					params,
					function(r){
						if($.trim(r)=='')
							throw 'loaded template is empty';

						html=r;
						gen=new EL.templateAggregator(html);

						EL.storage().setItem(
							'tpl_'+settings.template,
							html
						);
					}
				);
			}
		}
	}

	init();

	this.make=function(data){
		if(html==null){
			setTimeout(this.make,200);
		}else if(!html){
			throw 'html for setting template not found';
		}

		gen.start(data);
		gen._if();
		gen._foreach();
		gen._plain();

		return gen.end();
	};
}
Estelife.prototype.templateAggregator=function(h){
	var html=h,
		cache_if=[],
		cache_foreach=[],
		temp_html,temp_cache_if,temp_cache_foreach,data;

	(function init(){
		var reg=new RegExp('(<\\!--[\\s]*if[\\s]*\\((.*)\\)[\\s\\)]*\\!-->|<\\!--[\\s]*endif[\\s]*\\!-->)','gi'),
			matches=html.match(reg),
			stack,deep,tag,c;

		if(matches){
			stack=[];
			deep=0;

			for(var i=0; i<matches.length; i++){
				if(matches[i].indexOf('endif')>-1){
					if(deep<=0)
						continue;

					c=stack.pop();

					tag=matches[i].replace('endif','endif['+c.index+']');
					html=html.replace(
						new RegExp(matches[i]+'?'),
						tag
					);
					cache_if.push({
						'open':c.tag,
						'end':tag,
						'deep':deep
					});

					--deep;
				}else{
					++deep;
					
					tag=matches[i].replace('if','if['+i+']');
					html=html.replace(
						matches[i],
						tag
					);
					stack.push({
						'tag':tag,
						'index':i
					});
				}
			}

			cache_if=cache_if.reverse();
		}

		reg=new RegExp('(<\\!--[\\s]*foreach[\\(\\s]*[^\\)]+[\\)\\s]*\\!-->|<\\!--[\\s]*endforeach[\\s]*\\!-->)','gi');
		matches=html.match(reg);

		if(matches){
			stack=[];
			deep=0;

			for(var i=0; i<matches.length; i++){
				if(matches[i].indexOf('endforeach')>-1){
					c=stack.pop();

					tag=matches[i].replace('endforeach','endforeach['+c.index+']');
					html=html.replace(
						new RegExp(matches[i]+'?'),
						tag
					);
					cache_foreach.push({
						'open':c.tag,
						'end':tag,
						'deep':deep
					});

					--deep;
				}else{
					++deep;

					tag=matches[i].replace('foreach','foreach['+i+']');
					html=html.replace(
						matches[i],
						tag
					);
					stack.push({
						'tag':tag,
						'index':i
					});
				}
			}

			cache_foreach=cache_foreach.reverse();
		}

		stack=null;
		reg=null;
		matches=null;
	})();


	this.start=function(d){
		data=d;
		temp_html=html.replace(/(\&lt;|\&gt;)/gi,function(a){
			return (a=='&lt;') ?
				'<' : '>';
		});
		temp_cache_if=[].concat(cache_if);
		temp_cache_foreach=[].concat(cache_foreach);
	};

	this._if=function(){
		(function prepare(){
			if(temp_cache_if.length==0)
				return;

			var rule=temp_cache_if.shift(),
				reg=new RegExp(
					'('+EL.addSlashes(rule.open)+')([\\s\\S]+)'+EL.addSlashes(rule.end)
				),
				matches=temp_html.match(reg);

			if(!matches || !_check(matches[1])){
				var temp=[].concat(temp_cache_if),
					i=0;

				while(i<temp.length && temp[i].deep>1){
					delete temp_cache_if.remove(i);
					i++;
				}

				temp_html=temp_html.replace(reg,'');
			}else{
				temp_html=temp_html.replace(reg,'$2');
			}

			prepare();
		})();

		function _check(cond){
			var matches;
			if(matches=cond.match(/^<\!--[\s]*if\[[0-9]+\][\s\(]*(!)?\$([a-z_0-9\-\.]+)([\s]*[^\w\)]*)([\s]*[\w\d]*)[\s\)]*\!-->$/i)){
				return _checkExpr(
					matches[1],
					_getValue(matches[2]),
					$.trim(matches[3]),
					$.trim(matches[4])
				);
			}

			return false;
		};

		function _checkExpr(neg,value,expr,res){
			var result=false;

			if(!value)
				return (neg=='!');

			if(expr  && expr!=''){
				switch(expr){
					case '==':
					case '===':
						result=(res && value==res);
						break;
					case '!=':
					case '!==':
						result=(res && value!=res);
						break;
					case '<':
						result=(res && value<res);
						break;
					case '>':
						result=(res && value>res);
						break;
					case '<=':
						result=(res && value<=res);
						break;
					case '>=':
						result=(res && value<=res);
						break;
				}
			}else{
				result=(value && value!='' && value!=0 && value!='0');
			}

			return (result) ?
				(neg!='!') :
				(neg=='!')
		}
	};

	this._foreach=function(){
		(function prepare(){
			if(temp_cache_foreach.length==0)
				return;

			var rule=temp_cache_foreach.shift(),
				reg=new RegExp(
					'('+EL.addSlashes(rule.open)+')([\\s\\S]+)'+EL.addSlashes(rule.end)
				),
				matches=temp_html.match(reg);

			if(matches){
				temp_html=temp_html.replace(
					reg,
					_createContent(
						matches[1],
						matches[2]
					)
				);
			}

			prepare();
		})();

		function _createContent(fe,cn){
			var matches;
			if(matches=fe.match(/\(\$([^\s]+)[\s]*as[\s]*\$([^\s=]+)=>\$([^\s\)]+)\)/)){
				var value=_getValue(matches[1]);

				if(typeof value!='object'){
					return '';
				}else{
					return _replaceContent(
						matches[2],
						matches[3],
						value,
						cn
					);
				}
			}

			return '';
		}

		function _replaceContent(ck,cv,value,cn){
			var ncn='',temp;

			if(typeof value!='object'){
				ncn=cn.replace(
					new RegExp('<\\!--[\\s]*(\\$'+cv+'|\\$'+cv+'\\['+ck+'\\])[\\s]*\\!-->'),
					value
				);
			}else if(value instanceof Array){
				for(var key=0; key<value.length; key++){
					if(typeof value[key]=='object'){
						ncn+=_replaceContent(ck,cv,value[key],cn);
					}else{
						ncn+=_replaceContent(ck,cv,value[key],cn);
					}
				}
			}else{
				ncn=cn;
				for(key in value){
					ncn=ncn.replace(
						new RegExp('<\\!--[\\s]*(\\$'+cv+'\\.'+key+'|\\$'+ck+')[\\s]*\\!-->'),
						function(a,b){
							if(b=='$'+ck){
								return key;
							}else{
								return value[key];
							}
						}
					);
				}
			}
			return ncn;
		}

	};

	this._plain=function(){
		var reg;

		for(prop in data){
			reg=new RegExp(
				'<\\!--[\\s]*\\$'+prop+'[\\s]*\\!-->',
				'gi'
			);
			temp_html=temp_html.replace(reg,data[prop]);
		}
	};

	this.end=function(){
		temp_cache_foreach=null;
		temp_cache_if=null;
		return temp_html;
	};

	function _getValue(field){
		if(field.indexOf('.')>-1){
			field=field.split('.');
			var object=data;

			for(var i=0,x=field.length-1;i<field.length;i++){
				object=(field[i] in object) ?
					object[field[i]] : false;

				if(!object)
					return false;
				else if(i==x)
					return object[field];
			}
		}else{
			return (field in data) ?
				data[field] : false;
		}
	}
}