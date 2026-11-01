
xo.form_radio = function(name, arr, checked) {
	var checked = checked || 0;
	if(xo.empty(arr)) arr = [lang.no, lang.yes];
	var s = '';
	$.each(arr, function(k, v) {
		var add = k == checked ? ' checked="checked"' : '';
		s += "<label class=\"custom-input custom-radio\"><input type=\"radio\" name=\""+name+"\" value=\""+k+"\""+add+" />"+v+"</label> &nbsp; \r\n";
	});
	return s;
}

xo.form_options = function(arr, checked) {
	var checked = checked || 0;
	var s = '';
	$.each(arr, function(k, v) {
		var add = k == checked ? ' selected="selected"' : '';
		s += "<option value=\""+k+"\""+add+">"+v+"</option> \r\n";
	});
	return s;
}


xo.form_select = function(name, arr, checked, id) {
	var checked = checked || 0;
	var id = id || true;
	if(xo.empty(arr)) return '';
	var idadd = id === true ? "id=\""+name+"\"" : (id ? "id=\""+id+"\"" : '');
	var s = '';
	s += "<select name=\""+name+"\" class=\"custom-select\" "+idadd+"> \r\n";
	s += xo.form_options(arr, checked);
	s += "</select> \r\n";
	return s;
}