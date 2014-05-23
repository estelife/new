define(function(){
	return {
		Process: function(domElement,options){
			if(typeof domElement!='object' || !(domElement instanceof Element))
				throw 'incorrect object for animation';

			var animationFrame,computedStyle,animationType,
				element=domElement,
				settings=(options && typeof options=='object') ? options : {},
				avaiProps,stop,
				queue=[],
				/** http://www.powerping.be/js/jstween/ColorTween.js **/
					hexDigit=new Array("0","1","2","3","4","5","6","7","8","9","A","B","C","D","E","F"),
				dec2hex=function(dec){return(hexDigit[dec>>4]+hexDigit[dec&15]);},
				hex2dec=function(hex){return(parseInt(hex,16))},
				rgbToHex=function(r,g,b){return ((1 << 24) + (r << 16) + (g << 8) + b).toString(16).slice(1);},
				currentObject=this;

			(function init(){
				animationFrame=window.requestAnimationFrame	||
					window.webkitRequestAnimationFrame	||
					window.mozRequestAnimationFrame	||
					window.oRequestAnimationFrame	||
					window.msRequestAnimationFrame	||
					function(callback, element){
						window.setTimeout(callback, 1000 / 60);
					};
				computedStyle=element.currentStyle||window.getComputedStyle(element, null)
				animationType=('animationType' in settings) ?
					settings.animationType : 'linear';

				if(typeof animationType=='string'){
					if(!currentObject.hasOwnProperty('Types'))
						throw 'unsupported animation type';

					animationType=currentObject.Types[animationType];
				}

				settings.duration=('duration' in settings) ?
					parseInt(settings.duration) : 1000;

				avaiProps=[
					'left',
					'top',
					'bottom',
					'right',
					'margin-bottom',
					'margin-top',
					'margin-left',
					'margin-right',
					'padding-left',
					'padding-right',
					'padding-top',
					'padding-bottom',
					'width',
					'height',
					'background-color',
					'opacity'
				];
			})();

			this.run=function(prop, to, callback, duration){
				var from = '0', matches, index,
					styles = element.currentStyle || window.getComputedStyle(element, null),
					units = '';

				to = typeof to != 'string' ? to + '' : to;

				if (!prop || (index = avaiProps.inArray(prop)) == -1)
					throw 'incorrect style property for animation';

				from = styles[prop];

				if (index != 14 && (matches = from.match(/^([\d\.]+)([\w]+)?$/))) {
					units = (matches[2]) ? matches[2] : '';
					from = (index==15) ?
						parseFloat(matches[1]).toFixed(1)*10 :
						parseInt(from);
				}else if(index==14){
					units='';

					if(matches=from.match(/^rgb\(([\d]+)[\s,]+([\d]+)[\s,]+([\d]+)\)$/)){
						from=rgbToHex(
							parseInt(matches[1]),
							parseInt(matches[2]),
							parseInt(matches[3])
						);
					}

					from.toUpperCase();
				}else
					throw 'incorrect value fot style property: '+prop;

				if(index != 14 && (matches = to.match(/(\+=|\-=)/))) {
					to = to.replace(matches[1],'');
					to = index == 15 ?
						parseFloat(to).toFixed(1)*10 :
						parseInt(to);

					switch(matches[1]) {
						case '+=':
							to=from+to;
							break;
						case '-=':
							to=from-to;
							break;
					}
				}else if(index == 14){
					to = to.replace(/^#/,'');
					to.toUpperCase();
				}else {
					to = index == 15 ?
						parseFloat(to).toFixed(1)*10 :
						parseInt(to);
				}

				duration=parseInt(duration);
				duration=(isNaN(duration)) ?
					settings.duration : duration;

				prop=avaiProps[index].replace(/\-([a-z]{1})/,function(a,b){
					return b.toUpperCase()
				});

				if(index==14){
					_animateBackground(from,to,duration,prop,callback);
				}else if(index==15){
					_animateOpacity(from,to,duration,prop,callback);
				}else{
					_animate(from,to,duration,prop,units,callback);
				}
			};

			this.stop=function(){
				stop=true;
			};

			function _animate(from,to,duration,prop,units,callback){
				var start=new Date().getTime();

				animationFrame(function(){
					if(stop){
						stop=false;
						return;
					}

					var now=(new Date().getTime())-start,
						progress=now/duration,
						result=(progress>1) ? to : (to-from)*animationType(progress)+from;

					element.style[prop]=result+units;

					if(progress<1)
						animationFrame(arguments.callee);
					else if(callback && typeof callback=='function')
						callback();
				});
			}

			function _animateBackground(from,to,duration,prop,callback){
				var start=new Date().getTime();

				animationFrame(function(){
					if(stop){
						stop=false;
						return;
					}

					var now=(new Date().getTime())-start,
						progress=now/duration,
						result=(progress>1) ? to : getColor(from, to, 100 * animationType(progress));

					element.style[prop]=result;

					if(progress<1)
						animationFrame(arguments.callee);
					else if(callback && typeof callback=='function')
						callback();
				});
			}

			function _animateOpacity(from,to,duration,prop,callback){
				var start=new Date().getTime();

				animationFrame(function(){
					if(stop){
						stop=false;
						return;
					}

					var now=(new Date().getTime())-start,
						progress=now/duration,
						result=parseFloat(
							((progress>1) ? to : (to-from)*animationType(progress)+from)
						);

					element.style[prop]=result;

					if(progress<1)
						animationFrame(arguments.callee);
					else if(callback && typeof callback=='function')
						callback();
				});
			}

			/***********************************************
			 * Function    : getColor
			 * Author      : www.JavaScript-FX.com
			 *************************************************/
			function getColor(start, end, percent){
				var r1=hex2dec(start.slice(0,2));
				var g1=hex2dec(start.slice(2,4));
				var b1=hex2dec(start.slice(4,6));

				var r2=hex2dec(end.slice(0,2));
				var g2=hex2dec(end.slice(2,4));
				var b2=hex2dec(end.slice(4,6));

				var pc = percent/100;

				r= Math.floor(r1+(pc*(r2-r1)) + .5);
				g= Math.floor(g1+(pc*(g2-g1)) + .5);
				b= Math.floor(b1+(pc*(b2-b1)) + .5);

				return("#" + dec2hex(r) + dec2hex(g) + dec2hex(b));
			}
		},
		Types: (function(){
			function linear(progress) {
				return progress;
			}

			function power(progress) {
				return Math.pow(progress,2);
			}

			function circ(progress){
				return 1-Math.sin(Math.acos(progress));
			}

			function sine(progress){
				return 1 - Math.sin((1 - progress) * Math.PI/2);
			}

			function back(progress,x){
				x=(!x) ? 1 : x;
				return Math.pow(progress, 2) * ((x + 1) * progress - x);
			}

			function bounce(progress){
				for(var a = 0, b = 1, result; 1; a += b, b /= 2) {
					if (progress >= (7 - 4 * a) / 11)
						return -Math.pow((11 - 6 * a - 11 * progress) / 4, 2) + Math.pow(b, 2);
				}
			}

			function elastic(progress,x){
				x=(!x) ? 1 : x;
				return Math.pow(2,10 * (progress - 1)) * Math.cos(20 * progress * Math.PI * x / 3);
			}

			function easeOutBounce(progress){
				function d(progress){
					for(var a = 0, b = 1, result; 1; a += b, b /= 2) {
						if (progress >= (7 - 4 * a) / 11)
							return -Math.pow((11 - 6 * a - 11 * progress) / 4, 2) + Math.pow(b, 2);
					}
				}
				return 1 - d(1 - progress);
			}

			function easeInOutBounce(progress){
				return  (progress < .5) ?
					bounce(2*progress) / 2 :
					(2 - bounce(2*(1-progress))) / 2;
			}

			function easeInOutElastic(progress,x){
				x=(!x) ? 1 : x;

				function d(progress, x) {
					return Math.pow(2,10 * (progress - 1)) * Math.cos(20 * progress * Math.PI * x / 3);
				}

				return (progress < .5) ?
					d(2 * progress, x) / 2 :
					(2 - d(2 * (1 - progress), x)) / 2;
			}

			return {
				'linear':linear,
				'power':power,
				'circ':circ,
				'sine':sine,
				'back':back,
				'bounce':bounce,
				'elastic':elastic,
				'easeOutBounce':easeOutBounce,
				'easeInOutBounce':easeInOutBounce,
				'easeInOutElastic':easeInOutElastic
			};
		})()
	}
});