/*Validacija polja koja su textarea ili text.
Ne smeju da sadrze HTML tagove
============================================*/
function validate_html_text(formName){
	x = eval("document."+formName);
	for (i=0; i<x.elements.length;i++){

		if ((x.elements[i].type == "text") || (x.elements[i].type == "textarea")){
			if (x.elements[i].value!=""){
				var elemContent = x.elements[i].value;

				//< i >
				elemContent = elemContent.replace(/>/g, "&gt;");
				elemContent = elemContent.replace(/</g, "&lt;");

				//elemContent = elemContent.replace(/\r\n/g, "<br>");

				x.elements[i].value = elemContent;				
			}
		}
	}

}