//commonly used javascript functions

//determine if an array contains an element/value
function arrayContains(arr, obj) {
	for (var i = 0; i < arr.length; i++) {
		if (arr[i] === obj) {
			return true;
		}
	}
	return false;
}


//format numbers below 10 as a string with a preceding zero
function format_two_digits(n) {
	return n < 10 ? '0' + n : n;
}

// return the hours and minutes from a javascript date, in hh:mm format (uses format_two_digits)
function hhmm_format(d) {
	hours = format_two_digits(d.getHours());
	minutes = format_two_digits(d.getMinutes());
	return hours + ":" + minutes;
}

			
//getElementById shortcut
function $g(elid){
	return document.getElementById(elid);
}

//createElement shortcut
function $n(type,pnode,nclass,nid){
	var node = document.createElement(type);
	if(typeof nclass=="string") node.className = nclass;
	if(typeof nid=="string") node.id = nid;
	if(pnode!=null){
		if(typeof pnode == "string") pnode = $g(pnode);
		if(typeof pnode == "object") pnode.appendChild(node);
	}
	return node;
}

//cloneElement shortcut
function $c(cnode,pnode,nclass,nid){
	if(typeof cnode=="string") cnode = $g(cnode);
	if(typeof cnode=="object"){
		var node = cnode.cloneNode(true);
		node.id=null;
		if(typeof nclass=="string") node.className = nclass;
		if(typeof nid=="string") node.id = nid;
		if(typeof pnode=="string") pnode = $g(pnode);
		if(typeof pnode=="object") pnode.appendChild(node);
		return node;
	}
	return false;
}

//createTextNode shortcut
function $t(text,pnode){
	var node = document.createTextNode(text);
	if(typeof pnode == "string") pnode = $g(pnode);
	if(typeof pnode == "object") pnode.appendChild(node);
	
	return node;
}
//empty node
function $empty(node){
	if(typeof(node)!=="undefined"){
		while(node && node.firstChild) node.removeChild(node.firstChild);
	}
	return node;
}

//add an event handler to a node
function addHandler(obj,type, fn){
	if (obj.addEventListener)
		obj.addEventListener( type, fn, false );
	else if (obj.attachEvent){
		obj["e"+type+fn] = fn;
		obj[type+fn] = function() { obj["e"+type+fn]( window.event ); }
		obj.attachEvent( "on"+type, obj[type+fn] );
	}
}


//toggle an element's css class
function toggleCSSClass(objElement,strClass){
	if(typeof objElement == "string") objElement = $g(objElement);
	if(typeof objElement == "object"){
		if(hasCSSClass(objElement,strClass)) removeCSSClass(objElement,strClass);
		else addCSSClass(objElement,strClass);
	}
}

//add a CSS class to an element
function addCSSClass(objElement, strClass){
	if ( objElement.className ){
		var arrClasses = objElement.className.split(' ');
		
		//remove existing instances of the class
		var strUpper = strClass.toUpperCase();
		for ( var i = 0; i < arrClasses.length; i++ ){
			if ( arrClasses[i].toUpperCase() == strUpper ){
				arrClasses.splice(i, 1);
				i--;
			}
		}

		arrClasses[arrClasses.length] = strClass;
		objElement.className = arrClasses.join(' ');
	}else{  
		objElement.className = strClass;
	}
}

//remove a CSS class from an element
function removeCSSClass(objElement,strClass){
	strClass=strClass.toUpperCase();
	if(objElement.className){
		var arrClasses = objElement.className.split(' ');
		var classFound=false;
		for(var i=0;i<arrClasses.length;i++){
			if(arrClasses[i].toUpperCase()==strClass){
				arrClasses.splice(i,1);
				i--;
				classFound=true;
			}
		}
		if(classFound) objElement.className = arrClasses.join(' ');
	}
}

//check if an element has a CSS class
function hasCSSClass(objElement,strClass){
	strClass=strClass.toUpperCase();
	if(objElement.className){
		var arrClasses = objElement.className.split(' ');
		for(var i=0;i<arrClasses.length;i++){
			if(arrClasses[i].toUpperCase()==strClass) return true;
		}
	}
	return false;
}

