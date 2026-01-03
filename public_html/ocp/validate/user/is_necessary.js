function is_necessary(elem, elemType, elemLabela){
	var x = eval("document."+elem);

	if (x == null)
		x = document.forms[elem.substring(0, elem.indexOf('.'))].elements[elem.substring(elem.indexOf('.')+1, elem.length)+"[]"];

	var elemName = (elemLabela == null) ? elem.substring(elem.indexOf('.')+1, elem.length) : elemLabela;
	
	if ((x == null) && (elemType == null)) return false;

	if ((x != null) && x.length && !x.type && !x.value){//radio
		notChecked = true;
		for (var i=0; i<x.length;i++){
			if (x[i].checked) notChecked = false;
		}
		if (notChecked){
			alert(labField + " "+elemName + " " + labMustHaveValue);
			return false;
		}
	} else {
		if ((x == null) && (elemType != null)){//datumi i datumvreme
			switch (elemType){
				case "textDate":
					var x_dd = eval("document."+elem+"_dd");
					var x_mm = eval("document."+elem+"_mm");
					var x_yyyy = eval("document."+elem+"_yyyy");
					if ((x_dd.value == "") || (x_mm.value == "") || (x_yyyy.value == "")){
						alert(labField + " "+elemName + " " + labMustHaveValue);
						return false;
					}
					break;
				case "textDatetime":
					var x_dd = eval("document."+elem+"_dd");
					var x_mm = eval("document."+elem+"_mm");
					var x_yyyy = eval("document."+elem+"_yyyy");
					var x_time = eval("document."+elem+"_time");
					if ((x_dd.value == "") || (x_mm.value == "") || (x_yyyy.value == "") || (x_time.value == "")){
						alert(labField + " "+elemName + " " + labMustHaveValue);
						return false;
					}
					break;
			}
		} else {
			if (x.value == ""){
				alert(labField + " "+elemName + " " + labMustHaveValue);
				return false;
			}
		}
	}
	return true;
}