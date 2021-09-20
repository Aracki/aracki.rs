/*menja " u &quote;
===================*/
function validate_double_quotes(formName){
	for (var i=0; i < formName.elements.length; i++){
		var nextElement = formName.elements[i];
		if (nextElement.type == "text"){
			nextElement.value = nextElement.value.replace(/"/g, '&quot;');
		}
	}
}

function validate_double_quotes_field(formNameField){
	formNameField.value = formNameField.value.replace(/"/g, '&quot;');
}

function validate_double_quotes_value(formNameFieldValue){
	formNameFieldValue = formNameFieldValue.replace(/"/g, '&quot;');
	return formNameFieldValue;
}