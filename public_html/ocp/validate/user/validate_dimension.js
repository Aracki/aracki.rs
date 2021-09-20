/*	Validate given dimension
=========================*/

function validate_dimension(elem){
	var x = eval("document."+elem);	
	if (x.value!=""){
		if (x.value.indexOf("x") == -1){
			alert(labDimensionValid);
			return false;
		}else{
			var sirina = x.value.substring(0, x.value.indexOf('x'));
			if (isNaN(sirina)){
				alert(labWidthNumber);
				return false;
			} else {
				var visina = x.value.substring( x.value.indexOf('x') + 1, x.value.length);
				if (isNaN(visina)){
					alert(labHeightNumber);
					return false;
				} 
			}
		}
	}
	return true;
}