/*
	getElementsByClassName function
	Developed by Robert Nyman, http://www.robertnyman.com
	Code/licensing: http://code.google.com/p/getelementsbyclassname/
*/
var getElementsByClassName = function (className, tag, elm){
	if (document.getElementsByClassName) {
		getElementsByClassName = function (className, tag, elm) {
			elm = elm || document;
			var elements = elm.getElementsByClassName(className),
				nodeName = (tag)? new RegExp("\\b" + tag + "\\b", "i") : null,
				returnElements = [],
				current;
			for(var i=0, il=elements.length; i<il; i+=1){
				current = elements[i];
				if(!nodeName || nodeName.test(current.nodeName)) {
					returnElements.push(current);
				}
			}
			return returnElements;
		};
	}
	else if (document.evaluate) {
		getElementsByClassName = function (className, tag, elm) {
			tag = tag || "*";
			elm = elm || document;
			var classes = className.split(" "),
				classesToCheck = "",
				xhtmlNamespace = "http://www.w3.org/1999/xhtml",
				namespaceResolver = (document.documentElement.namespaceURI === xhtmlNamespace)? xhtmlNamespace : null,
				returnElements = [],
				elements,
				node;
			for(var j=0, jl=classes.length; j<jl; j+=1){
				classesToCheck += "[contains(concat(' ', @class, ' '), ' " + classes[j] + " ')]";
			}
			try	{
				elements = document.evaluate(".//" + tag + classesToCheck, elm, namespaceResolver, 0, null);
			}
			catch (e) {
				elements = document.evaluate(".//" + tag + classesToCheck, elm, null, 0, null);
			}
			while ((node = elements.iterateNext())) {
				returnElements.push(node);
			}
			return returnElements;
		};
	}
	else {
		getElementsByClassName = function (className, tag, elm) {
			tag = tag || "*";
			elm = elm || document;
			var classes = className.split(" "),
				classesToCheck = [],
				elements = (tag === "*" && elm.all)? elm.all : elm.getElementsByTagName(tag),
				current,
				returnElements = [],
				match;
			for(var k=0, kl=classes.length; k<kl; k+=1){
				classesToCheck.push(new RegExp("(^|\\s)" + classes[k] + "(\\s|$)"));
			}
			for(var l=0, ll=elements.length; l<ll; l+=1){
				current = elements[l];
				match = false;
				for(var m=0, ml=classesToCheck.length; m<ml; m+=1){
					match = classesToCheck[m].test(current.className);
					if (!match) {
						break;
					}
				}
				if (match) {
					returnElements.push(current);
				}
			}
			return returnElements;
		};
	}
	return getElementsByClassName(className, tag, elm);
};

//get css rules
function getCSS(className,element){
	className = className.toLowerCase();
	className = className.replace(/[^a-z]+/,'');
	
	var cssRules = 'cssRules';
	if(document.all) cssRules = 'rules';
	var sheets = document.styleSheets;
	for(var i=0; i<sheets.length; i++){
		var sheet = sheets[i];
		var rules = sheet[cssRules];
		for (var j=0;j<rules.length;j++){
			var rule = rules[j];
			var cls = rule.selectorText;
			cls = cls.toLowerCase();
			cls=cls.replace(/[^a-z]+/,'');
			
			if(cls==className){
				var rs = rule.style;
				return rs[element];
			}
		}
	}
	return false;
}
//set a css rule value
function setCSS(cssClass, element, value){
	var cssRules;
	if (document.all) {
		cssRules = 'rules';
	}else if (document.getElementById) {
		cssRules = 'cssRules';
	}
	var rules = document.styleSheets[0][cssRules];
	var added = false;
	for(var i=0;i<rules.length;i++){
		var cls = rules[i].selectorText;
		if(cls==cssClass){
			var rs = rules[i].style;
			if(rs[element]){
				rs[element] = value;
				added = true;
				break;
			}
		}	
	}
	if(!added){
		if(document.styleSheets[0].insertRule){
			document.styleSheets[0].insertRule(cssClass+' { '+element+': '+value+'; }',document.styleSheets[0][cssRules].length);
		} else if (document.styleSheets[0].addRule) {
			document.styleSheets[0].addRule(cssClass,element+': '+value+';');
		}
	}
}

