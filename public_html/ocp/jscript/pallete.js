	bName = navigator.appName;
	var paletteField = "";

	function paletteOpen(fieldName) {
		var d = new Date();
		paletteField = fieldName;
		palette = window.open("","paletteWindow","toolbar=0,location=0,menubar=0,scrollbars=0,resizable=1,width=200,height=200");
		palette.location.href = "/ocp/controls/colorControl/create_color.php?random="+Date.parse(d);
		palette.focus();
	}

	function paletteFieldUpdate(fieldName) {
		paletteField = fieldName;
	}

	function colorFieldFill(color) {
		var colorObject = eval('document.formObject.'+paletteField+';');
		colorObject.value = color;
	}

	function getColorFieldFill() {
		var colorObject = eval('document.formObject.'+paletteField+';');
		color=colorObject.value;
		return color;
	}

	function palletePopup(theURL,winName,features) { 
		window.open(theURL,winName,features);
	}

	function changeColour(id,colour) {
		tablecolour = '#008000'
		if (colour == '') { 
			id.bgColor = tablecolour;
		} else {
			id.bgColor = colour;
		}
	}