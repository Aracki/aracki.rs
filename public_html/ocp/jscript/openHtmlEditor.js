function openEditor (from,label,simple) {
	var d = new Date();

	url = "/ocp/controls/advanced_editor/frameset.php?random=" + Date.parse(d) + "&field="+from+"&label="+label+"&simple="+simple;
	if (simple == '1'){
		var tempArr = from.split(".");
		fromName = "simpleEditorObject_"+tempArr[1];
		eval("window.frames['"+fromName+"'].fillParent();");
	}
	window.open(url, 'html_editor', 'top=100, left=50, width=620, height=440, scrollbars=no, resizable=yes, status=yes');
	return false;
}
function checkHtmlEditors(simpleEditorArr) {
	var i=0;
	while (i<simpleEditorArr.length) {
		var iframeSimpleEditor = window.frames[simpleEditorArr[i]];
		if (iframeSimpleEditor != null){
			iframeSimpleEditor.fillParent();
		}
		i++;
	}
}