//initialize ajax request
function ajaxRequest(requestURL,parseFunction,params){
	//for GET requests, put the params in the URL. if the 'params' variable is present, POST will be used.
	var method = "GET";
	if(typeof(params)!="undefined") method="POST";
	var ajax;
	try{
		ajax=new XMLHttpRequest();
	}catch (e){
		try{
			ajax=new ActiveXObject("Msxml2.XMLHTTP");
		}catch (e){
			try{
				ajax=new ActiveXObject("Microsoft.XMLHTTP");
			}catch (e){
				return false;
			}
		}
	}
	
	ajax.open(method,requestURL);
	if(method=="POST"){
		ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		//ajax.setRequestHeader("Content-length", params.length);
		//ajax.setRequestHeader("Connection", "close");
	}
	ajax.onreadystatechange=function(){
		if(ajax.readyState==4){
			if(ajax.responseXML && ajax.responseXML.documentElement){
				parseFunction(ajax.responseXML.documentElement);
			}else{
				parseFunction(ajax.responseText);
			}
		}
	}
	ajax.send(params);
}

function getOffset(el) {
	var _x = 0;
	var _y = 0;
	while( el && !isNaN( el.offsetLeft ) && !isNaN( el.offsetTop ) ) {
		_x += el.offsetLeft - el.scrollLeft;
		_y += el.offsetTop - el.scrollTop;
		el = el.offsetParent;
	}
	return { top: _y, left: _x };
}

function stopProp(e){
	if(!e) var e = window.event;
	e.stopPropagation? e.stopPropagation() : e.cancelBubble = true;
}


function getMouseX(e){
	if(!e) var e = window.event;
	var x = 0;
	if (e.pageX) x = e.pageX;
	else if(e.clientX) x  = e.clientX + document.body.scrollLeft + document.documentElement.scrollLeft;
	curX=x;
	return x;
}
function getMouseY(e){
	if(!e) var e = window.event;
	var y = 0;
	if (e.pageY) y = e.pageY;
	else if(e.clientY) y  = e.clientY + document.body.scrollTop + document.documentElement.scrollTop;
	curY=y;
	return y;
}
function getMouseId(e){
	if(!e) var e = window.event;
	var id="";
	var targ;
	if (e.target) targ = e.target;
	else if (e.srcElement) targ=e.srcElement;
	if (targ.nodeType==3) targ = targ.parentNode;
	if(targ.attributes["id"]) id = targ.attributes["id"].nodeValue;
	return id;
}
function getMouseTarget(e){
	if(!e) var e = window.event;
	var targ;
	if (e.target) targ = e.target;
	else if (e.srcElement) targ=e.srcElement;
	if (targ.nodeType==3) targ = targ.parentNode;
	return targ;
}
function getMouseButton(e){
	if(!e) var e = window.event;
	var rightclick;
	if (e.which) rightclick = (e.which == 3);
	else if (e.button) rightclick = (e.button == 2);
	if(rightclick) return 2;
	return 1;
}

function setCookie(name, value, days){
	if(typeof(days)=="undefined") days=30;
	var today = new Date();
	today.setTime( today.getTime() );
	var expires_date = new Date(today.getTime() + parseInt(days * 24 * 60 *60 * 1000));
	var expires = expires_date.toGMTString();
	document.cookie=name+"="+escape(value)+";expires="+expires+";path=/";
}
function getCookie(name){
	var cookies = document.cookie.split(';');
	var cookie = '';
	var cookieName = '';
	var cookieValue = '';
	var cookieFound = false;
	
	for(i=0; i<cookies.length; i++){
		cookie = cookies[i].split('=');
		cookieName = cookie[0].replace(/^\s+|\s+$/g, '');
		if(cookieName==name){
			cookieFound=true;
			if(cookie.length>1) cookieValue = unescape( cookie[1].replace(/^\s+|\s+$/g, '') );
			return cookieValue;
		}
	}
	return null;
}
function deleteCookie(name){
	document.cookie = name+"=;path=/;expires=Thu, 01-Jan-1970 00:00:01 GMT";
}
