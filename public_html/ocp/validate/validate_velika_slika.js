function validate_velika_slika(){
	var slika = (document.formObject.ImageurlSlike.value == "") ? false : true; 

	if (!(slika)){
		alert(labLargeImage);
		return false;
	}
	return true;	
}