/*menja new line u br
=====================*/
function change_nl2br(elemName){
	var x = eval("document."+elemName);	
	if (x.value != ""){
		x.value = x.value.replace(/<br>/gi, "<br/>");
		if (!document.all) {
			//FF
			x.value = x.value.replace(/<br\/>\n/gi, "\n");
			x.value = x.value.replace(/<br\/>/gi, "\n");
			x.value = x.value.replace(/\n/gi, "<br\/>\n");	
		} else  {
			// IE
			x.value = x.value.replace(/<br\/>\r\n/gi, "\r\n");
			x.value = x.value.replace(/<br\/>/gi, "\r\n");
			x.value = x.value.replace(/\r\n/gi, "<br\/>\r\n");
		}
	}
	return true;	
}