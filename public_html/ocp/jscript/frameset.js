
/*
	ako je fixed postavljen on definise 
	koliko je visina frame-a bez din. odredjivanja
*/
var IE = (navigator.userAgent.indexOf("Firefox") == -1) ? true : false;

function adjustIFrameSize (parentRef, iframeWindow, delta, fixed) {
	var noOfRows = 0; 

	if (fixed != null){
		noOfRows = fixed;
		parentRef.document.getElementById("rightFrameset").setAttribute("rows", "30,52,"+noOfRows+",*");
	} else {
		if (iframeWindow.document.getElementsByTagName("frameset").length == 0){
			noOfRows += frameHeight(this, iframeWindow);
			if (delta)	noOfRows += delta;

			if (noOfRows > 226)
				parentRef.document.getElementById("rightFrameset").setAttribute("rows", "30,52, 226,*");
			else 
				parentRef.document.getElementById("rightFrameset").setAttribute("rows", "30,52,"+noOfRows+",*");
		}
	}
}	

function adjustIFrameSizeAdmin (parentRef, iframeWindow, delta, fixed) {
	var noOfRows = 0; 

	if (fixed != null){
		noOfRows = fixed;
		parentRef.document.getElementById("resizableFrameset").setAttribute('rows', noOfRows+",*");
	} else {
		if (iframeWindow.document.getElementsByTagName("frameset").length == 0){
			noOfRows += frameHeight(this, iframeWindow);
			if (delta)	noOfRows += delta;
			noOfRows -= 2;
			//alert(noOfRows);
			if (noOfRows > 226)
				parentRef.document.getElementById("resizableFrameset").setAttribute("rows", "226,*");
			else {
				parentRef.document.getElementById('resizableFrameset').setAttribute('rows', noOfRows+",*");
			}
		}
	}
}	

function frameHeight(parentRef,  frameWindow){
	var default_delta = 2;
	var iframeHeight = 0; 
	if (frameWindow.document.height) {
			var iframeElement = parentRef.document.getElementById(frameWindow.name);
			iframeHeight = frameWindow.document.height + default_delta;
	} else if (IE) {
		var iframeElement = parentRef.document.all[frameWindow.name];
		if (frameWindow.document.compatMode && frameWindow.document.compatMode != 'BackCompat'){
			iframeHeight = frameWindow.document.documentElement.scrollHeight + default_delta;
		} else {
			iframeHeight = frameWindow.document.body.scrollHeight + default_delta;
		}
	}

	return iframeHeight; 
}