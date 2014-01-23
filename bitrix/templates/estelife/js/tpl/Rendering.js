define(function(){
	return function(h,t){
		var html=h,
			templateName=t,
			cache=[],
			temp_html,temp_cache,
			registry={
				global:{},
				local:{}
			},
			object=this;

		/**
		 * Компилирует шаблон, изет инструкции, кэширует их и присваивает индексы
		 */
		(function compile(){
			var reg=/<!--[\s]*(((if|elseif|foreach)[\s]*\([^\)]+\))|(endif|else|endforeach))[\s]*!-->/gi,
				matches=html.match(reg),stack;

			if(matches){
				stack=[];
				var tag,current,temp,target;

				for(var i=0,y;i<matches.length;){
					if(!target)
						target=matches[i];

					if(/(endif|endforeach)/.test(target)){
						current=stack.pop();

						if(!current){
							throw 'not found open tag for '+target + ' in '+templateName+'['+i+']';
						}

						tag=target.replace(/(endif|endforeach)/,'$1['+current.index+']');
						html=html.replace(
							new RegExp(target.addslashes()+'?'),
							tag
						);

						current={
							'open':current.tag,
							'end':tag,
							'childs':('childs' in current) ?
								current.childs : null,
							'type':current.type,
							'cond':current.cond
						};

						if(stack.length>0){
							y=stack.length-1;

							if(!('childs' in stack[y]))
								stack[y].childs=[];

							stack[y].childs.push(current);
						}else{
							cache.push(current);
						}
					}else if(/(else|elseif)/.test(target)){
						y=stack.length-1;

						if(y<0)
							continue;

						current=stack[y];

						if(current.type!='if')
							throw 'not found open tag for '+target + ' in '+templateName+'['+i+']';

						temp=target.match(
							/<!--[\s]*(else|elseif)[\s]*(\((.*)\))?/i
						);
						temp[3]=$.trim(temp[3]);

						if(temp[1]=='else' || temp[3]==''){
							temp[3]=(current.cond.substr(0,1)=='!') ?
								current.cond.substr(1) : '!'+current.cond;
						}

						matches[i]='<!--if('+temp[3]+')!-->';

						html=html.replace(
							new RegExp(target.addslashes()+'?'),
							'<!--endif!--> '+matches[i]
						);
						target='<!--endif!-->';
						i--;

						continue;
					}else{
						tag=target.replace(/<!--[\s]*(if|foreach)/,'<!--$1['+i+']');
						html=html.replace(
							target,
							tag
						);
						temp=target.match(
							/<!--[\s]*(if|foreach)[\s]*\((.*)\)/i
						);
						stack.push({
							'tag':tag,
							'index':i,
							'type':temp[1],
							'cond':$.trim(temp[2])
						});
					}

					target=null;
					i++;
				}

				temp=null;
				tag=null;
				current=null;
				target=null;
			}

			stack=null;
			reg=null;
			matches=null;
		})();

		/**
		 * Открытый метод для осуществления агрегации данных
		 * вызывает внутренние методы для обработки инструкций
		 */
		this.aggregate=function(d){
			registry.global=d;
			registry.local={};
			temp_html=html.replace(/(\&lt;|\&gt;)/gi,function(a){
				return (a=='&lt;') ?
					'<' : '>';
			});

			temp_cache=[].concat(cache);

			temp_html=_assign(temp_html,true);
			temp_html=_aggregate(temp_cache,temp_html);
			temp_html=_replaceParams(temp_html);

			return temp_html;
		};

		/**
		 * Закрытая функция агрегации, обходит кэш инструкций
		 */
		function _aggregate(rules,temp){
			for(var i=0; i<rules.length; i++){
				if(rules[i].type=='if'){
					temp=_if(
						rules[i],
						temp
					);
				}else if(rules[i].type=='foreach'){
					temp=_foreach(
						rules[i],
						temp
					);
				}
			}
			return temp;
		}

		function _if(rule,temp){
			return (function prepare(){
				var reg=new RegExp(rule.open.addslashes()+'([\\s\\S]+)'+rule.end.addslashes());

				if(!reg.test(temp) || !_check(rule.cond)){
					temp=temp.replace(reg,'');
				}else{
					temp=temp.replace(reg,'$1');

					if(rule.childs)
						temp=_aggregate(
							rule.childs,
							temp
						);
				}

				return temp;
			})();

			function _check(cond){
				var matches;

				if(matches=cond.match(/(!)?(\$([\w\d\_\.]+)(([\-\/%\+]+)([\d]+))*)([\s]*([\!\=\<>]{1,3})[\s]*((\$?[\w\d\._]+)(([\-\/%\+]+)([\d]+))*))*/i)){
					return _checkExpr(
						matches[1],
						$.trim(matches[3]),
						$.trim(matches[5]),
						$.trim(matches[6]),
						$.trim(matches[8]),
						$.trim(matches[10]),
						$.trim(matches[12]),
						$.trim(matches[13])
					);
				}

				return false;
			}

			function _checkExpr(neg,left,lo,ld,expr,right,ro,rd){
				var result=false,
					left_val=_value(left),
					right_val=(right.substr(0,1)=='$') ?
						_value(right.substr(1)) : right;

				if(!left_val)
					return (neg=='!');

				left_val=_prepareValueForExpr(left_val,lo,ld);
				right_val=_prepareValueForExpr(right_val,ro,rd);

				if(expr  && expr!=''){
					switch(expr){
						case '==':
						case '===':
							result=(right_val && left_val==right_val);
							break;
						case '!=':
						case '!==':
							result=(right_val && left_val!=right_val);
							break;
						case '<':
							result=(right_val && left_val<right_val);
							break;
						case '>':
							result=(right_val && left_val>right_val);
							break;
						case '<=':
							result=(right_val && left_val<=right_val);
							break;
						case '>=':
							result=(right_val && left_val<=right_val);
							break;
					}
				}else{
					if(typeof left_val=='object'){
						if(!(left_val instanceof Array))
							left_val=Object.keys(left_val);

						result=(left_val.length>0);
					}else{
						result=(
							left_val &&
								left_val!='' &&
								left_val!=0 &&
								left_val!='0'
							);
					}
				}

				return (result) ?
					(neg!='!') :
					(neg=='!');
			}

			function _prepareValueForExpr(value,oper,data){
				if(oper && data){
					value=parseInt(value);
					data=parseInt(data);

					if(isNaN(data))
						data=0;

					if(isNaN(value))
						value=0;

					switch(oper){
						case '%':
							return value%data;
							break;
						case '-':
							return value-data;
							break;
						case '+':
							return value+data;
							break;
						case '/':
							return value/data;
							break;
						case '*':
							return value*data;
							break;
					}
				}

				return value;
			}
		}

		function _foreach(rule,temp){

			return (function prepare(){
				var regExp=new RegExp(rule.open.addslashes()+'([\\s\\S]+)'+rule.end.addslashes()),
					matches=temp.match(regExp);

				if(matches){
					temp=temp.replace(
						regExp,
						_prepareCicle(
							rule.cond,
							matches[1]
						)
					);
				}

				return temp;
			})();

			function _prepareCicle(cond,content){
				var matches,
					temp='';

				if(matches=cond.match(/[\s]*\$([^\s]+)[\s]*as[\s]*\$([^\s=]+)=>\$([^\s\)]+)[\s]*/)){
					var keys,
						value=_value(matches[1]);

					if(typeof value=='object'){
						if(!(value instanceof Array))
							keys=Object.keys(value);

						var length=(keys) ? keys.length : value.length,
							temp_content;

						for(var i=0; i<length; i++){
							registry.local[matches[2]]=(!keys) ? i : keys[i];
							registry.local[matches[3]]=(!keys) ? value[i] : value[keys[i]];
							temp_content=_assign(content);

							if(rule.childs){
								temp_content=_aggregate(
									rule.childs,
									temp_content
								);
							}

							if(temp_content!=''){
								temp_content=_assign(temp_content);
								temp+=_replaceParams(temp_content);
							}
						}

						delete registry.local[matches[2]];
						delete registry.local[matches[3]];
					}
				}

				return temp;
			}
		}

		function _assign(content,global){
			var matches,tm,left,right,incr,
				temp=content,
				bad=['foreach'],
				exReg=new RegExp('(<\\!--[\\s]*\\$[\\w\\d\\._]+(=[^!\\s]+|\\+\\+|\\-\\-)[\\s]*\\!-->|'+bad.join('|')+')+','gi');

			while((matches = exReg.exec(content)) != null){
				if($.inArray(matches[0],bad)>-1)
					break;

				temp=temp.replace(matches[0],'');

				if(tm=matches[0].match(/\$([\w\d\._]+)(=(\$?[^\!\s]+)|\+\+|\-\-)/)){
					left=$.trim(tm[1]);
					incr=$.trim(tm[2]);
					right=$.trim(tm[3]);

					if(incr=='++' || incr=='--'){
						if(!(left in registry.local))
							registry.local[left]=_value(left,0);

						registry.local[left]=parseInt(registry.local[left]);

						if(isNaN(registry.local[left]))
							registry.local[left]=0;

						if(incr=='++')
							registry.local[left]++;
						else
							registry.local[left]--;

						continue;
					}else if(right.substr(0,1)=='$')
						right=_value(right.substr(1));

					if(!global)
						registry.local[left]=right;
					else
						registry.global[left]=right;
				}
			}

			return temp;
		}

		function _replaceParams(temp){
			var matches=temp.match(/<\!--[\s]*\$([^!]+)[\s]*\!-->/gi);

			if(matches){
				var v,t;
				for(var i=0; i<matches.length; i++){
					if(t=matches[i].match(/\$([^!\s]+)/)){
						v=_value(t[1]);
						temp=temp.replace(matches[i],((v!==false) ? v : ''));
					}
				}
			}

			return temp;
		}

		function _value(field,def){
			field=$.trim(field);
			var data=$.extend(
				{},
				registry.global,
				registry.local
			);

			if(field.indexOf('.')>-1){
				field=field.split('.');

				for(var i=0,x=field.length-1;i<field.length;i++){
					data=(data && typeof data=='object' && field[i] in data) ?
						data[field[i]] : false;

					if(data===false)
						return def;
					else if(i==x)
						return data;
				}
			}else{
				return (field in data) ?
					data[field] : def;
			}

			return def;
		}
	}
});