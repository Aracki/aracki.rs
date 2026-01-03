/*	Validate given e-mail
=========================*/

/*function validate_email(elem){
	var x = eval("document."+elem);
	var y = x.value.toString();
	if (y.length < 7 || x.value.indexOf("@") == -1 || x.value.indexOf(".") == -1 || x.value.indexOf("@") == 0 || x.value.indexOf(".") == 0 || x.value.indexOf("@") == x.value.indexOf(".")+1 || x.value.indexOf("@") == x.value.indexOf(".")-1) {
		alert("E-mail is not correct.");
		event.returnValue = false;
	} else {
		alert("usao ovde");
		var firstOccurance = false;
		for (var i = 0; i < y.length; i++) {
			if (y.substr(i, 1) == "@" && !firstOccurance) {
				firstOccurance = true;
				var secondOccurance = false;
			}

			if (y.substr(i, 1) == "@" && firstOccurance && !secondOccurance) {
				secondOccurance = true;
			}
		}

		if (firstOccurance && !secondOccurance) { return true; }
		if (firstOccurance && secondOccurance) { event.returnValue = false; }

		return true;
	}

}*/

function validate_email(elem){
	var x = eval("document."+elem);
	
	if (x.value!=""){
		var reEmail = new RegExp("^[\\w-_\.]*[\\w-_\.]\@[\\w]\.+[\\w]+[\\w]$");
		if (!reEmail.test(x.value)){
			alert(labEmailCorrect);
			return false;
		}
	}

	return true;
}