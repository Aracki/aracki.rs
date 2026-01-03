//SQL i ODBC table_reserved_keywords
var table_reserved_keywords = new Array("Blok", "DodatniMeni", "IpZemlja", "Izvestaj", "Logs", "Ocp", "OcpJezik", "OcpLabela", "OcpPrevod", "Polja", "RadioLista", "Root", "SecurityIzvestaj", "SecurityObjects", "SecuritySekcija", "SecurityStranica", "SecurityVerzija", "Sekcija", "SelectLista", "SiteMenu", "Stranica", "Stranica_Blok", "Stranica_Stranica", "Template", "TipBloka", "TipoviObjekata", "Upload", "UserGroups", "Users", "Verzija");

/*	if column name has ' or blank returns false
==============================================*/
function validate_table_name(name, labNotNumber, labFirstChar, labForrbidenChar, labReservedKeywords){
	//ime ne sme biti broj
	if (!isNaN(name)){
		alert(name + labNotNumber);
		return false;
	}

	//mora poceti sa _ ili alfa characterom
	var firstChar = name.charAt(0);
	re = new RegExp( "[_a-zA-Z]", "gi");
	if (!re.test(firstChar)){
		alert(name + labFirstChar);
		return false;
	}

	//nedozvoljeni karakteri
	var forrbidenChars = Array("", "");
	re = new RegExp( "[!@#$%^&*()+={}|\;':,./<>? \"]", "gi");
	if (re.test(name)){
		alert(name + labForrbidenChar);
		return false;
	}

	//validaciju ocp rezervisanih reci
	var retVal = true;

	var tempName = name.toUpperCase();
	for (var i=0; i<table_reserved_keywords.length; i++){
		if (tempName == table_reserved_keywords[i].toUpperCase()){
			alert(name + labReservedKeywords);
			retVal = false;
			break;
		}
	}

	return retVal;
}