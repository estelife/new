define(function(){
	var _browser;

	Array.prototype.inArray = function(needle, strict) {
		var key, found = -1,
			haystack = this;

		strict = !!strict;

		for (key in haystack) {
			if ((strict && haystack[key] === needle) || (!strict && haystack[key] == needle)) {
				found = key;
				break;
			}
		}

		return found;
	};

	return {
		scrollTarget: function() {
			return this.browser().webkit ? $('body') : $('html');
		},
		browser: function () {
			if(!_browser){
				_browser={};

				if(!jQuery.browser){
					var ua=navigator.userAgent.toLowerCase();

					var match = /(chrome)[ \/]([\w.]+)/.exec( ua ) ||
						/(webkit)[ \/]([\w.]+)/.exec( ua ) ||
						/(opera)(?:.*version|)[ \/]([\w.]+)/.exec( ua ) ||
						/(msie) ([\w.]+)/.exec( ua ) ||
						ua.indexOf("compatible") < 0 && /(mozilla)(?:.*? rv:([\w.]+)|)/.exec( ua ) ||
						[];

					var matched={
						browser: match[1] || "",
						version: match[2] || "0"
					};

					if(matched.browser){
						_browser[matched.browser]=true;
						_browser.version=matched.version;
					}

					if(_browser.chrome){
						_browser.webkit=true;
					}else if(browser.webkit){
						_browser.safari=true;
					}
				}else{
					_browser=jQuery.browser;
				}
			}

			return _browser;
		},
		getSize: function(element, isOuter) {
			if (!element.is(':visible')) {
				element.css({
					display: 'block',
					visibility: 'hidden'
				});
				var result = this.getSize(element, isOuter);
				element.css({
					display: 'none',
					visibility: 'visible'
				});
				return result;
			} else {
				return {
					width: isOuter ? element.outerWidth() : element.width(),
					height: isOuter ? element.outerHeight() : element.height()
				}
			}
		}
	}
});