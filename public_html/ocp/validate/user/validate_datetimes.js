/*	Validate given date
=========================*/

function validate_datetimes(elem){
	var x_dd = eval("document."+elem+"_dd.value");
	var x_mm = eval("document."+elem+"_mm.value");
	var x_yyyy = eval("document."+elem+"_yyyy.value");
	var x_time = eval("document."+elem+"_time.value");
	if (x_dd.substring(0, 1) == "0") {
		x_dd = x_dd.substring(1, x_dd.length); 
		eval("document."+elem+"_dd.value = x_dd");
	}
	if (x_mm.substring(0, 1) == "0") {
		x_mm = x_mm.substring(1, x_mm.length);
		eval("document."+elem+"_mm.value = x_mm");
	}
	var valid = false;

	if ((x_dd != "") && (x_mm != "") && (x_yyyy != "") && (x_time != "")){
		if (parseInt(x_mm)<=12 && parseInt(x_mm)>0){
			if (parseInt(x_dd)<=31 && parseInt(x_dd)>0){
				if (parseInt(x_yyyy)>0){
					prva2tacka = x_time.indexOf(":");
					druga2tacka = x_time.indexOf(":", prva2tacka+1);

					hours = x_time.substring(0, prva2tacka);
					minutes = x_time.substring(prva2tacka+1, druga2tacka);
					seconds = x_time.substring(druga2tacka+1, x_time.length);
					if (parseInt(hours)<=23 && parseInt(hours)>=0){
						if (parseInt(minutes)<=59 && parseInt(minutes)>=0){
							if (parseInt(seconds)<=59 && parseInt(seconds)>=0){
								valid = true;
							}
						}
					}
				}
			}
		}
	} else{
		valid = true;
	}

	if (!valid)  {
		alert(labDateNotValid);
	} else {
		eval("document."+elem+"_mm.value = x_mm");
		eval("document."+elem+"_dd.value = x_dd");	
	}
	return valid;
}