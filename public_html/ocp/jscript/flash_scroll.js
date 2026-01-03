function enableScroll(obj){
	//alert ("enabled: "+obj.id);
	obj.canScroll = true;
}
function disableScroll(obj){
	//alert ("disabled "+obj.id);
	obj.canScroll = false;
}
function scrollFlash(obj){
	if(obj.canScroll == true){
		window.event.returnValue = false;
		if (window.event.wheelDelta >= 120){
			var scrollSpeed = -50
		}else if(window.event.wheelDelta <= -120){
			var scrollSpeed = 50
		}
		obj.SetVariable("_root.scrollWheel",scrollSpeed);
	}
}