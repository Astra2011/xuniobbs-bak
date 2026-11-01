/*
* xunio.js 封装了部分 PHP 常用的函数，便于代码移植和使用。
* 技术支持： http://bbs.xiuno.com/
*/

/********************* 对 window 对象进行扩展 ************************/
// 兼容 ie89
if(!Object.keys) {
	Object.keys = function(o) {
		var arr = [];
		for(var k in o) {
			if(o.hasOwnProperty(k)) arr.push(k);
		}
		return arr;
	}
}
if(!Object.values) {
	Object.values = function(o) {
		var arr = [];
		if(!o) return arr;
		for(var k in o) {
			if(o.hasOwnProperty(k)) arr.push(o[k]);
		}
		return arr;
	}
}
Array.values = function(arr) {
	return xo.array_filter(arr);
};

Object.first = function(obj) {
	for(var k in obj) return obj[k];
};
Object.last = function(obj) {
	for(var k in obj);
	return obj[k];
};
Object.length = function(obj) {
	var n = 0;
	for(var k in obj) n++;
	return n;
};
Object.count = function(obj) {
	if(!obj) return 0;
	if(obj.length) return obj.length;
	var n = 0;
	for(k in obj) {
		if(obj.hasOwnProperty(k)) n++;
	}
	return n;
};
Object.sum = function(obj) {
	var sum = 0;
	$.each(obj, function(k, v) {sum += intval(v)});
	return sum;
};
if(typeof console == 'undefined') {
	console = {};
	console.log = function() {};
}

/********************* xo 模拟 php 函数 ************************/

// var xo = window; // browser， 如果要兼容以前的版本，请开启这里。
// var xo = global; // nodejs
var xo = {}; // 避免冲突，自己的命名空间。

// 针对国内的山寨套壳浏览器检测不准确
xo.is_ie = (!!document.all) ? true : false;// ie6789
xo.is_ie_10 = navigator.userAgent.indexOf('Trident') != -1;
xo.is_ff = navigator.userAgent.indexOf('Firefox') != -1;
xo.in_mobile = ($(window).width() < 1140);
xo.options = {}; // 全局配置


xo.htmlspecialchars = function(s) {
	s = s.replace(/</g, "&lt;");
	s = s.replace(/>/g, "&gt;");
	return s;
};

// 标准的 urlencode()
xo._urlencode = function(s) {
	s = encodeURIComponent(s);
	s = xo.strtolower(s);
	return s;
};

// 标准的 urldecode()
xo._urldecode = function(s) {
	s = decodeURIComponent(s);
	return s;
};

xo.urlencode = function(s) {
	s = encodeURIComponent(s);
	s = s.replace(/_/g, "%5f");
	s = s.replace(/\-/g, "%2d");
	s = s.replace(/\./g, "%2e");
	s = s.replace(/\~/g, "%7e");
	s = s.replace(/\!/g, "%21");
	s = s.replace(/\*/g, "%2a");
	s = s.replace(/\(/g, "%28");
	s = s.replace(/\)/g, "%29");
	//s = s.replace(/\+/g, "%20");
	s = s.replace(/\%/g, "_");
	return s;
};

xo.urldecode = function(s) {
	s = s.replace(/_/g, "%");
	s = decodeURIComponent(s);
	return s;
};

// 兼容 3.0
xo.xo_urlencode = xo.urlencode_safe;
xo.xo_urldecode = xo.urldecode_safe;

xo.nl2br = function(s) {
	s = s.replace(/\r\n/g, "\n");
	s = s.replace(/\n/g, "<br>");
	s = s.replace(/\t/g, "&nbsp; &nbsp; &nbsp; &nbsp; ");
	return s;
};

xo.time = function() {
	return xo.intval(Date.now() / 1000);
};

xo.intval = function(s) {
	var i = parseInt(s);
	return isNaN(i) ? 0 : i;
};

xo.floatval = function(s) {
	if(!s) return 0;
	if(s.constructor === Array) {
		for(var i=0; i<s.length; i++) {
			s[i] = xo.floatval(s[i]);
		}
		return s;
	}
	var r = parseFloat(s);
	return isNaN(r) ? 0 : r;
};

xo.isset = function(k) {
	var t = typeof k;
	return t != 'undefined' && t != 'unknown';
};

xo.empty = function(s) {
	if(s == '0') return true;
	if(!s) {
		return true;
	} else {
		//$.isPlainObject
		if(s.constructor === Object) {
			return Object.keys(s).length == 0;
		} else if(s.constructor === Array) {
			return s.length == 0;
		}
		return false;
	}
};

xo.ceil = Math.ceil;
xo.round = Math.round;
xo.floor = Math.floor;
xo.f2y = function(i, callback) {
	if(!callback) callback = round;
	var r = i / 100;
	return callback(r);
};
xo.y2f = function(s) {
	var r = xo.round(xo.intval(s) * 100);
	return r;
};
xo.strtolower = function(s) {
	s += '';
	return s.toLowerCase();
};
xo.strtoupper = function(s) {
	s += '';
	return s.toUpperCase();
};

xo.json_type = function(o) {
	var _toS = Object.prototype.toString;
	var _types = {
		'undefined': 'undefined',
		'number': 'number',
		'boolean': 'boolean',
		'string': 'string',
		'[object Function]': 'function',
		'[object RegExp]': 'regexp',
		'[object Array]': 'array',
		'[object Date]': 'date',
		'[object Error]': 'error'
	};
	return _types[typeof o] || _types[_toS.call(o)] || (o ? 'object' : 'null');
};

