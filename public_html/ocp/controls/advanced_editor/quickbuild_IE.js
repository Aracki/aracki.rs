document.writeln('<script src="/ocp/controls/advanced_editor/showModalDialog.js"></script>');
var cellSelect = null;
var cellsSelected = null;
var ctrlExist = false;

/*Funkcija koja samo proverava da li je 
neka celija selektovana
=======================================*/
function cellSelected(){
	if(cellSelect==null){
		alert(labClickCell); 
		return false;
	} 
	return true;  
}  

/*Funkcija koja pokrece tableEditor
mora da se izvrsi na onLoad strane
===================================*/
	function start(){
		for ( var i=0; i<textareaNames.length; i++){
			var table = tableEditArray[i];							//ako je table '1' textarea -> iframe
			var lastFID = changetoIframeEditor(eval('document.formObject.'+textareaNames[i]), table);
			if (textareaNames.length) fID = lastFID;
		}
	}

/*e1 je textarea koji se menja u iframe
tom prilikom dodeljuje mu se fid tj. njegov id
-tableedit ostavlja mogucnost da se texarea 
ostane textarea
==============================================*/
	function changetoIframeEditor(el, tableEdit){
		if(navigator.platform!="Win32") return null;
		var wi = el.style.width;											//sirina iframe
		var	hi= el.style.height;											//visina iframe
		var val='';													//vrednost koja ce se upisati
		var fID;													//id iframe-a

		var parent= el.parentNode;
		while(parent.nodeName != 'FORM') parent= parent.parentNode;

		var oform= parent;
		var fidx=0;
		while(document.forms[fidx] != oform) fidx++ ;				// index forme u nizu document.forms

		if(el.nodeName=='TEXTAREA'){									//preuzimanje vrednosti i kreiranje fid
			fID= fidx + 'VDevID' + el.name; 
			val= el.value;
		} else return;

		createEditor(el, fID , wi, hi, tableEdit);
		iEditor(fID);

		val= val.replace(/\r/g,"");
		val= val.replace(/\n</g,"<");
		val= val.replace(/\n/g, "<br>");
		val= val.replace(/\t/g, "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;");
		val= val.replace(/\\/g, "&#92;");
		val= val.replace(/\'/g, "&#39;");
//ovo ne sme zbog linkova!!!
		//val= val.replace(/\"/g,"&quot;");

		setTimeout("document.frames['"+fID+"'].document.createStyleSheet('/css/general.css')", 200);
		setTimeout("document.frames['"+fID+"'].document.createStyleSheet('/ocp/css/opsti.css')", 200);
		setTimeout("document.frames['"+fID+"'].document.body.className='"+normalClass+"'", 200);
		setTimeout("document.frames['"+fID+"'].document.body.innerHTML='"+val+"'", 200);
		oform[fID.split('VDevID')[1]].value= val;
		
		TXTOBJ= null;
		return fID;   
	}

/*Funkcija koja kreira editor sa svim njegovim funkcijama
=========================================================*/
	function createEditor(el, id, wi, hi, tableEdit){
		var idA= id.split('VDevID');

		var strx = "<iframe id="+id+" style='height:"+hi+"; width:100%; border:0;' frameborder='0'></iframe>";
		strx += "<input name="+idA[1]+" type=hidden style='display:inline'>";
		var str = "<TABLE border=0 cellspacing=0 cellpadding=0 width='100%'><tr><td align=center>";
		str += strx + "</td></tr>"
		str += "</TABLE>" ;
		str += "<Div name='Temp"+id+"' Id='Temp"+id+"' style='display:none'></Div>";

		el.outerHTML = str;
}

/*Funkcija koja prijavljuje handle mousedown i keydown na iframe-u
=================================================================*/
	function iEditor(idF){
		var obj=document.frames[idF];
		obj.document.designMode="On";
		obj.document.attachEvent("onmousedown",function(){TXT=null; fID=idF; FMDown();});
		obj.document.attachEvent("onkeydown",FKDown);
		obj.document.attachEvent("onkeyup",FKUp);
		format[idF]='HTML';
	}

/*Funkcija koja obradjuje mouseDown na iframe
==============================================*/
	function FMDown(){
		var el = document.frames[fID];
		var el = el.event.srcElement;

		//alert(el.event.ctrlKey);
		doClick(el);
	}

/*Funkcija koja obradjuje keyDown na iframe-u
============================================*/
	function FKDown(){
		var el=document.frames[fID];
		var obj = document.selection.createRange();
		var obj = obj.parentElement();
		var obj = obj.tagName;
		
		
		if(!el||!el.event){
			alert(labClickEditor);
			return
		}

		var key = el.event.keyCode;							//key koji je down
		var shft= el.event.shiftKey;						// + shift
		var ctrl= el.event.ctrlKey;							// + ctrl
		var alt = el.event.altKey;							// + alt

		if(alt) return;

		if (el.event.keyCode == 17){
			ctrlExist = true;
		}

		// detektuje Enter i pazi na liste
		if (key == 13){
			if (shft){
				insertNewline(el); return false;
			} else {
				if (obj == 'LI'){
					return true;
				} else if (obj	!= 'P'){
					insertNewParagraph(el); 
					return false;
				}
				
			}
		}

		if(ctrl && key==86){								// ctrl+V paste i ciscenje od Word tagova
			document.execCommand("paste");
			cleanupWordHTML(fID.substring(fID.indexOf("VDevID") + 6, fID.length)); 
			return false; 
		} else if (ctrl && key==90){
			document.execCommand("undo");
			cleanMark();
			return false;
		}
	}

/*Funkcija koja obradjuje keyUp na iframe-u
============================================*/
	function FKUp(){
		var el=document.frames[fID];
		if(!el.event){
			alert(labClickEditor);
			return
		}
		if (el.event.keyCode == 17)
			deselectCells();
		return true;
	}

/*Funkcija koja procesira odgovarajucu komande
==============================================*/
	function doFormatF(arr){
		var el=document.frames[fID];

		if(!el){			
			alert(labClickEditor); return
		}
		el.focus()

		var cmd = new Array();
		cmd = arr.split(',')
		el.document.execCommand(cmd[0],false);
	}

/*Funkcija koja radi swap moda WYSWYG <--> HTML
===============================================*/
	function swapMode(mode){

		var el=document.frames[fID];
		if(!el){
			alert(labClickEditor);
			return false;
		}
		el.focus();

		if (mode == "HTML" && (format[fID] == "Text")) return false;
		if (mode == "Text" && (format[fID] == "HTML")) return false;

		var eStyle= el.document.body.style
		if(mode=="HTML"){
			FACE[fID]= eStyle.fontFamily			//sacuvaj font u WYSQYG
			SIZE[fID]= eStyle.fontSize				
			// COLOR[fID]= eStyle.color

			eStyle.fontFamily="Courier New";			//postavljanje stila u HTML - modu
			eStyle.fontSize="9pt"
			eStyle.fontStyle="normal"
			eStyle.color="#000000"
			eStyle.backgroundColor="#EFEFEF"

			el.document.body.innerText= el.document.body.innerHTML;
			format[fID]="Text";
		} else {
			eStyle.fontFamily = FACE[fID];			//vrati face
			if (eStyle.fontSize) eStyle.fontSize= SIZE[fID];			//vrati size
			eStyle.backgroundColor= "#ffffff";
			el.document.body.innerHTML = removeFontSize0(el.document.body.innerText);
			format[fID]="HTML";
		}
		return true;
	}

/*Funkcija koja izvlaci sadrzaj tableeditora
============================================*/
	function editorContents(fid){
		var el=document.frames[fid]
		if(!el)return

		var strx, strx1;
		if(format[fid]=="HTML"){
			strx=el.document.body.innerHTML;
			strx1=el.document.body.innerText;
		}else{
			strx=el.document.body.innerText;
			strx1=el.document.body.innerHTML;
		}
		if(strx1=='' && strx.indexOf('<IMG')<0 && strx.indexOf('<HR')<0 ) 
			return '';
		strx = strx.replace(/\r/g,""); 
		strx = strx.replace(/\n>/g,">"); 
		strx = strx.replace(/>\n/g,">"); 

		strx = strx.replace(/\\/g,"&#92;");
		strx = strx.replace(/\'/g,"&#39;");

		var idx= strx.indexOf('ViEtDeVdIvId');
		if( idx>=0 ) 
			strx= strx.substring(strx.indexOf('>')+1,strx.lastIndexOf('</DIV>'));

		idx= strx.indexOf('<style>@import url(');
		if( idx>=0 ) strx= strx.substring(0,idx);
		return strx;
	}


/*Funkcija koja vraca broj reci u u podstringu stringa
ceo, podstring pocinje tamo gde se sub zavrsava
======================================================*/
	function wordCount(sub, ceo){
		var wc = 0;
		var endIndex = ceo.indexOf(sub);
		var ceoSub = ceo.substring(0, endIndex);
		while (ceoSub.indexOf(" ") != -1){
			if (ceoSub.indexOf(" ") != -1)	wc++;
			ceoSub = ceoSub.substring(ceoSub.indexOf(" ")+1, ceoSub.length);
		}
		wc++;
		return wc;
	}
	

/*Funkcija koja otvara popup za unosenje adrese
============================================================*/
	function openLinkEditor(type){
		var d = new Date();

		var el=document.frames[fID];
		if(!el){
			alert(labClickEditor);
			return false;
		}
		el.focus();

		if (type=="email")
		{		
			var arr=showPopup('/ocp/controls/advanced_editor/create_email.php?random='+Date.parse(d), el, 
				"font-family:Verdana;font-size:12;dialogWidth:400px;dialogHeight:120px; edge:sunken;help:no;status:no");
		} else {
			var arr=showPopup('/ocp/controls/advanced_editor/create_link.php?random='+Date.parse(d), el, 
				"font-family:Verdana;font-size:12;dialogWidth:400px;dialogHeight:150px; edge:sunken;help:no;status:no");
		}

		return true;
	}

	function doClick(el){
		if(el.tagName=='TABLE'){ 
			TABLE=el; 
			return;
		}
		TABLE=null;
		while(el.tagName !='TD' && el.tagName !='BODY'){ 
			el= el.parentElement;
		}
		if(el.tagName!="TD"){ 
			cleanupSelection(); 
			cellSelect=null; 
			return;
		} 
		currentCell(el);
	}

	function cleanupSelection(){
		if (cellSelect==null || cellSelect.parentElement==null) return

		if (cellsSelected != null){
			for (var i=0; i<cellsSelected.length; i++){
				cellsSelected[i].runtimeStyle.backgroundColor = "" ;
				cellsSelected[i].runtimeStyle.color = "";
				cellsSelected[i].removeAttribute("id");
				var table= cellsSelected[i].parentElement.parentElement.parentElement
				if(table) table.runtimeStyle.backgroundColor= "" ;	
			}
		} else {
			cellSelect.runtimeStyle.backgroundColor = "" ;
			cellSelect.runtimeStyle.color = "";
			cellSelect.removeAttribute("id");
			var table= cellSelect.parentElement.parentElement.parentElement
			if(table) table.runtimeStyle.backgroundColor= "" ;
		}
	}

/************* CURRENT CELL ****************/
function currentCell(cell){
	if (!ctrlExist){
		cleanupSelection();
	} else {
//		alert(cellsSelected);
		if (cellsSelected != null && cell.runtimeStyle.backgroundColor == "cyan"){
			var not_found = true;
			var tmp = new Array();
			for (var i=0; i<cellsSelected.length; i++){
				if (cellsSelected[i].id != cell.id){
					tmp[tmp.length] = cellsSelected[i];
				} else not_fond = false;
			}
			cellsSelected = tmp;
			cellSelect = cell;
			cleanupSelection();
			return;
		} else if (cellsSelected == null || cellsSelected.length == 0){
			if (cellSelect != null){
				cellsSelected = new Array(cellSelect, cell);
			} else 
				cellsSelected = new Array(cell);
//			cellSelect = null;
		}
//		alert(cellsSelected);
	}
	
	var table= cell.parentElement.parentElement.parentElement

	if(!table.bgColor) table.runtimeStyle.backgroundColor='#c0c0f0' ;

	if(cell.bgColor!='#00ffff' && table.bgColor!='#00ffff') 
		cell.runtimeStyle.backgroundColor = "cyan";  
	else 
		cell.runtimeStyle.backgroundColor = "#00aaaa";  
	cell.runtimeStyle.color = "red";
	
	if (cell.id + "" == "undefined" || cell.id + "" == ""){
		cell.id = "" + Date.parse(new Date());
	}
	
	cellSelect = cell; 
	
	addCellsToSelected(cell);
}

function addCellsToSelected(cell){
	if (ctrlExist){
		if (cellsSelected != null){
			cellsSelected[cellsSelected.length] = cell;
		} else {
			cellsSelected = new Array();
			cellsSelected[cellsSelected.length] = cell;
		}
	}
}

function deselectCells(){
	ctrlExist = false;
		
	if (cellsSelected != null){
		for (var i=0; i<cellsSelected.length; i++){
			cellSelect = cellsSelected[i];
			cleanupSelection();
		}
		cellsSelected = null;
	}
}
