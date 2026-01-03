var fID;					//  IFrame Id
var TXTOBJ=null;			//  Textarea Object
var format=new Array();		//	format cuva trenutni mod svakog iframe na strani (WYSWYG | HTML)
var FACE= new Array();		//	face cuva face fonta (WYSWYG) u svakom iframe-u na strani
var SIZE= new Array();		//	face cuva size fonta (WYSWYG) u svakom iframe-u na strani

var cellSelect = null;
var TABLE = null;
var DIV = null;
var ACTIVE = false;

var normalClass = "block editor_body ocp_scroll";
var htmlClass = "editor_source";

var IE = (navigator.userAgent.indexOf("Firefox") == -1) ? true : false;

if(IE) document.writeln('<script src="/ocp/controls/advanced_editor/quickbuild_IE.js"></script>');
else document.writeln('<script src="/ocp/controls/advanced_editor/quickbuild_Moz.js"></script>');