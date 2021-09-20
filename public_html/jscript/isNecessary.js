function isNecessary(elem){
	x = eval("document."+elem);
	elemName = elem.substring(elem.indexOf(".")+1, elem.length);
	if (x == null) return false;
	if (x.value == ""){
		alert("Polje "+elemName+" mora biti popunjeno.");
		return false;
	}
	return true;
}