
function validate_tekst_slika(){
	var naslov = (document.formObject.naslov.value == "") ? false : true; 
	var tekst = (document.formObject.tekst.value == "") ? false : true; 
	var slika = (document.formObject.ImageurlSlike.value == "") ? false : true; 

	if (!(naslov || tekst || slika)){
		alert(labTextImage);
		return false;
	}

	var x = document.formObject.tekst;
	var iframe_editor = document.getElementById("simpleEditor_tekst");
	var iframe_exist = (iframe_editor != null) && (iframe_editor != "undefined") ? true : false;
	if (x.value != "" && !iframe_exist){
		x.value = x.value.replace(/<br>/gi, "<br/>");
		x.value = x.value.replace(/<br\/>\r\n/gi, "\r\n");
		//x.value = x.value.replace(/<br\/>/gi, "\r\n");
		x.value = x.value.replace(/\r\n/gi, "<br\/>\r\n");
	}

	return true;	
}