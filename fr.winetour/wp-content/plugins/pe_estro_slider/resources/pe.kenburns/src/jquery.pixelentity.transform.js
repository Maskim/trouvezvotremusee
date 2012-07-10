(function ($) {
	/*jslint undef: false, browser: true, devel: false, eqeqeq: false, bitwise: false, white: false, plusplus: false, regexp: false */ 
	/*global jQuery,setTimeout */

	var origin = "0px 0px";
	// nearest
	var scalingMode = "bilinear";

	// stripped down internal modernizr (css3 3d transform only)
	var Modernizr=function(a,b,c){function z(a,b){for(var d in a)if(j[a[d]]!==c)return b=="pfx"?a[d]:!0;return!1}function y(a,b){return!!~(""+a).indexOf(b)}function x(a,b){return typeof a===b}function w(a,b){return v(m.join(a+";")+(b||""))}function v(a){j.cssText=a}var d="2.0.6",e={},f=b.documentElement,g=b.head||b.getElementsByTagName("head")[0],h="modernizr",i=b.createElement(h),j=i.style,k,l=Object.prototype.toString,m=" -webkit- -moz- -o- -ms- -khtml- ".split(" "),n={},o={},p={},q=[],r=function(a,c,d,e){var g,i,j,k=b.createElement("div");if(parseInt(d,10))while(d--)j=b.createElement("div"),j.id=e?e[d]:h+(d+1),k.appendChild(j);g=["&shy;","<style>",a,"</style>"].join(""),k.id=h,k.innerHTML+=g,f.appendChild(k),i=c(k,a),k.parentNode.removeChild(k);return!!i},s,t={}.hasOwnProperty,u;!x(t,c)&&!x(t.call,c)?u=function(a,b){return t.call(a,b)}:u=function(a,b){return b in a&&x(a.constructor.prototype[b],c)};var A=function(a,c){var d=a.join(""),f=c.length;r(d,function(a,c){var d=b.styleSheets[b.styleSheets.length-1],g=d.cssRules&&d.cssRules[0]?d.cssRules[0].cssText:d.cssText||"",h=a.childNodes,i={};while(f--)i[h[f].id]=h[f];e.csstransforms3d=i.csstransforms3d.offsetLeft===9},f,c)}([,["@media (",m.join("transform-3d),("),h,")","{#csstransforms3d{left:9px;position:absolute}}"].join("")],[,"csstransforms3d"]);n.csstransforms3d=function(){var a=!!z(["perspectiveProperty","WebkitPerspective","MozPerspective","OPerspective","msPerspective"]);a&&"webkitPerspective"in f.style&&(a=e.csstransforms3d);return a};for(var B in n)u(n,B)&&(s=B.toLowerCase(),e[s]=n[B](),q.push((e[s]?"":"no-")+s));v(""),i=k=null,e._version=d,e._prefixes=m,e.testProp=function(a){return z([a])},e.testStyles=r;return e}(this,this.document);

	/*
		very stripped down version of 
		https://github.com/heygrady/transform/blob/master/README.md
	*/
	
	var rmatrix = /progid:DXImageTransform\.Microsoft\.Matrix\(.*?\)/;
	
	// Steal some code from Modernizr
	var m = document.createElement( 'modernizr' ),
		m_style = m.style;
		
	/**
	 * Find the prefix that this browser uses
	 */	
	function getVendorPrefix() {
		var property = {
			transformProperty : '',
			MozTransform : '-moz-',
			WebkitTransform : '-webkit-',
			OTransform : '-o-',
			msTransform : '-ms-'
		};
		for (var p in property) {
			if (typeof m_style[p] != 'undefined') {
				return property[p];
			}
		}
		return null;
	}
	
	function supportCssTransforms() {
		var props = [ 'transformProperty', 'WebkitTransform', 'MozTransform', 'OTransform', 'msTransform' ];
		for ( var i in props ) {
			if ( m_style[ props[i] ] !== undefined  ) {
				return true;
			}
		}
		return false;
	};
		
	// Capture some basic properties
	var vendorPrefix			= getVendorPrefix(),
		transformProperty		= vendorPrefix !== null ? vendorPrefix + 'transform' : false,
		transformOriginProperty	= vendorPrefix !== null ? vendorPrefix + 'transform-origin' : false;
	
	// store support in the jQuery Support object
	$.support.csstransforms = supportCssTransforms();
	
	$.support.hw3dTransform = (Modernizr["csstransforms3d"] && $.browser.webkit); //&& (navigator.userAgent.toLowerCase().match(/(iphone|ipod|ipad|chrome)/) !== null);
	
	// IE9 public preview 6 requires the DOM names
	if (vendorPrefix == '-ms-') {
		transformProperty = 'msTransform';
		transformOriginProperty = 'msTransformOrigin';
	}

	function transform(el,ratio,dx,dy,w,h) {
		if ($.support.csstransforms) {
			var offs;
			
			var unit="px";
			
			if (w && h && (parseInt(dx) != dx || parseInt(dy) != dy)) {
				dx=100*dx/w;
				dy=100*dy/h;
				unit="%";
			}
				
			if ($.support.hw3dTransform) {
				offs = (dx !== undefined ) ? "translate3d("+dx+unit+","+dy+unit+",0) " : "translateZ(0) ";
			} else {
				offs = (dx !== undefined ) ? "translate("+dx+unit+","+dy+unit+") " : "";
			}
			
			$(el).css(transformOriginProperty,origin).css(transformProperty,offs+"scale("+ratio+")");
			
		} else if ($.browser.msie) {
			var style = el.style;
			var matrixFilter = 'progid:DXImageTransform.Microsoft.Matrix(FilterType="'+scalingMode+'",M11='+ratio+',M12=0,M21=0,M22='+ratio+',Dx='+dx+',Dy='+dy+')';
			var filter = style.filter || $.curCSS( el, "filter" ) || "";
			style.filter = rmatrix.test(filter) ? filter.replace(rmatrix, matrixFilter) : filter ? filter + ' ' + matrixFilter : matrixFilter;
		}
	}

	$.fn.transform = function(ratio,dx,dy,w,h) {
		
		this.each(function() {
			transform(this,ratio,dx,dy,w,h);
		});
		
		return this;		 
	};	
		
})(jQuery);

		

