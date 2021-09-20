document.writeln('<script src="/ocp/controls/advanced_editor/showModalDialog.js"></script>');
var siteURL = window.location.href.substring(0, window.location.href.indexOf("/ocp/"));
var cellSelect = null;
var cellsSelected = null;

/*Funkcija koja samo proverava da li je 
neka celija selektovana
=======================================*/
function cellSelected(){
	if(cellSelect == null){
		alert(labClickCell);
		if(fID) addEventToTable(document.getElementById(fID).contentWindow);
		return false;
	} 
	return true; 
}

/*Funkcija koja samo proverava da li je 
neka celija selektovana
=======================================*/
function clickTD(e){
	var el= document.getElementById(fID).contentWindow;
	cellSelect	= e.currentTarget;

	var ctrl= e.ctrlKey;							// + ctrl
	if (ctrl){
		if (cellsSelected != null){
			cellsSelected[cellsSelected.length] = e.currentTarget;
		} else {
			cellsSelected = new Array();
			cellsSelected[cellsSelected.length] = e.currentTarget;
		}
	} else cellsSelected = null;
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
		var wi = el.style.width;											//sirina iframe
		var	hi= el.style.height;											//visina iframe

		var parent=el.parentNode
		while(parent.nodeName!='FORM') parent=parent.parentNode
		var oform=parent
		var fidx=0;
		while(document.forms[fidx]!=oform)fidx++//form index
		
		var val='';

		if(el.nodeName=='TEXTAREA'){
			fID=fidx+'VDevID'+el.getAttribute('name');
			val=el.value
		} else return;

		createEditor(el,fID,wi,hi, tableEdit)
		setTimeout("iEditor('"+fID+"')",200)
		return fID;
	}
	

