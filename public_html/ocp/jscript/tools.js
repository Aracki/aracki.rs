function printURL() {
	if (document.all) {
		var header = '<html><head><title>Printable version</title><meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
		header += '<link rel="stylesheet" href="/ocp/css/opsti.css" type="text/css"><link rel="stylesheet" href="/ocp/css/opcije.css" type="text/css"><script src="/ocp/jscript/tools.js"></script></head>';
		header += '<body bgcolor="#FFFFFF" class="normal" text="#000000" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">';
		header += "<table align='center' width='100%'><tr><td width='100%'>"
		var footer = '</td></tr></table></body></html>';

		var strInner = document.body.innerHTML;
		var startPos = strInner.lastIndexOf("<!-- print_start -->");	
		var endPos = strInner.lastIndexOf("<!-- print_end -->");
		strInner = strInner.substring(startPos+20, endPos);
		
		if (strInner.lastIndexOf("<!-- print_pause_on -->") > -1){
			var str1 = "<!-- print_pause_on -->";
			var str2 = "<!-- print_pause_off -->";
			startPos = strInner.lastIndexOf(str1);	
			endPos = strInner.lastIndexOf(str2);
			if (endPos > startPos){
				leftStr = strInner.substring(0, startPos);
				rightStr = strInner.substring(endPos + str2.length, strInner.length);
				strInner =	 leftStr + rightStr;
			}
		}

		var xwin = window.open("/ocp/html/blank.html", "PrintVersion", "menubar=yes, scrollbars=yes, resizable=yes, width=650, toolbar=yes, statubar=no");
		xwin.document.write(header+strInner+footer);
		xwin.document.execCommand('Print');
		// btags = cleanupHTML(xwin.document);
	} else {
		alert("This is IE4.0+ option.");
	}
}
