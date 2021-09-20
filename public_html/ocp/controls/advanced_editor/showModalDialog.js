	
	function showPopup(url, parent, parameters){
		/*
		"font-family:Verdana;font-size:12;dialogWidth:400px;dialogHeight:120px; edge:sunken;help:no;status:no"
		"dialogWidth:30em;dialogHeight:34em; edge:sunken;help:no;status:no"	
		"dialogWidth:400px;dialogHeight:123px; edge:sunken;help:no;status:no"
		"dialogTop=100px; dialogLeft=50px; dialogWidth=760px; dialogHeight=560px; scrollbars=yes; resizable=yes; status=yes;");
		*/
//		alert(parameters);

		var parArr = new Array();
		parArr["width"] = extractModalParameter("dialogWidth", parameters);
		if (parArr["width"] == null) parArr["width"] = 0.5*screen.width;

		parArr["height"] = extractModalParameter("dialogHeight", parameters);
		if (parArr["height"] == null) parArr["height"] = 100;

		parArr["top"] = extractModalParameter("dialogTop", parameters);
		if (parArr["top"] == null) parArr["top"] = (screen.height-parseInt(parArr["height"]))/2;

		parArr["left"] = extractModalParameter("dialogLeft", parameters);
		if (parArr["left"] == null) parArr["left"] = (screen.width-parseInt(parArr["width"]))/2;

		parArr["scrollbars"] = extractModalParameter("scrollbars", parameters);
		if (parArr["scrollbars"] == null) parArr["scrollbars"] = "auto";

		parArr["resizable"] = extractModalParameter("resizable", parameters);
		if (parArr["resizable"] == null) parArr["resizable"] = "yes";

		parArr["toolbar"] = extractModalParameter("toolbar", parameters);
		if (parArr["toolbar"] == null) parArr["toolbar"] = "no";

		parArr["status"] = extractModalParameter("status", parameters);
		if (parArr["status"] == null) parArr["status"] = "no";

		parArr["menubar"] = extractModalParameter("menubar", parameters);
		if (parArr["menubar"] == null) parArr["menubar"] = "no";

		parArr["directories"] = extractModalParameter("directories", parameters);
		if (parArr["directories"] == null) parArr["directories"] = "no";

		var paramString = "";
		for (key in parArr ){
			paramString += key + "=" + parArr[key] + ",";
		}

		var frame_name = (window.opener != null && window.opener.fID != null) ? "popup_popup_editor" : "popup_editor";

		var nW = window.open(url, frame_name, paramString.substring(0, paramString.length-1));
		nW.focus()
		
		return null;
	}

	function extractModalParameter(search, parameters){
		searchIndex = parameters.indexOf(search);
		if (searchIndex > -1){
			searchIndex += search.length+1;
			var endIndex = parameters.indexOf(";", searchIndex) == -1 ? parameters.length :  parameters.indexOf(";", searchIndex);
			var value = parameters.substring(searchIndex, endIndex);
			while (value.charAt(0) == " ") value = value.substring(1);
			while (value.charAt(value.length-1) == " ") value = value.substring(0, value.length-1);
			//alert(search + " " + value);
			var reEm = new RegExp("(em)$");
			if (reEm.test(value)){
				value = parseInt(value.substring(0, value.length-2));
				value = (parseInt(value/0.9)*12)+"px";
			}
			//alert(value);
			return value;
		}
		return null;
	}