xo.json_encode = function(o) {
	var json_replace_chars = function(chr) {
		var specialChars = { '\b': '\\b', '\t': '\\t', '\n': '\\n', '\f': '\\f', '\r': '\\r', '"': '\\"', '\\': '\\\\' };
		return specialChars[chr] || '\\u00' + Math.floor(chr.charCodeAt() / 16).toString(16) + (chr.charCodeAt() % 16).toString(16);
	};

	var s = [];
	switch (xo.json_type(o)) {
		case 'undefined':
			return 'undefined';
			break;
		case 'null':
			return 'null';
			break;
		case 'number':
		case 'boolean':
		case 'date':
		case 'function':
			return o.toString();
			break;
		case 'string':
			return '"' + o.replace(/[\x00-\x1f\\"]/g, json_replace_chars) + '"';
			break;
		case 'array':
			for (var i = 0, l = o.length; i < l; i++) {
				s.push(xo.json_encode(o[i]));
			}
			return '[' + s.join(',') + ']';
			break;
		case 'error':
		case 'object':
			for (var p in o) {
				s.push('"' + p + '"' + ':' + xo.json_encode(o[p]));
			}
			return '{' + s.join(',') + '}';
			break;
		default:
			return '';
			break;
	}
};

xo.json_decode = function(s) {
	if(!s) return null;
	try {
		// 去掉广告代码。这行代码挺无语的，为了照顾国内很多人浏览器中广告病毒的事实。
		// s = s.replace(/\}\s*<script[^>]*>[\s\S]*?<\/script>\s*$/ig, '}');
		if(s.match(/^<!DOCTYPE/i)) return null;
		var json = JSON.parse(s);
		return json;
	} catch(e) {
		//alert('JSON格式错误：' + s);
		//window.json_error_string = s;	// 记录到全局
		return null;
	}
};

xo.clone = function(obj) {
        return xo.json_decode(xo.json_encode(obj));
}

// 方便移植 PHP 代码
xo.min = function() {return Math.min.apply(this, arguments);}
xo.max = function() {return Math.max.apply(this, arguments);}
xo.str_replace = function(s, d, str) {var p = new RegExp(s, 'g'); return str.replace(p, d);}
xo.strrpos = function(str, s) {return str.lastIndexOf(s);}
xo.strpos = function(str, s) {return str.indexOf(s);}
xo.substr = function(str, start, len) {
	// 支持负数
	if(!str) return '';
	var end = length;
	var length = str.length;
	if(start < 0) start = length + start;
	if(!len) {
		end = length;
	} else if(len > 0) {
		end = start + len;
	} else {
		end = length + len;
	}
	return str.substring(start, end);
};
xo.explode = function(sep, s) {return s.split(sep);}
xo.implode = function(glur, arr) {return arr.join(glur);}
xo.array_merge = function(arr1, arr2) {return arr1 && arr1.__proto__ === Array.prototype && arr2 && arr2.__proto__ === Array.prototype ? arr1.concat(arr2) : $.extend(arr1, arr2);}
// 比较两个数组的差异，在 arr1 之中，但是不在 arr2 中。返回差异结果集的新数组，
xo.array_diff = function(arr1, arr2) {
	if(arr1.__proto__ === Array.prototype) {
		var o = {};
		for(var i = 0, len = arr2.length; i < len; i++) o[arr2[i]] = true;
		var r = [];
		for(i = 0, len = arr1.length; i < len; i++) {
			var v = arr1[i];
			if(o[v]) continue;
			r.push(v);
		}
		return r;
	} else {
		var r = {};
		for(k in arr1) {
			if(!arr2[k]) r[k] = arr1[k];
		}
		return r;
	}
};
// 过滤空值，可以用于删除
/*
	// 第一种用法：
	var arr = [0,1,2,3];
	delete arr[1];
	delete arr[2];
	arr = array_filter(arr);
	
	// 第二种：
	var arr = [0,1,2,3];
	array_filter(arr, function(k,v) { k == 1} );
*/
xo.array_filter = function(arr, callback) {
	var newarr = [];
	for(var k in arr) {
		var v = arr[k];
		if(callback && callback(k, v)) continue;
		// if(!callback && v === undefined) continue; // 默认过滤空值
		newarr.push(v);
	}
	return newarr;
};
xo.array_keys = function(obj) {
	var arr = [];
	$.each(obj, function(k) {arr.push(k);});
	return arr;
};
xo.array_values = function(obj) {
	var arr = [];
	$.each(obj, function(k, v) {arr.push(v);});
	return arr;
};
xo.in_array = function(v, arr) { return $.inArray(v, arr) != -1;}

xo.rand = function(n) {
	var str = 'ABCDEFGHJKMNPQRSTWXYZabcdefhijkmnprstwxyz2345678';
	var r = '';
	for (i = 0; i < n; i++) {
		r += str.charAt(Math.floor(Math.random() * str.length));
	}
	return r;
};

xo.random = function(min, max) {
	var num = Math.random()*(max-min + 1) + min;
	var r = Math.ceil(num);
	return r;
};

// 所谓的 js 编译模板，不过是一堆效率低下的正则替换，这种东西根据自己喜好用吧。
xo.template = function(s, json) {
	//console.log(json);
	for(k in json) {
		var r = new RegExp('\{('+k+')\}', 'g');
		s = s.replace(r, function(match, name) {
			return json[name];
		});
	}
	return s;
};

xo.is_mobile = function(s) {
	var r = /^\d{11}$/;
	if(!s) {
		return false;
	} else if(!r.test(s)) {
		return false;
	}
	return true;
};

xo.is_email = function(s) {
	var r = /^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/i
	if(!s) {
		return false;
	} else if(!r.test(s)) {
		return false;
	}
	return true;
};

xo.is_string = function(obj) {return Object.prototype.toString.apply(obj) == '[object String]';};
xo.is_function = function(obj) {return Object.prototype.toString.apply(obj) == '[object Function]';};
xo.is_array = function(obj) {return Object.prototype.toString.apply(obj) == '[object Array]';};
xo.is_number = function(obj) {return Object.prototype.toString.apply(obj) == '[object Number]' || /^\d+$/.test(obj);};
xo.is_regexp = function(obj) {return Object.prototype.toString.apply(obj) == '[object RegExp]';};
xo.is_object = function(obj) {return Object.prototype.toString.apply(obj) == '[object Object]';};
xo.is_element = function(obj) {return !!(obj && obj.nodeType === 1);};

xo.lang = function(key, arr) {
	var r = lang[key] ? lang[key] : "lang["+key+"]";
	if(arr) {
		$.each(arr, function(k, v) { r = xo.str_replace("{"+k+"}", v, r);});	
	}
	return r;
};

/* 
	js 版本的翻页函数
*/
// 用例：pages('user-list-{page}.htm', 100, 10, 5);
xo.pages = function (url, totalnum, page, pagesize) {
	if(!page) page = 1;
	if(!pagesize) pagesize = 20;
	var totalpage = xo.ceil(totalnum / pagesize);
	if(totalpage < 2) return '';
	page = xo.min(totalpage, page);
	var shownum = 5;	// 显示多少个页 * 2

	var start = xo.max(1, page - shownum);
	var end = xo.min(totalpage, page + shownum);

	// 不足 $shownum，补全左右两侧
	var right = page + shownum - totalpage;
	if(right > 0) start = xo.max(1, start -= right);
	left = page - shownum;
	if(left < 0) end = xo.min(totalpage, end -= left);

	var s = '';
	if(page != 1) s += '<a href="'+xo.str_replace('{page}', page-1, url)+'">◀</a>';
	if(start > 1) s += '<a href="'+xo.str_replace('{page}', 1, url)+'">1 '+(start > 2 ? '... ' : '')+'</a>';
	for(i=start; i<=end; i++) {
		if(i == page) {
			s += '<a href="'+xo.str_replace('{page}', i, url)+'" class="active">'+i+'</a>';// active
		} else {
			s += '<a href="'+xo.str_replace('{page}', i, url)+'">'+i+'</a>';
		}
	}
	if(end != totalpage) s += '<a href="'+xo.str_replace('{page}', totalpage, url)+'">'+(totalpage - end > 1 ? '... ' : '')+totalpage+'</a>';
	if(page != totalpage) s += '<a href="'+xo.str_replace('{page}', page+1, url)+'">▶</a>';
	return s;
};

xo.parse_url = function(url) {
	if(url.match(/^(([a-z]+):)\/\//i)) {
		var arr = url.match(/^(([a-z]+):\/\/)?([^\/\?#]+)\/*([^\?#]*)\??([^#]*)#?(\w*)$/i);
		if(!arr) return null;
		var r = {
			'schema': arr[2],
			'host': arr[3],
			'path': arr[4],
			'query': arr[5],
			'anchor': arr[6],
			'requesturi': arr[4] + (arr[5] ? '?'+arr[5] : '') + (arr[6] ? '#'+arr[6] : '')
		};
		console.log(r);
		return r;
	} else {
		
		var arr = url.match(/^([^\?#]*)\??([^#]*)#?(\w*)$/i);
		if(!arr) return null;
		var r = {
			'schema': '',
			'host': '',
			'path': arr[1],
			'query': arr[2],
			'anchor': arr[3],
			'requesturi': arr[1] + (arr[2] ? '?'+arr[2] : '')  + (arr[3] ? '#'+arr[3] : '')
		};
		console.log(r);
		return r;
	}
};

xo.parse_str = function (str){
	var sep1 = '=';
	var sep2 = '&';
	var arr = str.split(sep2);
	var arr2 = {};
	for(var x=0; x < arr.length; x++){
		var tmp = arr[x].split(sep1);
		arr2[unescape(tmp[0])] = unescape(tmp[1]).replace(/[+]/g, ' ');
	}
	return arr2;
};

// 解析 url 参数获取 $_GET 变量
xo.parse_url_param = function(url) {
	var arr = xo.parse_url(url);
	var q = arr.path;
	var pos = xo.strrpos(q, '/');
	q = xo.substr(q, pos + 1);
	var r = [];
	if(xo.substr(q, -4) == '.htm') {
		q = xo.substr(q, 0, -4);
		r = xo.explode('-', q);
	// 首页
	} else if (url && url != window.location && url != '.' && url != '/' && url != './'){
		r = ['thread', 'seo', url];
	}

	// 将 xxx.htm?a=b&c=d 后面的正常的 _GET 放到 $_SERVER['_GET']
	if(!empty(arr['query'])) {
		var arr2 = xo.parse_str(arr['query']);
		r = xo.array_merge(r, arr2);
	}
	return r;
};

// 从参数里获取数据
xo.param = function(key) {

};

// 模拟服务端 url() 函数

xo.url = function(u, url_rewrite) {
	var on = window.url_rewrite_on || url_rewrite;
	if(xo.strpos(u, '/') != -1) {
		var path = xo.substr(u, 0, xo.strrpos(u, '/') + 1);
		var query = xo.substr(u, xo.strrpos(u, '/') + 1);
	} else {
		var path = '';
		var query = u;
	}
	var r = '';
	if(!on) {
		r = path + '?' + query + '.htm';
	} else if(on == 1) {
		r = path + query + ".htm";
	} else if(on == 2) {
		r = path + '?' + xo.str_replace('-', '/', query);
	} else if(on == 3) {
		r = path + xo.str_replace('-', '/', query);
	}
	return r;
};

// 将参数添加到 URL
xo.url_add_arg = function(url, k, v) {
	var pos = xo.strpos(url, '.htm');
	if(pos === false) {
		return xo.strpos(url, '?') === false ? url + "&" + k + "=" + v :  url + "?" + k + "=" + v;
	} else {
		return xo.substr(url, 0, pos) + '-' + v + xo.substr(url, pos);
	}
};

// 页面跳转的时间
//xo.jumpdelay = xo.debug ? 20000000 : 2000;


/********************* 对 JQuery 进行扩展 ************************/

$.location = function(url, seconds) {
	if(seconds === undefined) seconds = 1;
	setTimeout(function() {window.location='./';}, seconds * (debug ? 2000 : 1000));
};

// 二级数组排序
/*Array.prototype.proto_sort = Array.prototype.sort;
Array.prototype.sort = function(arg) {
	if(arg === undefined) {
		return this.proto_sort();
	} else if(arg.constructor === Function) {
		return this.proto_sort(arg);
	} else if(arg.constructor === Object) {
		var k = Object.first(arg);
		var v = arg[k];
		return this.proto_sort(function(a, b) {return v == 1 ? a[k] > b[k] : a[k] < b[k];});
	} else {
		return this;
	}
}*/

xo.arrlist_values = function(arrlist, key) {
	var r = [];
	arrlist.map(function(arr) { r.push(arr[key]); });
	return r;
};

xo.arrlist_key_values = function(arrlist, key, val, pre) {
	var r = {};
	var pre = pre || '';
	arrlist.map(function(arr) { r[arr[pre+key]] = arr[val]; });
	return r;
};

xo.arrlist_keep_keys = function(arrlist, keys) {
	if(!xo.is_array(keys)) keys = [keys];
	for(k in arrlist) {
		var arr = arrlist[k];
		var newarr = {};
		for(k2 in keys) {
			var key = keys[k2];
			newarr[key] = arr[key];
		}
		arrlist[k] = newarr;
	}
	return arrlist;
}
/*var arrlist = [
	{uid:1, gid:3},
	{uid:2, gid:2},
	{uid:3, gid:1},
];
var arrlist2 = xo.arrlist_keep_keys(arrlist, 'gid');
console.log(arrlist2);*/

xo.arrlist_multisort = function(arrlist, k, asc) {
	var arrlist = arrlist.sort(function(a, b) {
		if(a[k] == b[k]) return 0;
		var r = a[k] > b[k];
		r = asc ? r : !r;
		return r ? 1 : -1;
	});
	return arrlist;
}
/*
var arrlist = [
	{uid:1, gid:3},
	{uid:2, gid:2},
	{uid:3, gid:1},
];
var arrlist2 = xo.arrlist_multisort(arrlist, 'gid', false);
console.log(arrlist2);
*/

// if(xo.is_ie) document.documentElement.addBehavior("#default#userdata");

$.pdata = function(key, value) {
	var r = '';
	if(typeof value != 'undefined') {
		value = xo.json_encode(value);
	}

	// HTML 5
	try {
		// ie10 需要 try 一下
		if(window.localStorage){
			if(typeof value == 'undefined') {
				r = localStorage.getItem(key);
				return xo.json_decode(r);
			} else {
				return localStorage.setItem(key, value);
			}
		}
	} catch(e) {}

	// HTML 4
	if(xo.is_ie && (!document.documentElement || typeof document.documentElement.load == 'unknown' || !document.documentElement.load)) {
		return '';
	}
	// get
	if(typeof value == 'undefined') {
		if(xo.is_ie) {
			try {
				document.documentElement.load(key);
				r = document.documentElement.getAttribute(key);
			} catch(e) {
				//alert('$.pdata:' + e.message);
				r = '';
			}
		} else {
			try {
				r = sessionStorage.getItem(key) && sessionStorage.getItem(key).toString().length == 0 ? '' : (sessionStorage.getItem(key) == null ? '' : sessionStorage.getItem(key));
			} catch(e) {
				r = '';
			}
		}
		return xo.json_decode(r);
	// set
	} else {
		if(xo.is_ie){
			try {
				// fix: IE TEST for ie6 崩溃
				document.documentElement.load(key);
				document.documentElement.setAttribute(key, value);
				document.documentElement.save(key);
				return  document.documentElement.getAttribute(key);
			} catch(error) {/*alert('setdata:'+error.message);*/}
		} else {
			try {
				return sessionStorage.setItem(key, value);
			} catch(error) {/*alert('setdata:'+error.message);*/}
		}
	}
};

// time 单位为秒，与php setcookie, 和  misc::setcookie() 的 time 参数略有差异。
$.cookie = function(name, value, time, path) {
	if(typeof value != 'undefined') {
		if (value === null) {
			var value = '';
			var time = -1;
		}
		if(typeof time != 'undefined') {
			date = new Date();
			date.setTime(date.getTime() + (time * 1000));
			var time = '; expires=' + date.toUTCString();
		} else {
			var time = '';
		}
		var path = path ? '; path=' + path : '';
		//var domain = domain ? '; domain=' + domain : '';
		//var secure = secure ? '; secure' : '';
		document.cookie = name + '=' + encodeURIComponent(value) + time + path;
	} else {
		var v = '';
		if(document.cookie && document.cookie != '') {
			var cookies = document.cookie.split(';');
			for(var i = 0; i < cookies.length; i++) {
				var cookie = cookies[i].trim();
				if(cookie.substring(0, name.length + 1) == (name + '=')) {
					v = decodeURIComponent(cookie.substring(name.length + 1)) + '';
					break;
				}
			}
		}
		return v;
	}
};


// 改变Location URL ?
$.xget = function(url, callback, retry) {
	if(retry === undefined) retry = 1;
	$.ajax({
		type: 'GET',
		url: url,
		dataType: 'text',
		timeout: 15000,
		xhrFields: {withCredentials: true},
		success: function(r){
			if(!r) return callback(-100, 'Server Response Empty!');
			var s = xo.json_decode(r);
			if(!s) {
				return callback(-101, r); // 'Server Response xo.json_decode() failed：'+
			}
			if(s.code === undefined) {
				if($.isPlainObject(s)) {
					return callback(0, s);
				} else {
					return callback(-102, r); // 'Server Response Not JSON 2：'+
				}
			} else if(s.code == 0) {
				return callback(0, s.message);
			//系统错误
			} else if(s.code < 0) {
				return callback(s.code, s.message);
			//业务逻辑错误
			} else {
				return callback(s.code, s.message);
			
			}
		},
		// 网络错误，重试
		error: function(xhr, type) {
			if(retry > 1) {
				$.xget(url, callback, retry - 1);
			} else {
				if((type != 'abort' && type != 'error') || xhr.status == 403 || xhr.status == 404) {
					return callback(-1000, "xhr.responseText:"+xhr.responseText+', type:'+type);
				} else {
					console.log("xhr.responseText:"+xhr.responseText+', type:'+type);
					return callback(-1001, "xhr.responseText:"+xhr.responseText+', type:'+type);
				}
			}
		}
	});
};

// ajax progress plugin
(function($, window, undefined) {
	//is onprogress supported by browser?
	var hasOnProgress = ("onprogress" in $.ajaxSettings.xhr());

	//If not supported, do nothing
	if (!hasOnProgress) {
		return;
	}
	
	//patch ajax settings to call a progress callback
	var oldXHR = $.ajaxSettings.xhr;
	$.ajaxSettings.xhr = function() {
		var xhr = oldXHR();
		if(xhr instanceof window.XMLHttpRequest) {
			xhr.addEventListener('progress', this.progress, false);
		}
		
		if(xhr.upload) {
			xhr.upload.addEventListener('progress', this.progress, false);
		}
		
		return xhr;
	};
})(jQuery, window);

$.xpost = function(url, postdata, callback, progress_callback) {
	if(typeof postdata === 'function') {
		callback = postdata;
		postdata = null;
	}
	
	$.ajax({
		type: 'POST',
		url: url,
		data: postdata,
		dataType: 'text',
		timeout: 6000000,
		progress: function(e) {
			if (e.lengthComputable) {
				if(progress_callback) progress_callback(e.loaded / e.total * 100);
				//console.log('progress1:'+e.loaded / e.total * 100 + '%');
			}
		},
		success: function(r){
			if(!r) return callback(-1, 'Server Response Empty!');
			var s = xo.json_decode(r);
			if(!s || s.code === undefined) return callback(-1, 'Server Response Not JSON：'+r);
			if(s.code == 0) {
				return callback(0, s.message);
			//系统错误
			} else if(s.code < 0) {
				return callback(s.code, s.message);
			} else {
				return callback(s.code, s.message);
			}
		},
		error: function(xhr, type) {
			if(type != 'abort' && type != 'error' || xhr.status == 403) {
				return callback(-1000, "xhr.responseText:"+xhr.responseText+', type:'+type);
			} else {
				console.log("xhr.responseText:"+xhr.responseText+', type:'+type);
				return callback(-1001, "xhr.responseText:"+xhr.responseText+', type:'+type);
			}
		}
	});
};

/*
	功能：
		异步加载 js, 加载成功以后 callback
	用法：
		$.require('1.js', '2.js', function() {
			alert('after all loaded');
		});
		$.require(['1.js', '2.js' function() {
			alert('after all loaded');
		}]);
*/
// 区别于全局的 node.js require 关键字
$.required = [];
$.require = function() {
	var args = null;
	if(arguments[0] && typeof arguments[0] == 'object') { // 如果0 为数组
		args = arguments[0];
		if(arguments[1]) args.push(arguments[1]);
	} else {
		args = arguments;
	}
	this.load = function(args, i) {
		var _this = this;
		if(args[i] === undefined) return;
		if(typeof args[i] == 'string') {
			var js = args[i];
			// 避免重复加载
			if($.inArray(js, $.required) != -1) {
				if(i < args.length) this.load(args, i+1);
				return;
			}
			$.required.push(js);
			var script = document.createElement("script");
			script.src = js;
			script.onerror = function() {
				console.log('script load error:'+js);
				_this.load(args, i+1);
			};
			if(xo.is_ie) {
				script.onreadystatechange = function() {
					if(script.readyState == 'loaded' || script.readyState == 'complete') {
						_this.load(args, i+1);
						script.onreadystatechange = null;
					}
				};
			} else {
				script.onload = function() { _this.load(args, i+1); };
			}
			document.getElementsByTagName('head')[0].appendChild(script);
		} else if(typeof args[i] == 'function'){
			var f = args[i];
			f();
			if(i < args.length) this.load(args, i+1);
		} else {
			_this.load(args, i+1);
		}
	};
	this.load(args, 0);
};

$.require_css = function(filename) {
	// 判断重复加载
	var tags = document.getElementsByTagName('link');
	for(var i=0; i<tags.length; i++) {
		if(tags[i].href.indexOf(filename) != -1) {
			return false;
		}
	}
	
	var link = document.createElement("link");
	link.rel = "stylesheet";
	link.type = "text/css";
	link.href = filename;
	document.getElementsByTagName('head')[0].appendChild(link);
};

// 在节点上显示 loading 图标
$.fn.loading = function(action) {
	return this.each(function() {
		var jthis = $(this);
		jthis.css('position', 'relative');
		if(!this.jloading) this.jloading = $('<div class="loading"><img src="static/loading.gif" /></div>').appendTo(jthis);
		var jloading = this.jloading.show();
		if(!action) {
			var offset = jthis.position();
			var left = offset.left;
			var top = offset.top;
			var w = jthis.width();
			var h = xo.min(jthis.height(), $(window).height());
			var left = w / 2 - jloading.width() / 2;
			var top = (h / 2 -  jloading.height() / 2) * 2 / 3;
			jloading.css('position', 'absolute').css('left', left).css('top', top);
		} else if(action == 'close') {
			jloading.remove();
			this.jloading = null;
		}
	});
};

// 对图片进行缩略，裁剪，然后 base64 存入 form 隐藏表单，name 与 file 控件相同
// 上传过程中，禁止 button，对图片可以缩略
$.fn.base64_encode_file = function(width, height, action) {
	var action = action || 'thumb';
	var jform = $(this);
	var jsubmit = jform.find('input[type="submit"]');
	jform.on('change', 'input[type="file"]', function(e) {
		var jfile = $(this);
		var jassoc = jfile.data('assoc') ? $('#'+jfile.data('assoc')) : null;
		var obj = e.target;
		jsubmit.button('disabled');
		var file = obj.files[0];

       		// 创建一个隐藏域，用来保存 base64 数据
		var jhidden = $('<input type="hidden" name="'+obj.name+'" />').appendTo(jform);
		obj.name = '';

		var reader = new FileReader();
		reader.readAsDataURL(file);
		reader.onload = function(e) {
			// 如果是图片，并且设置了，宽高，和剪切模式
			if(width && height && xo.substr(this.result, 0, 10) == 'data:image') {
				xo.image_resize(this.result, function(code, message) {
					if(code == 0) {
						if(jassoc) jassoc.attr('src', message.data);
						jhidden.val(message.data); // base64
					} else {
						alert(message);
					}
					jsubmit.button('reset');
				}, {width: width, height: height, action: action});
			} else {
				if(jassoc) jassoc.attr('src', this.result);
				jhidden.val(this.result);
				jsubmit.button('reset');
			}
		}
	});
};

xo.base64_data_image_type = function(s) {
	//data:image/png;base64
	r = s.match(/^data:image\/(\w+);/i);
	return r[1];
};

// 图片背景透明算法 by axiuno@gmail.com，只能处理小图片，效率做过改进，目前速度还不错。
xo.image_background_opacity = function(data, width, height, callback) {
	var x = 0;
	var y = 0;
	//var map = {}; // 图片的状态位： 0: 未检测，1:检测过是背景，2：检测过不是背景
	//var unmap = {}; // 未检测过的 map 
	var checked = {'0-0':1}; // 检测过的点
	var unchecked = {}; // 未检测过的点，会不停得将新的未检测的点放进来，检测过的移动到 checked;
	var unchecked_arr = []; // 用来加速
	// 从四周遍历
	/*
		*************************************
		*                                   *
		*                                   *
		*                                   *
		*                                   *
		*                                   *
		*                                   *
		*                                   *
		*                                   *
		*                                   *
		*                                   *
		*                                   *
		*************************************
	*/
	for(var i = 0; i < width; i++) {
		var k1 = i + '-0';
		var k2 = i + '-' + (height - 1);
		unchecked[k1] = 1;
		unchecked[k2] = 1;
		unchecked_arr.push(k1);
		unchecked_arr.push(k2);
	}
	for(var i = 1; i < height - 1; i++) {
		var k1 ='0-' + i;
		var k2 = (width - 1) + '-' + i;
		unchecked[k1] = 1;
		unchecked[k2] = 1;
		unchecked_arr.push(k1);
		unchecked_arr.push(k2);
	}
	
	var bg = [data[0], data[1], data[2], data[3]];
	// 如果不是纯黑，纯白，则返回。
	if(!((bg[0] == 0 && bg[1] == 0 && bg[2] == 0) || (bg[0] == 255 && bg[1] == 255 && bg[2] == 255))) return;
	// 判断该点是否被检测过。
	/*
	function is_checked(x, y) {
		return checked[x+'-'+y] ? true : false;
	}
	function is_unchecked(x, y) {
		return unchecked[x+'-'+y] ? true : false;
	}*/
	
	function get_one_unchecked() {
		if(unchecked_arr.length == 0) return false;
		var k = unchecked_arr.pop();
		var r = xo.explode('-', k);
		return r;
	}
	function checked_push(x, y) {
		var k = x+'-'+y;
		if(checked[k] === undefined) checked[k] = 1;
	}
	function unchecked_push(x, y) {
		var k = x+'-'+y;
		if(checked[k] === undefined && unchecked[k] === undefined) {
			unchecked[k] = 1;
			unchecked_arr.push(k);
		}
	}
	
	var n = 0;
	while(1) {
		//if(k++ > 100000) break;
		//if(checked.length > 10000) return;
		//(n++ % 10000 == 0) {
			//alert(n);
			//console.log(unchecked_arr);
			//console.log(unchecked);
			//break;
		//}
		// 遍历未检测的区域，并且不在 checked 列表的，放进去。
		var curr = get_one_unchecked();
		//if(unchecked.length > 1000) return;
		// 遍历完毕，终止遍历
		if(!curr) break;
		var x = xo.intval(curr[0]);
		var y = xo.intval(curr[1]);
		
		// 在 data 中的偏移量应该 * 4, rgba 各占一位。
		var pos = 4 * ((y * width) + x);
		var r = data[pos];
		var g = data[pos + 1];
		var b = data[pos + 2];
		var a = data[pos + 3];
		
		if(Math.abs(r - bg[0]) < 2 && Math.abs(g == bg[1]) < 2 && Math.abs(b == bg[2]) < 2) {
			
			if(!callback) {
				data[pos + 0] = 0; // 处理为透明
				data[pos + 1] = 0; // 处理为透明
				data[pos + 2] = 0; // 处理为透明
				data[pos + 3] = 0; // 处理为透明
			} else {
				callback(data, pos);
			}			
		
			// 检测边距
			if(y > 0) unchecked_push(x, y-1);	 // 上
			if(x < width - 1) unchecked_push(x+1, y); // 右
			if(y < height - 1) unchecked_push(x, y+1); // 下
			if(x > 0) unchecked_push(x-1, y); 	// 左
		}
		
		checked_push(x, y); // 保存
	}
};

xo.image_file_type = function(file_base64_data) {
	var pre = xo.substr(file_base64_data, 0, 14);
	if(pre == 'data:image/gif') {
		return 'gif';
	} else if(pre == 'data:image/jpe' || pre == 'data:image/jpg') {
		return 'jpg';
	} else if(pre == 'data:image/png') {
		return 'png';
	}
	return 'jpg';
}

//对图片进行裁切，缩略，对黑色背景，透明化处理
xo.image_resize = function(file_base64_data, callback, options) {
	var thumb_width = options.width || 1200;
	var thumb_height = options.height || 2400;
	var action = options.action || 'thumb';
	var filetype = options.filetype || xo.image_file_type(file_base64_data);//xo.base64_data_image_type(file_base64_data);
	var qulity = options.qulity || 0.9; // 图片质量, 1 为无损
	
	if(thumb_width < 1) return callback(-1, '缩略图宽度不能小于 1 / thumb image width length is less 1 pix');
	if(xo.substr(file_base64_data, 0, 10) != 'data:image') return callback(-1, '传入的 base64 数据有问题 / deformed base64 data');
	// && xo.substr(file_base64_data, 0, 14) != 'data:image/gif' gif 不支持\
	
	var img = new Image();
	img.onload = function() {
		
		var water_img = new Image();
		water_img.onload = function() {
			var canvas = document.createElement('canvas');
			// 等比缩放
			var width = 0, height = 0, canvas_width = 0, canvas_height = 0;
			var dx = 0, dy = 0;
			
			var img_width = img.width;
			var img_height = img.height;
			
			if(xo.substr(file_base64_data, 0, 14) == 'data:image/gif') return callback(0, {width: img_width, height: img_height, data: file_base64_data});
			
			// width, height: 计算出来的宽高（求）
			// thumb_width, thumb_height: 要求的缩略宽高
			// img_width, img_height: 原始图片宽高
			// canvas_width, canvas_height: 画布宽高
			if(action == 'thumb') {
				if(img_width < thumb_width && img_height && thumb_height) {
					width = img_width;
					height = img_height;
				} else {
					// 横形
					if(img_width / img_height > thumb_width / thumb_height) {
						var width = thumb_width; // 以缩略图宽度为准，进行缩放
						var height = Math.ceil((thumb_width / img_width) * img_height);
					// 竖形
					} else {
						var height = thumb_height; // 以缩略图宽度为准，进行缩放
						var width = Math.ceil((img_width / img_height) * thumb_height);
					}
				}
				canvas_width = width;
				canvas_height = height;
			} else if(action == 'clip') {
				if(img_width < thumb_width && img_height && thumb_height) {
					if(img_height > thumb_height) {
						thumb_width = width = img_width;
						// thumb_height = height = thumb_height;
					} else {
						thumb_width = width = img_width;
						thumb_height = height = img_height;
					}
				} else {
					// 横形
					if(img_width / img_height > thumb_width / thumb_height) {
						var height = thumb_height; // 以缩略图宽度为准，进行缩放
						var width = Math.ceil((img_width / img_height) * thumb_height);
						var dx = -((width - thumb_width) / 2);
						var dy = 0;
					// 竖形
					} else {
						var width = thumb_width; // 以缩略图宽度为准，进行缩放
						var height = Math.ceil((img_height / img_width) * thumb_width);
						dx = 0;
						dy = -((height - thumb_height) / 2);
					}
				}
				canvas_width = thumb_width;
				canvas_height = thumb_height;
			}
			canvas.width = canvas_width;
			canvas.height = canvas_height;
			var ctx = canvas.getContext("2d"); 
	
			//ctx.fillStyle = 'rgb(255,255,255)';
			//ctx.fillRect(0,0,width,height);
	
			ctx.clearRect(0, 0, width, height); 			// canvas清屏
			ctx.drawImage(img, 0, 0, img_width, img_height, dx, dy, width, height);	// 将图像绘制到canvas上 
			
			var water_width = water_img.width;
			var water_height = water_img.height;
			if(img_width > 400 && img_width > water_width && water_width > 4) {
				var x =  img_width - water_width - 16;
				var y = img_height - water_height - 16;
				
				// 参数参考：https://developer.mozilla.org/en-US/docs/Web/API/CanvasRenderingContext2D/drawImage
				ctx.globalAlpha = 0.3; // 水印透明度
				ctx.beginPath();
				ctx.drawImage(water_img, 0, 0, water_width, water_height, x, y, water_width, water_height);	// 将水印图像绘制到canvas上 
				ctx.closePath();
				ctx.save();
			}
			
			var imagedata = ctx.getImageData(0, 0, canvas_width, canvas_height);
			var data = imagedata.data;
			// 判断与 [0,0] 值相同的并且连续的像素为背景
	
			//xo.image_background_opacity(data, canvas_width, canvas_height);
	
			// 将修改后的代码复制回画布中
			ctx.putImageData(imagedata, 0, 0);
	
			//filetype = 'png';
			if(filetype == 'jpg') filetype = 'jpeg';
			var s = canvas.toDataURL('image/'+filetype, qulity);
			if(callback) callback(0, {width: width, height: height, data: s});
		
		};
		water_img.src = options.water_image_url || xo.options.water_image_url;
	};
	img.onerror = function(e) {
		console.log(e);
		alert(e);
	};
	img.src = file_base64_data;
};

/*
	用法：
	var file = e.target.files[0]; // 文件控件 onchange 后触发的 event;
	var upload_url = 'xxx.php'; // 服务端地址
	var postdata = {width: 2048, height: 4096, action: 'thumb', filetype: 'jpg'}; // postdata|options 公用，一起传给服务端。
	var progress = function(percent) { console.log('progress:'+ percent); }}; // 如果是图片，会根据此项设定进行缩略和剪切 thumb|clip
	xo.upload_file(file, upload_url, postdata, function(code, json) {
		// 成功
		if(code == 0) {
			console.log(json.url);
			console.log(json.width);
			console.log(json.height);
		} else {
			alert(json);
		}
	}, progress);
*/
xo.upload_file = function(file, upload_url, postdata, complete_callback, progress_callback, thumb_callback) {
	postdata = postdata || {};
	postdata.width = postdata.width || 1200;
	postdata.height = postdata.height || 2400;
	
	var ajax_upload_file = function(base64_data) {
		var ajax_upload = function(upload_url, postdata, complete_callback) {
			$.xpost(upload_url, postdata, function(code, message) {
				if(code != 0) return complete_callback(code, message);
				if(complete_callback) complete_callback(0, message);
			}, function(percent) {
				if(progress_callback) progress_callback(percent);
			});
		};
		
		// gif 直接上传
		// 图片进行缩放，然后上传
		//  && xo.substr(base64_data, 0, 14) != 'data:image/gif'
		if(xo.substr(base64_data, 0, 10) == 'data:image') {
			var filename = file.name ? file.name : (file.type == 'image/png' ? 'capture.png' : 'capture.jpg');
			xo.image_resize(base64_data, function(code, message) {
				if(code != 0) return alert(message);
				// message.width, message.height 是缩略后的宽度和高度
				postdata.name = filename;
				postdata.data = message.data;
				postdata.width = message.width;
				postdata.height = message.height;
				ajax_upload(upload_url, postdata, complete_callback);
			}, postdata);
		// 文件直接上传， 不缩略
		} else {
			var filename = file.name ? file.name : '';
			postdata.name = filename;
			postdata.data = base64_data;
			postdata.width = 0;
			postdata.height = 0;
			ajax_upload(upload_url, postdata, complete_callback);
		}
	};
		
	// 如果为 base64 则不需要 new FileReader()
	if(xo.is_string(file) && xo.substr(file, 0, 10) == 'data:image') {
		var base64_data = file;
		if(thumb_callback) thumb_callback(base64_data);
		ajax_upload_file(base64_data);
	} else {
		var reader = new FileReader();
			reader.readAsDataURL(file);
			reader.onload = function() {
				var base64_data = this.result;
				if(thumb_callback) thumb_callback(base64_data);
			    ajax_upload_file(base64_data);
			}
	}
	
};

// 从事件对象中查找 file 对象，兼容 jquery event, clipboard, file.onchange
xo.get_files_from_event = function(e) {
	function get_paste_files(e) {
		return e.clipboardData && e.clipboardData.items ? e.clipboardData.items : null;
	}
	function get_drop_files(e) {
		return e.dataTransfer && e.dataTransfer.files ? e.dataTransfer.files : null;
	}
	if(e.originalEvent) e = e.originalEvent;
	if(e.type == 'change' && e.target && e.target.files && e.target.files.length > 0) return e.target.files;
	var files = e.type == 'paste' ? get_paste_files(e) : get_drop_files(e);
	return files;
};

// 获取所有的 父节点集合，一直到最顶层节点为止。, IE8 没有 HTMLElement
xo.nodeHasParent = function(node, topNode) {
	if(!topNode) topNode = document.body;
	var pnode = node.parentNode;
	while(pnode) {
		if(pnode == topNode) return true;
		pnode = pnode.parentNode;
	};
	return false;
};

// 表单提交碰到错误的时候，依赖此处，否则错误会直接跳过，不利于发现错误
window.onerror = function(msg, url, line) {
	if(!window.debug) return;
	alert("error: "+msg+"\r\n line: "+line+"\r\n url: "+url);
	// 阻止所有的 form 提交动作
	return false;
};

// remove() 并不清除子节点事件！！用来替代 remove()，避免内存泄露
$.fn.removeDeep = function() {
	 this.each(function() {
		$(this).find('*').off();
	});
	this.off();
	this.remove();
	return this;
};

// empty 清楚子节点事件，释放内存。
$.fn.emptyDeep = function() {
	this.each(function() {
		$(this).find('*').off();
	});
	this.empty();
	return this;
};

$.fn.son = $.fn.children;

/*
	用来增强 $.fn.val()
	
	用来选中和获取 select radio checkbox 的值，用法：
	$('#select1').checked(1);			// 设置 value="1" 的 option 为选中状态
	$('#select1').checked();			// 返回选中的值。
	$('input[type="checkbox"]').checked([2,3,4]);	// 设置 value="2" 3 4 的 checkbox 为选中状态
	$('input[type="checkbox"]').checked();		// 获取选中状态的 checkbox 的值，返回 []
	$('input[type="radio"]').checked(2);		// 设置 value="2" 的 radio 为选中状态
	$('input[type="radio"]').checked();		// 返回选中状态的 radio 的值。
*/
$.fn.checked = function(v) {
	// 转字符串
	if(v) v = v instanceof Array ? v.map(function(vv) {return vv+""}) : v + "";
	var filter = function() {return !(v instanceof Array) ? (this.value == v) : ($.inArray(this.value, v) != -1)};
	// 设置
	if(v) {
		this.each(function() {
			if(xo.strtolower(this.tagName) == 'select') {
				$(this).find('option').filter(filter).prop('selected', true);
			} else if(xo.strtolower(this.type) == 'checkbox' || xo.strtolower(this.type) == 'radio') {
				// console.log(v);
				$(this).filter(filter).prop('checked', true);
			}
		});
		return this;
	// 获取，值用数组的方式返回
	} else {
		if(this.length == 0) return [];
		var tagtype = xo.strtolower(this[0].tagName) == 'select' ? 'select' : xo.strtolower(this[0].type);
		var r = (tagtype == 'checkbox' ? [] : '');
		for(var i=0; i<this.length; i++) {
			var tag = this[i];
			if(tagtype == 'select') {
				var joption = $(tag).find('option').filter(function() {return this.selected == true});
				if(joption.length > 0) return joption.attr('value');
			} else if(tagtype == 'checkbox') {
				if(tag.checked) r.push(tag.value);
			} else if(tagtype == 'radio') {
				if(tag.checked) return tag.value;
			}
		}
		return r;
	}
};

// 支持连续操作 jsubmit.button(message).delay(1000).button('reset');
$.fn.button = function(status) {
	return this.each(function() {
		var jthis = $(this);
		jthis.queue(function (next) {
			var loading_text = jthis.attr('loading-text') || jthis.data('loading-text');
			if(status == 'loading') {
				jthis.prop('disabled', true).addClass('disabled').attr('default-text', jthis.text());
				jthis.html(loading_text);
			} else if(status == 'disabled') {
				jthis.prop('disabled', true).addClass('disabled');
			} else if(status == 'enable') {
				jthis.prop('disabled', false).removeClass('disabled');
			} else if(status == 'reset') {
				jthis.prop('disabled', false).removeClass('disabled');
				if(jthis.attr('default-text')) {
					jthis.text(jthis.attr('default-text'));
				}
			} else {
				jthis.text(status);
			}
			next();
		});
	});
};

// 支持连续操作 jsubmit.button(message).delay(1000).button('reset').delay(1000).location('http://xxxx');
$.fn.location = function(href) {
	var jthis = this;
	jthis.queue(function(next) {
		if(!href) {
			window.location.reload();
		} else {
			window.location = href;
		}
		next();
	});
};

// 在控件上方提示错误信息
$.fn.alert = function(message) {
	var jthis = $(this);
	jthis.addClass('is-invalid');
	// 表单里紧跟在输入框后面的 .invalid-feedback 显示文字
	var jfeedback = jthis.siblings('.invalid-feedback');
	if(jfeedback.length == 0) {
		jfeedback = jthis.closest('.input-group, .mb-3').find('.invalid-feedback');
	}
	jfeedback.first().text(message);
	// 悬浮提示（Bootstrap 5 的 tooltip 不再是 jQuery 插件）
	if(window.bootstrap && bootstrap.Tooltip) {
		bootstrap.Tooltip.getOrCreateInstance(jthis[0], {title: message, trigger: 'manual'}).show();
	}
	return this;
};

$.fn.serializeObject = function() {
	var self = this,
		json = {},
		push_counters = {},
		patterns = {
			"validate": /^[a-zA-Z][a-zA-Z0-9_]*(?:\[(?:\d*|[a-zA-Z0-9_]+)\])*$/,
			"key":	  /[a-zA-Z0-9_]+|(?=\[\])/g,
			"push":	 /^$/,
			"fixed":	/^\d+$/,
			"named":	/^[a-zA-Z0-9_]+$/
		};

	this.build = function(base, key, value){
		base[key] = value;
		return base;
	};

	this.push_counter = function(key){
		if(push_counters[key] === undefined){
			push_counters[key] = 0;
		}
		return push_counters[key]++;
	};

	$.each($(this).serializeArray(), function(){

		// skip invalid keys
		if(!patterns.validate.test(this.name)){
			return;
		}

		var k,
			keys = this.name.match(patterns.key),
			merge = this.value,
			reverse_key = this.name;

		while((k = keys.pop()) !== undefined){

			// adjust reverse_key
			reverse_key = reverse_key.replace(new RegExp("\\[" + k + "\\]$"), '');

			// push
			if(k.match(patterns.push)){
				merge = self.build([], self.push_counter(reverse_key), merge);
			}

			// fixed
			else if(k.match(patterns.fixed)){
				merge = self.build([], k, merge);
			}

			// named
			else if(k.match(patterns.named)){
				merge = self.build({}, k, merge);
			}
		}

		json = $.extend(true, json, merge);
	});

	return json;
};

/*
$.fn.serializeObject = function() {
	var o = {};
	var a = this.serializeArray();
	$.each(a, function() {
		if(o[this.name]) {
			if(!o[this.name].push) {
				o[this.name] = [o[this.name]];
			}
			o[this.name].push(this.value || '');
		} else {
			o[this.name] = this.value || '';
		}
	});
	return o;
};*/
 
/*
$.fn.serializeObject = function() {
	var formobj = {};
	$([].slice.call(this.get(0).elements)).each(function() {
		var jthis = $(this);
		var type = jthis.attr('type');
		var name = jthis.attr('name');
		if (name && xo.strtolower(this.nodeName) != 'fieldset' && !this.disabled && type != 'submit' && type != 'reset' && type != 'button' &&
		((type != 'radio' && type != 'checkbox') || this.checked)) {
			// 还有一些情况没有考虑, 比如: hidden 或 text 类型使用 name 数组时
			if(type == 'radio' || type == 'checkbox') {
				if(!formobj[name]) formobj[name] = [];
				formobj[name].push(jthis.val());
			}else{
				formobj[name] = jthis.val();
			}
		}
	})
	return formobj;
}*/

// 批量修改 input name="gid[123]" 中的 123 的值
$.fn.attr_name_index = function(rowid) {
	return this.each(function() {
		var jthis = $(this);
		var name = jthis.attr('name');
		name = name.replace(/\[(\d*)\]/, function(all, oldid) {
			var newid = rowid === undefined ? xo.intval(oldid) + 1 : rowid;
			return '[' + newid + ']';
		});
		jthis.attr('name', name);
	});
};

// 重置 form 状态
$.fn.reset = function() {
	var jform = $(this);
	jform.find('input[type="submit"]').button('reset');
	jform.find('.is-invalid').removeClass('is-invalid');
	jform.find('.invalid-feedback').text('');
	if(window.bootstrap && bootstrap.Tooltip) {
		jform.find('input').each(function() {
			var inst = bootstrap.Tooltip.getInstance(this);
			if(inst) inst.dispose();
		});
	}
};

// 用来代替 <base href="../" /> 的功能
$.fn.base_href = function(base) {
	function replace_url(url) {
		if(url.match('/^https?:\/\//i')) {
			return url;
		} else {
			return base + url;
		}
	}
	this.find('img').each(function() {
		var jthis = $(this);
		var src = jthis.attr('src');
		if(src) jthis.attr('src', replace_url(src));
	});
	this.find('a').each(function() {
		var jthis = $(this);
		var href = jthis.attr('href');
		if(href) jthis.attr('href', replace_url(href));
	});
	return this;
};

// $.each() 的串行版本，用法：
/*
	$.each_sync(items, function(i, callback) {
		var item = items[i];
		$.post(url, function() {
			// ...
			callback();
		});
	});
*/
$.each_sync = function(array, func, callback){
	async.series((function(){
		var func_arr = [];
		for(var i = 0; i< array.length; i++){
			var f = function(i){
				return function(callback){
					func(i, callback);
					/*
					setTimeout(function() {
						func(i, callback);
					}, 2000);*/
					
				}
			};
			func_arr.push(f(i))
		}
		return func_arr;
	})(), function(error, results) {
		if(callback) callback(null, "complete");
	});
};

// 定位
/*
         11      12      1
        --------------------
     10 |  -11   -12   -1  | 2
        |                  |
      9 |  -9    0     -3  | 3
        |                  |
      8 |  -7    -6    -5  | 4
        --------------------
         7        6       5

     将菜单定位于自己的周围：
     $(this).xo_position($('#menuid'), 6);

*/
// 将菜单定位于自己的周围
$.fn.xo_position = function(jfloat, pos, offset) {
	var jthis = $(this);
	var jparent = jthis.offsetParent();
	var pos = pos || 0;
	var offset = offset || {left: 0, top: 0};
	offset.left = offset.left || 0;
	offset.top = offset.top || 0;
	
	// 如果 menu 藏的特别深，把它移动出来。
	if(jfloat.offsetParent().get(0) != jthis.offsetParent().get(0)) {
		jfloat.appendTo(jthis.offsetParent());
	}
	
	// 设置菜单为绝对定位
	jfloat.css('position', 'absolute').css('z-index', jthis.css('z-index') + 1);
	
	var p = jthis.position();
	p.w = jthis.outerWidth();
	p.h = jthis.outerHeight();
	var m = {left: 0, top: 0};
	m.w = jfloat.outerWidth();
	m.h = jfloat.outerHeight();
	p.margin = {
		left: xo.floatval(jthis.css('margin-left')),
		top: xo.floatval(jthis.css('margin-top')),
		right: xo.floatval(jthis.css('margin-right')),
		bottom: xo.floatval(jthis.css('margin-bottom')),
	};
	p.border = {
		left: xo.floatval(jthis.css('border-left-width')),
		top: xo.floatval(jthis.css('border-top-width')),
		right: xo.floatval(jthis.css('border-right-width')),
		bottom: xo.floatval(jthis.css('border-bottom-width')),
	};
	//alert('margin-top:'+p.margin.top+', border-top:'+p.border.top);
	
	if(pos == 12) {
		m.left = p.left + ((p.w - m.w) / 2);
		m.top = p.top - m.h ;
	} else if(pos == 1) {
		m.left = p.left + (p.w - m.w);
		m.top = p.top - m.h;
	} else if(pos == 11) {
		m.left = p.left;
		m.top = p.top - m.h;
	} else if(pos == 2) {
		m.left = p.left + p.w;
		m.top = p.top;
	} else if(pos == 3) {
		m.left = p.left + p.w;
		m.top = p.top + ((p.h - m.h) / 2);
	} else if(pos == 4) {
		m.left = p.left + p.w;
		m.top = p.top + (p.h - m.h);
	} else if(pos == 5) {
		m.left = p.left + (p.w - m.w);
		m.top = p.top + p.h;
	} else if(pos == 6) {
		m.left = p.left + ((p.w - m.w) / 2);
		m.top = p.top + p.h;
	} else if(pos == 7) {
		m.left = p.left;
		m.top = p.top + p.h;
	} else if(pos == 8) {
		m.left = p.left - m.w;
		m.top = p.top + (p.h - m.h);
	} else if(pos == 9) {
		m.left = p.left - m.w;
		m.top = p.top + ((p.h - m.h) / 2);
	} else if(pos == 10) {
		m.left = p.left - m.w;
		m.top = p.top;
	} else if(pos == -12) {
		m.left = p.left + ((p.w - m.w) / 2);
		m.top = p.top;
	} else if(pos == -1) {
		m.left = p.left + (p.w - m.w);
		m.top = p.top;
	} else if(pos == -3) {
		m.left = p.left + p.w - m.w;
		m.top = p.top + ((p.h - m.h) / 2);
	} else if(pos == -5) {
		m.left = p.left + (p.w - m.w);
		m.top = p.top + p.h - m.h;
	} else if(pos == -6) {
		m.left = p.left + ((p.w - m.w) / 2);
		m.top = p.top + p.h - m.h;
	} else if(pos == -7) {
		m.left = p.left;
		m.top = p.top + p.h - m.h;
	} else if(pos == -9) {
		m.left = p.left;
		m.top = p.top + ((p.h - m.h) / 2);
	} else if(pos == -11) {
		m.left = p.left;
		m.top = p.top - m.h + m.h;
	} else if(pos == 0) {
		m.left = p.left + ((p.w - m.w) / 2);
		m.top = p.top + ((p.h - m.h) / 2);
	}
	jfloat.css({left: m.left + offset.left, top: m.top + offset.top});
};

// 菜单定位
/*
         11        12     1
        --------------------
     10 |                  | 2
        |                  |
      9 |        0         | 3
        |                  |
      8 |                  | 4
        --------------------
         7        6       5

	弹出菜单：
	$(this).xo_menu($('#menuid'), 6);
*/
$.fn.xo_menu = function(jmenu, pos, option) {
	// 生成一个箭头放到菜单的周围
	var jthis = $(this);
	var pos = pos || 6;
	var offset = {};
	var option = option || {hidearrow: 0};
	var jparent = jmenu.offsetParent();
	if(!jmenu.jarrow && !option.hidearrow) jmenu.jarrow = $('<div class="arrow arrow-up" style="display: none;"><div class="arrow-box"></div></div>').insertAfter(jthis);
	if(!option.hidearrow) {
		if(pos == 2 || pos == 3 || pos == 4) {
			jmenu.jarrow.addClass('arrow-left');
			offset.left = 7;
		} else if(pos == 5 || pos == 6 || pos == 7) {
			jmenu.jarrow.addClass('arrow-up');
			offset.top = 7;
		} else if(pos == 8 || pos == 9 || pos == 10) {
			jmenu.jarrow.addClass('arrow-right');
			offset.left = -7;
		} else if(pos == 11 || pos == 12 || pos == 1) {
			jmenu.jarrow.addClass('arrow-down');
			offset.top = -7;
		}
	}
	var arr_pos_map = {2: 10, 3: 9, 4: 8, 5: 1, 6: 12, 7: 11, 8: 4, 8: 3, 10: 2, 11: 7, 12: 6, 1: 5};
	var arr_offset_map = {
		2: {left: -1, top: 10},
		3: {left: -1, top: 0},
		4: {left: -1, top: -10},
		5: {left: -10, top: -1},
		6: {left: 0, top: -1},
		7: {left: 10, top: -1},
		8: {left: 1, top: -10},
		9: {left: 1, top: 0},
		10: {left: 1, top: 10},
		11: {left: 10, top: 1},
		12: {left: 0, top: 1},
		1: {left: -10, top: 1},
	};
	jthis.xo_position(jmenu, pos, offset);
	jmenu.toggle();
	
	// arrow
	var mpos = arr_pos_map[pos];
	if(!option.hidearrow) jmenu.xo_position(jmenu.jarrow, mpos, arr_offset_map[mpos]);
	if(!option.hidearrow) jmenu.jarrow.toggle();
	var menu_hide = function(e) {
		if(jmenu.is(":hidden")) return;
		jmenu.toggle();
		if(!option.hidearrow) jmenu.jarrow.hide();
		$('body').off('click', menu_hide);
	};
	
	$('body').off('click', menu_hide).on('click', menu_hide);
};


$.fn.xo_dropdown = function() {
	return this.each(function() {
		var jthis = $(this);
		var jtoggler = jthis.find('.dropdown-toggle');
		var jdropmenu = jthis.find('.dropdown-menu');
		var pos = jthis.data('pos') || 5;
		var hidearrow = !!jthis.data('hidearrow');
		jtoggler.on('click', function() {
			jtoggler.xo_menu(jdropmenu, pos, {hidearrow: hidearrow});
			return false;
		});
	});
};

$.fn.xo_toggle = function() {
	return this.each(function() {
		var jthis = $(this);
		var jtarget = $(jthis.data('target'));
		var target_hide = function(e) {
			if(jtarget.is(":hidden")) return;
			jtarget.slideToggle('fast');
			$('body').off('click', target_hide);
		};
		jthis.on('click', function() {
			jtarget.slideToggle('fast');
			$('body').off('click', target_hide).on('click', target_hide);
			return false;
		});
	});
};

$('.xo-dropdown').xo_dropdown();
$('.xo-toggle').xo_toggle();

// ---------------- Bootstrap 5 弹窗 ----------------
// 打开弹窗
xo.modal_show = function(jmodal, options) {
	if(!jmodal || jmodal.length == 0) return null;
	var inst = bootstrap.Modal.getOrCreateInstance(jmodal[0], options || {});
	inst.show();
	return inst;
};

// 关闭弹窗。dispose 只销毁实例、不摘掉节点，遮罩会留一层，所以这里连同节点一起删。
xo.modal_close = function(jmodal) {
	if(!jmodal || jmodal.length == 0) return;
	var el = jmodal[0];
	var inst = bootstrap.Modal.getInstance(el);
	if(!inst) {
		jmodal.remove();
		return;
	}
	// 打开动画还没结束就调用 hide() 会被 Bootstrap 直接忽略，等 shown 后再关
	if(inst._isTransitioning) {
		$(el).one('shown.bs.modal', function() {
			xo.modal_close(jmodal);
		});
		return;
	}
	$(el).one('hidden.bs.modal', function() {
		$(this).remove();
	});
	inst.hide();
};

console.log('xunio.js loaded');
