/*	Validate given date
=========================*/
function validate_dates(elem){
	var x_dd = eval("document."+elem+"_dd.value"); 
	var x_mm = eval("document."+elem+"_mm.value"); 
	var x_yyyy = eval("document."+elem+"_yyyy.value");
	if (x_dd.substring(0, 1) == "0") {
		x_dd = x_dd.substring(1, x_dd.length); 
	}

	if (x_mm.substring(0, 1) == "0") {
		x_mm = x_mm.substring(1, x_mm.length); 
	}
	var valid = false;
	
	if ((x_dd != "") && (x_mm != "") && (x_yyyy != "")){
		if ((parseInt(x_mm)<=12) && (parseInt(x_mm)>0)){
			if ((parseInt(x_dd)<=31) && (parseInt(x_dd)>0)){
				if (parseInt(x_yyyy)>0){
					valid = true;
				}
			}
		}
	} else  {
		valid = true;
	}

	if (!valid) {
		alert(labDateNotValid);
	} else {
		eval("document."+elem+"_mm.value = x_mm");
		eval("document."+elem+"_dd.value = x_dd");
	}
	return valid;
}