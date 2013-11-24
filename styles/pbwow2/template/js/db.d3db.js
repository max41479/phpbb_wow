/**
 * Tooltip JS (C)2011 D3DB.com
 * Powers external websites with Diablo 3 Tooltips
 */
var D3DB_Tooltip = {
	
	settings : [],
	cache : [],
	styleLoaded : false,
	rateLimit : 0,
	fixed : 0,
	overLink : false,
	
	getPosition : function(e) {
	    e = e || window.event;
	    var cursor = {x:0, y:0};
	    if (e.pageX || e.pageY) {
	        cursor.x = e.pageX;
	        cursor.y = e.pageY;
	    }
	    else {
	        cursor.x = e.clientX +
	            (document.documentElement.scrollLeft ||
	            document.body.scrollLeft) -
	            document.documentElement.clientLeft;
	        cursor.y = e.clientY +
	            (document.documentElement.scrollTop ||
	            document.body.scrollTop) -
	            document.documentElement.clientTop;
	    }
	    return cursor;
	},
	
	init : function() {
		
		var scanLinks = document.links;
		for (var i=0; i<scanLinks.length; i++) {
			var url = scanLinks[i].href.split('/');
			if (url[2] != 'd3db.com' && url[2] != 'www.d3db.com' && url[2] != 'dev-01.d3db.com') continue;
			if (url[3] != 'item' && url[3] != 'skill' && url[3] != 'runestone' && url[3] != 'achievement' && url[3] != 'item_custom') continue;
			
			scanLinks[i].onmousemove = function(e) {
				D3DB_Tooltip.overLink = true
				var url = this.href.split('/');
				D3DB_Tooltip.rateLimit++;
				if (D3DB_Tooltip.rateLimit > 1 && D3DB_Tooltip.cache[url[5]] == undefined) {
					return;
				}
				
				e = D3DB_Tooltip.getPosition(e);
				D3DB_Tooltip.settings.mouseY = e.y;
				D3DB_Tooltip.settings.mouseX = e.x;
				
				if (D3DB_Tooltip.cache[url[5]] == 'false') {
					return false;
				}
					
				switch(url[3])
				{
					case 'achievement' :
						slug = url[5];
						if (D3DB_Tooltip.cache[slug] == undefined) {
							D3DB_Tooltip.addPower('achievement',slug);
						} else {
							D3DB_Tooltip.showTooltip(slug);
						}
						break;
					case 'item' :
						slug = url[5];
						if (D3DB_Tooltip.cache[slug] == undefined) {
							D3DB_Tooltip.addPower('item',slug);
						} else {
							D3DB_Tooltip.showTooltip(slug);
						}
						break;
					case 'item_custom' :
						slug = url[5];
						if (D3DB_Tooltip.cache[slug] == undefined) {
							D3DB_Tooltip.addPower('item_custom',slug);
						} else {
							D3DB_Tooltip.showTooltip(slug);
						}
						break;
					
					case 'skill' :
						slug = url[5];
						if (D3DB_Tooltip.cache[slug] == undefined) {
							D3DB_Tooltip.addPower('skill',slug);
						} else {
							D3DB_Tooltip.showTooltip(slug);
						}
						break;
					
					case 'runestone' :
						slug = url[5];
						rune = url[6];
						if (D3DB_Tooltip.cache[slug + '_' + rune] == undefined) {
							D3DB_Tooltip.addPower('runestone',slug,rune);
						} else {
							D3DB_Tooltip.showTooltip(slug + '_' + rune);
						}
						break;
				}
			}
			scanLinks[i].onmouseout = function(e) {
				D3DB_Tooltip.overLink = false
				D3DB_Tooltip.hideTooltip();
			}
		}
		
		setTimeout("D3DB_Tooltip.init()",1000);
	},
	
	loadItem : function(options) {
		if (options.tooltip == 'false') {
			this.cache[options.slug] = 'false';
			return;
		}
		this.cache[options.slug] = options.tooltip;
		
		D3DB_Tooltip.showTooltip(options.slug);
	},
	
	addPower : function(type,slug,rune) {
		
		if (this.styleLoaded == false) {
			this.styleLoaded = true;
			var head = document.getElementsByTagName('head')[0];
			var css = document.createElement('link');
			css.type = 'text/css';
			css.rel = 'stylesheet';
			css.href = 'http://dev-01.d3db.com/css/tooltip.css';
			head.appendChild(css);
		}
		
		var head= document.getElementsByTagName('head')[0];
		var script= document.createElement('script');
		script.type= 'text/javascript';
		if (rune)
			script.src = 'http://dev-01.d3db.com/power/loadtooltip/'+type+'/'+slug+'/'+rune;
		else
			script.src = 'http://dev-01.d3db.com/power/loadtooltip/'+type+'/'+slug;
		head.appendChild(script);
	},
	
	checkLoaded : function(slug) {
		if (this.cache[slug] != undefined && this.cache[slug].length > 0) {
			return true;
		} else {
			return false;
		}
	},
	
	showTooltip : function(slug) {
		var tthtml = document.getElementById('d3db_tooltip');
		if (!tthtml) {
			var tthtml = document.createElement('div');
			tthtml.style.display = 'none';
			tthtml.style.position = 'absolute';
			tthtml.style.top = '-1000px';
			tthtml.style.left = '-1000px';
			tthtml.style.zIndex = '99999';
			tthtml.setAttribute('id','d3db_tooltip');
			document.body.appendChild(tthtml);
		}
		
		tthtml.innerHTML = this.cache[slug];
		tthtml.style.display = 'block';
		
		// Browser window dimensions
		if (document.body && document.body.offsetWidth) {
		 this.settings.window_w = document.body.offsetWidth;
		 this.settings.window_h = document.body.offsetHeight;
		}
		if (document.compatMode=='CSS1Compat' &&
		    document.documentElement &&
		    document.documentElement.offsetWidth ) {
		 this.settings.window_w = document.documentElement.offsetWidth;
		 this.settings.window_h = document.documentElement.offsetHeight;
		}
		if (window.innerWidth && window.innerHeight) {
		 this.settings.window_w = window.innerWidth;
		 this.settings.window_h = window.innerHeight;
		}
		
		// Scroll positions
		if (window.pageYOffset) {
			this.settings.scroll_top = window.pageYOffset;
		} else {
			this.settings.scroll_top = (document.body.parentElement) ? document.body.parentElement.scrollTop : 0;
		}
		
		if (window.pageXOffset) {
			this.settings.scroll_top = window.pageXOffset;
		} else {
			this.settings.scroll_left = (document.body.parentElement) ? document.body.parentElement.scrollLeft : 0;
		}
		
		// Calculate current viewport space
		this.settings.viewport_top = parseInt(this.settings.mouseY - this.settings.scroll_top);
		this.settings.viewport_left = parseInt(this.settings.window_w - this.settings.mouseX);
		
		this.settings.tooltip_h = tthtml.offsetHeight;
		
		if (this.settings.tooltip_h == 0) this.settings.tooltip_h = 500;
		
		if (this.settings.tooltip_w > this.settings.viewport_left) {
			var left_offset = parseInt(this.settings.mouseX - this.settings.tooltip_w - 15);
		} else {
			var left_offset = parseInt(this.settings.mouseX + 15);
		}
		
		if (this.settings.tooltip_h > this.settings.viewport_top) {
			var top_offset = parseInt(this.settings.mouseY + 15);
		} else {
			var top_offset = parseInt(this.settings.mouseY - this.settings.tooltip_h - 15);
		}

		if (this.cache[slug] != 'false') {
			if (left_offset == undefined || left_offset == 0) left_offset = '-1000px';
			tthtml.style.position = 'absolute';
			tthtml.style.left = left_offset + 'px';
			tthtml.style.top = top_offset + 'px';
			setTimeout('document.getElementById(\'d3db_tooltip\').style.display = \'block\'',1000);
		}
		
		if (this.overLink == false) this.hideTooltip()
	},
	
	hideTooltip : function() {
		D3DB_Tooltip.rateLimit = 0;
		var tthtml = document.getElementById('d3db_tooltip');
		if (tthtml) {
			tthtml.style.position = 'absolute';
			tthtml.style.display = 'none';
			tthtml.style.top = '-1000px';
			tthtml.style.left = '-1000px';
			tthtml.innerHTML = '';
		}
	},
	
	getUriSegments : function(url) {
		this.uriSegments = url.split('/');
	}
	
}

D3DB_Tooltip.init();