/*Funkcija koja kreira editor sa svim njegovim funkcijama
=========================================================*/
	function createEditor(el,id,wi,hi, tableEdit){
		var hval=''
		if(el.value)hval=el.value
		hval=hval.replace(/\'/g,"&#39;")
		hval=hval.replace(/&(?!nbsp;)/g,"&amp;");

		var arr=id.split("VDevID")
		var strx="<iframe id="+id+" style='height:"+hi+"; width:100%; border:0;' frameborder='0'></iframe>"
		strx +="<input name="+arr[1]+" type=hidden value='"+hval+"'></input>"
		var str="<TABLE border=0 cellspacing=0 cellpadding=1 width='100%'><tr><td width='100%'>"
		str +=strx+"</td></tr>"
		str +="</TABLE>"
		str += "<Div name='Temp"+id+"' Id='Temp"+id+"' style='display:none'></Div>";

		var parent=el.parentNode
		var oDiv=document.createElement('div')
		parent.insertBefore(oDiv,el)
		parent.removeChild(el)
		oDiv.innerHTML=str
	}

/*Funkcija koja prijavljuje handle mousedown i keydown na iframe-u
=================================================================*/
	function iEditor(idF){
		var o=document.getElementById(idF).contentWindow.document;
		o.designMode="On";
		o.execCommand("undo", false, null);

		o.addEventListener("mousedown",function(){TXT=null;fID=idF},false);
		o.addEventListener("mouseup", FMUp, false);
		o.addEventListener("keypress", FKPress, true);

		var arr=idF.split("VDevID")
		var v=document.forms[arr[0]][arr[1]].value
		v=v.replace(/\r/g,"");
		v=v.replace(/\n</g,"<");
		v=v.replace(/\t/g,"     ");

		v=v.replace(/\n/g, "");
		v=v.replace(/\t/g,"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;");
		v=v.replace(/\\/g,"&#92;");
		v=v.replace(/\'/g,"&#39;");
//ovo ne sme zbog linkova!!!
//		v=v.replace(/\"/g,"&quot;");

		importCss(siteURL + "/css/general.css");
		importCss(siteURL + "/ocp/css/opsti.css");
		setTimeout("document.getElementById('"+fID+"').contentWindow.document.body.className='"+normalClass+"'",200)
		setTimeout("document.getElementById('"+fID+"').contentWindow.document.body.innerHTML='"+v+"'",200);
		o.execCommand('useCSS',false, true);
		format[idF]='HTML';

		//addEventToTable(document.getElementById(fID).contentWindow);
	}

/*Funkcija neophodna za Mozillu, ukljucivanje iframe-ova
========================================================*/
	function importCss(css_file, css){
		var elmHead = document.getElementById(fID).contentWindow.document.getElementsByTagName('head')[0];
		var elmStyle = document.getElementById(fID).contentWindow.document.createElement('style');
        elmStyle.type = 'text/css';
        elmHead.appendChild(elmStyle);
		if (css != null){
			elmStyle.innerHTML = css + "\n";
		} else {
	        elmStyle.innerHTML = "@import url('"+css_file+"');\n";
		}
  	}

	function clickE(){
		var el= document.getElementById(fID).contentWindow;
		if(!el){alert(labClickEditor);return}
		return el
	}

/*Funkcija koja obradjuje keyDown na iframe-u
============================================*/
	function FKPress(e){
//		alert("fkpress"):
		var el=clickE();
		if(!el) return
		
		var key = e.charCode									//key koji je down
		var shft= e.shiftKey;						// + shift
		var ctrl= e.ctrlKey;							// + ctrl
		var alt = e.altKey;							// + alt

		if (alt) return;

//		alert(key+" "+shft+" "+ctrl+" "+alt+" "+e.which);
		if(ctrl && ((key==86) || (key == 118))){// ctrl+V paste i ciscenje od Word tagova
			try{
				document.execCommand("paste", false, null);
			} catch (e){
				//alert(e.toString());
			}
			var nameEditora = fID.substring(fID.indexOf("VDevID") + 6, fID.length);
			setTimeout("cleanupWordHTML('"+nameEditora+"')", 400); 
			return false; 
		} else if (ctrl && ((key==90) || (key == 122))){// ctrl+z undo
			try{
				document.execCommand("undo", false, null);
			} catch (e){
				//alert(e.toString());
			}
			setTimeout("cleanMark()", 400); 
			return false; 
		} 

		if (!ctrl) return false;
		var stop=false;

		switch(key){
			case 99:case 120:return//Ctrl+C or X
			case 98:document.execCommand("Bold",false,null);stop=true;break//Ctrl+b
			case 105:document.execCommand("Italic",false,null);stop=true;break//Ctrl+i
			case 117:document.execCommand("Underline",false,null);stop=true;break//Ctrl+u
			case 71:stop=true;break//ctrl+G search
			case 75:stop=true;break//ctrl+K search forward
			case 74:stop=true;break//ctrl+J search backward
			case 83:stop=true;break//ctrl+S
			case 84:stop=true;break//ctrl+T swapMode
			case 48:case 49:case 50:case 51:case 52:
			case 53:case 54:case 55:case 56:case 57:stop=true;break//ctrl 0-9 Highlight
		}
		if(stop==true){e.preventDefault();return}

		return;
	}

/*Funkcija koja obradjuje mouseUp na iframe-u
============================================*/
	function setCursor(el, targetNode, targetPoint){ 
		//Setup selection range 
		range = el.document.createRange(); 
		//Place range around the target node 
		range.selectNode(targetNode); 
		//Get the selection 
		sel = el.getSelection(); 
		sel.removeAllRanges();
		//set selection
		sel.addRange(range);
		range.deleteContents();

	}

/*Funkcija koja obradjuje mouseUp na iframe-u
============================================*/
	function FMUp(e){
		curDIV=null;curIMG=null
		var el=document.getElementById(fID).contentWindow
		var cont=objInnerHTML(el)
		var SYM,idx
		if(format[fID]=="HTML")SYM="**SourcePOSITION**"
		else SYM="**ViewPOSITION**"
		idx= cont.indexOf(SYM)
		if(idx<0) return
		var sel=el.getSelection()
		var sNode=sel.focusNode
		var r=sel.getRangeAt(0)
		var sp=r.startOffset
		var ep=r.endOffset
		var i=-1,parent,opa
		r.setStart(sNode,0)
		while(1){
			i++;
			try{
				r.setEnd(sNode,i)
			} catch(e){
				break;
			}
		}

		if(sel==SYM){
			parent=sNode.parentNode
			opa=parent.parentNode
			opa.removeChild(parent)
		} else {
			r.setStart(sNode,sp)
			r.setEnd(sNode,ep)
		}
	}

/*Funkcija koja procesira odgovarajucu komande
==============================================*/
	function doFormatF(arr){
		var el=clickE()
		if(!el)return
		el.focus()

		var cmd=new Array()
		cmd=arr.split(',')
		if(cmd[1]!=null)
			el.document.execCommand(cmd[0],false,cmd[1]);
		else 
			el.document.execCommand(cmd[0],false,null);
	}


/*Funkcija koja radi swap moda WYSWYG <--> HTML
===============================================*/
	function swapMode(mode){
		var el=clickE()
		if(!el) return false;
		el.focus()

		if (mode == "HTML" && (format[fID] == "Text")) return false;
		if (mode == "Text" && (format[fID] == "HTML")) return false;

		if(mode == "HTML"){//view->sourcecode
			el.document.getElementsByTagName("body")[0].className = htmlClass;
			el.document.body.innerHTML = objInnerText(el);
			format[fID]="Text";
		} else { //sourcecode->preview
			el.document.getElementsByTagName("body")[0].className = normalClass;
			el.document.body.innerHTML = objInnerHTML(el);
			format[fID]="HTML";
			
			addEventToTable(el);//add event listen
		}

		return true;
	}

/*Funkcija koja izvlaci sadrzaj html-editora
============================================*/
	function editorContents(fid){
		fID=fid
		var el=document.getElementById(fID).contentWindow;
		var strx,strx1
		if(format[fid]=="HTML"){
		  strx=objInnerHTML(el)
		}
		else strx=objInnerHTML(el)

		strx=doCleanCode(strx,fid)
		return strx
	}

/*Pomocna funkcija za prethodnu
============================================*/
	function doCleanCode(strx,fid){
		strx=strx.replace(/\r/g,"")
		strx=strx.replace(/\n>/g,">")
		strx=strx.replace(/>\n/g,">")
		strx=strx.replace(/\n/g," ")
		strx=strx.replace(/\\/g,"&#92;")
		strx=strx.replace(/\'/g,"&#39;")

		var idx=strx.indexOf('ViEtDeVdIvId')
		if(idx>=0)strx=strx.substring(strx.indexOf('>')+1,strx.lastIndexOf('</DIV>'))

		var defdiv=""
		if(FACE[fid])defdiv+=";FONT-FAMILY:"+FACE[fid]
		if(SIZE[fid])defdiv+=";FONT-SIZE:"+SIZE[fid]
		if(defdiv){
		 defdiv='<DIV id=ViEtDeVdIvId style="POSITION:Relative'+defdiv+'">'
		 strx=defdiv+strx+"</DIV>"
		}
		strx=strx.replace(/<p([^>])*>(&nbsp;)*\s*<\/p>/gi,"")
		strx=strx.replace(/<span([^>])*>(&nbsp;)*\s*<\/span>/gi,"")
		var SYM="<span style=\"background-color: magenta; color: white;\">**ViewPOSITION**</span>"
		strx=strx.replace(SYM,'')
		SYM="<span style=\"background-color: magenta; color: white;\">**SourcePOSITION**</span>"
		strx=strx.replace(SYM,'')
		return strx
	}

	function objInnerText(el){
		var con=el.document.body.innerHTML
		con=con.replace(/<br>\r\n/g,"<br>")
		con=con.replace(/&/g,"&amp;")
		con=con.replace(/\</g,"&lt;")
		con=exchangeTags(con,"&lt;div>","&lt;/div>","","")//delete trick div
		con=con.replace(/>&lt;table/ig,"><br>&lt;table")
		con=con.replace(/>&lt;tbody/ig,"><br>&lt;tbody")
		con=con.replace(/>&lt;tr/ig,"><br>&lt;tr")
		con=con.replace(/>&lt;td/ig,"><br>&lt;td")
		return con
	}

	function objInnerHTML(el){
		var con=el.document.body.innerHTML
		con=con.replace(/\r\n/g," ")
		con=con.replace(/&amp;lt;/g,"&amp;amp;lt;")
		con=con.replace(/&amp;/g,"&")
		con=con.replace(/&lt;/g,"<")
		con=con.replace(/&gt;/g,">")
		con=con.replace(/&amp;lt;/g,"&lt;")
		con=con.replace(/><br>( *?)<table/ig,"><table")
		con=con.replace(/><br>( *?)<tbody/ig,"><tbody")
		con=con.replace(/><br>( *?)<tr/ig,"><tr")
		con=con.replace(/><br>( *?)<td/ig,"><td")
		return con
	}

/*Funkcija koja otvara popup za unosenje adrese
============================================================*/
	function openLinkEditor(type){
		var d = new Date();

		var el= document.getElementById(fID).contentWindow;
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

	function cleanupSelection(){
		if (cellSelect==null || cellSelect.parentElement==null) return
		cellSelect.runtimeStyle.backgroundColor = "" ;
		cellSelect.runtimeStyle.color = "";
		var table= cellSelect.parentElement.parentElement.parentElement
		if(table) table.runtimeStyle.backgroundColor= "" ;
	}


	function doClick(el){
		if(el.tagName=='TABLE'){ 
			TABLE=el; 
			return;
		}
		TABLE=null
		while(el.tagName !='TD' && el.tagName !='BODY') 
			el= el.parentElement
		if(el.tagName!="TD"){ cleanupSelection(); cellSelect=null; return }
		if(el!=cellSelect) currentCell(el)
	}

/************* CURRENT CELL ****************/
	function currentCell(cell){
	  cleanupSelection();
	  var table= cell.parentElement.parentElement.parentElement

	  if(!table.bgColor) table.runtimeStyle.backgroundColor='#c0c0f0' ;

	  if(cell.bgColor!='#00ffff' && table.bgColor!='#00ffff') 
		cell.runtimeStyle.backgroundColor = "cyan";  
	  else cell.runtimeStyle.backgroundColor = "#00aaaa";  
	  cell.runtimeStyle.color = "red";
	  cellSelect = cell; 
	  // alert (cellSelect);
	}

	function insertHTML(e,html){
		e.focus();
		var div=e.document.createElement("div");
		div.innerHTML=html;
		var child=div.firstChild;
		if(!child.nextSibling)insertNodeAtSelection(e,child);
		else insertNodeAtSelection(e,div);
	}

	/*** This function comes original from Mozdev.org (s. Midas-Demo)***/
	function insertNodeAtSelection(win, insertNode){
		var afterNode
		// get current selection
		var sel=win.getSelection()
		var range=sel.getRangeAt(0)
		sel.removeAllRanges()
		range.deleteContents()
		var container=range.startContainer
		var pos=range.startOffset
		// make a new range for the new selection
		range=document.createRange()
		if(container.nodeType==3&&insertNode.nodeType==3){
			// if we insert text in a textnode, do optimized insertion
			container.insertData(pos, insertNode.nodeValue)
			// put cursor after inserted text
			range.setEnd(container,pos+insertNode.length)
			range.setStart(container,pos+insertNode.length)
		}else{
			 if(container.nodeType==3) {
				// when inserting into a textnode
				// we create 2 new textnodes and put the insertNode in between
				var textNode=container
				container=textNode.parentNode
				var text=textNode.nodeValue
				// text before the split
				var textBefore=text.substr(0,pos)
				// text after the split
				var textAfter=text.substr(pos)
				var beforeNode=document.createTextNode(textBefore)
				var afterNode=document.createTextNode(textAfter)
				// insert the 3 new nodes before the old one
				container.insertBefore(afterNode, textNode)
				container.insertBefore(insertNode, afterNode)
				container.insertBefore(beforeNode, insertNode)
				// remove the old node
				container.removeChild(textNode)
			} else{
				// else simply insert the node
				afterNode=container.childNodes[pos]
				container.insertBefore(insertNode,afterNode)
			}
		}
	}