function changeSelect(background, type, fieldName, fieldValue, offset, restrict){
	if ((offset == "") || (offset == "undefined"))
		return;
	var d = new Date();
	window.open("/ocp/styles/foreignKey.php?random="+Date.parse(d)+"&Type="+type+"&FieldName="+fieldName+"&FieldValue="+fieldValue+"&offset="+offset+"&background="+background+"&restrict="+restrict, "frame"+fieldName)
}
