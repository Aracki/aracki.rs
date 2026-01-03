/*	Validate given int
=========================*/

function validate_ints(elem, elemLabela){
	var x = eval("document."+elem);
	var ret_value = true;
	if (x.value != ""){
		var elemName = x.name;
		var reInt = /^-{0,1}[1-9][0-9]*$/;
		if ((x.value != "0") && !reInt.test(x.value)){
			ret_value = false;
			alert(elemLabela+" "+labIsNumber);
		}
	}
	return ret_value;
}