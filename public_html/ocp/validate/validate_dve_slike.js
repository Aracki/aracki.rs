function validate_dve_slike(){
	var slika1 = (document.formObject.Image01urlSlike.value == "") ? false : true; 
	var slika2 = (document.formObject.Image02urlSlike.value == "") ? false : true; 

	if (!(slika1 || slika2)){
		alert(labTwoImages);
		return false;
	}

	return true;	
}