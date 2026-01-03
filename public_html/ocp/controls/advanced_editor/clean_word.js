	function cleanMark(){
		var el= (IE) ? document.frames[fID] : document.getElementById(fID).contentWindow;

		var MARK= "ViEtDeVtRiCk";

		if (el.document.body.innerHTML.indexOf(MARK) != -1){
			//if (IE){
				el.document.body.innerHTML = el.document.body.innerHTML.replace(MARK, "");
			//}
		}
	}

/* Funkcija koja cisti sve word tagove
======================================*/
	function cleanupWordHTML (textareaName){
		var el= (IE) ? document.frames[fID] : document.getElementById(fID).contentWindow;

		var MARK= "ViEtDeVtRiCk";

		if (IE){//ocuvanje onoga sto je bilo selektovano u modu koji se napusta
			var selType=el.document.selection.type;
			if(selType!="Control"){
				var caret=el.document.selection.createRange();
				var sadrzaj = caret.text;
				if (sadrzaj != ""){
					var wc = wordCount(sadrzaj, el.document.body.innerText);
					el.document.selection.empty();
					var caretNew = el.document.selection.createRange();
					caretNew.moveStart("word", wc)
					caretNew.text = MARK
					caretNew.select();
				} else {
					var caret=el.document.selection.createRange();
					el.curword=caret.duplicate();
					el.curword.text = MARK;
				}
			}
		} else {
//			sel = el.getSelection(); 
//			if (sel != null){
//				sel.removeAllRanges();
//			}
			insertHTML(el, "<mark id=\"markNode\">" + MARK+"</mark>");
		}

		var temporary = document.getElementById("Temp"+fID);
		temporary.innerHTML = el.document.body.innerHTML;

		var badTagsArray = new Array(
			"P", "UL", "LI","SPAN", 
			"TABLE", "TR", "TD", "A",  
			"B", "STRONG", "FONT", "CLASS", 
			"H1", "H2", "H3", "H4", 
			"H5", "H6", "DIV", "IMG", "META", "LINK", "STYLE", "TITLE");

		//GeckoUseSPAN
		for (j=0;j<badTagsArray.length;j++) {
			var bodyTags = (IE) ? temporary.all.tags(badTagsArray[j]) : temporary.getElementsByTagName(badTagsArray[j]);
			for (i=bodyTags.length-1;i >= 0;i--) {
				try {
					// ovo je za sve tagove
					badTag = bodyTags[i];
					badTag.removeAttribute ("size");
					badTag.removeAttribute ("face");
					switch (j) {
						case 0: //p tag
							badTag = saveAttribute(badTag, "align", "p");
							break;
						case 1: //ul tag
						case 2: //li tag
							badTag = removeAttributes(badTag);
							break;
						case 3: //span tag
							badTag = saveAttribute(badTag, "color", "font");
							//preimenovanje u font tag ne radi u mozilli, pravi neocekivane probleme
							//a nikakvi javascript error-i se ne vide
							break;
						case 4://table tag
							badTag = saveAttribute(badTag, "class", "table");
							badTag = saveAttribute(badTag, "width", "table");
							badTag.removeAttribute ("style");
							badTag.removeAttribute ("border");
							badTag.removeAttribute("cellSpacing");
							badTag.removeAttribute("cellPadding");
							break;
						case 5: //tr tag
						case 6:	//td tag
							badTag.removeAttribute ("style");
							badTag.removeAttribute ("bgColor");
							badTag.removeAttribute ("vAlign");
							badTag = saveAttribute(badTag, "class", badTag.nodeName);
							break;
						case 7: //a tag
							badTag = saveAttribute(badTag, "href", "a");
							break;
						case 8://b
						case 9: //strong
							badTag = removeAttributes(badTag);
							badTag.nodeName = "STRONG";
							break;
						case 10: //font tag
							badTag = saveAttribute(badTag, "color", "font");
							break;
						case 18://div tag
							if (!IE){
								badTag = saveAttribute(badTag, "align", "div");
							} else {
								badTag.outerHTML = badTag.innerHTML;
							}							
							break;
						case 19://img tag
							if (badTag.hasAttribute('src')) {
								var src=badTag.getAttribute('src');
								re = /^file:\/\/(?:.*)(\/.*)(\/.*)$/i;
								var found = src.match(re);
								if (found != null) {
									src = "/upload/images/radovi" + found[1] + found[2];
									badTag.setAttribute("src", src);
								}
							}
							break;
						default: //ostali
							if (IE){
								badTag.outerHTML = badTag.innerHTML;
							} else {
								if (badTag.parentNode != null){
									var parent = badTag.parentNode;
									parent.removeChild(badTag);
									j--;
								} else {
									badTag = removeAttributes(badTag);
								}
							}
							break;

					}
					bodyTags[i] = badTag;
				} catch (e) {
				}
			}
		}

		// izbacivanje glupih tagova
		var innerHTML = temporary.innerHTML;
		innerHTML = innerHTML.replace(/<!--(\w|\W)+?-->/g, "");
		innerHTML = innerHTML.replace(/ style=\"\"/g, "");
		innerHTML = innerHTML.replace(/<meta>/gi, "");
		innerHTML = innerHTML.replace(/<link>/gi, "");
		innerHTML = innerHTML.replace(/<o:p>/g, "");
		innerHTML = innerHTML.replace(/<\/o:p>/g, "");
		innerHTML = innerHTML.replace(/(&nbsp;)/g, " ");
		innerHTML = innerHTML.replace(/\n/g, " ");

		temporary.innerHTML = innerHTML;
		temporary.innerHTML = removeFontSize0(temporary.innerHTML);
		if (IE)
			temporary.all.tags = removeEmptyTags(temporary.all.tags);
		else 
			temporary = removeEmptyTags(temporary);
		
		var esc = escape(temporary.innerHTML);

		regEx = /%0A/g;
		s = esc
		r = s.replace(regEx, "");
		esc = r;

		regEx = /%0D/g;
		s = esc
		r = s.replace(regEx, "");
		esc = r;
				
		esc = unescape(esc);
		
		//alert(esc);
			
		var textArea = eval('document.getElementById("formObject").'+textareaName+'.value');
		textArea.value = esc
		el.document.body.innerHTML =esc; 

//		alert(esc);

		if (IE){//ocuvanje onoga sto je bilo selektovano u modu koji se napusta
			if(selType!="Control"){
				caret = el.document.selection.createRange();
				var found= caret.findText(MARK,100000,5) // backward
				if(found==false) found= caret.findText(MARK,100000,4) // foreward

				if(found==false && format[fID]=="HTML") {
					var strx= el.document.body.innerHTML
					strx= strx.replace(/ViEtDeVtRiCk/ig,"");
					el.document.body.innerHTML= strx
					return;
				}
				caret.select();
				el.curword=caret.duplicate();
				el.curword.text = '' ;							// erase trick selection 
				caret.select();  
				caret.scrollIntoView();
			}
		} else {
			setCursor(el, el.document.getElementById("markNode"), 0);
		}
	}


/* Funkcija koja cuva atribute
======================================*/
	function saveAttribute(badTag, attribute, newTagName){
		switch (attribute){
			case "color":
					
					var colorVar = badTag.color;
					if (isValid(colorVar)){
						if (isOcpColor(colorVar)){ 
							badTag.color = colorVar;
						} else {
							if (IE){
								badTag.outerHTML = "<"+newTagName+">"+badTag.innerHTML+"</"+newTagName+">";
							} else{
								badTag = removeAttributes(badTag);
							}
						}
					} else {
						var colorVar = badTag.style.color;
						if (!isValid(colorVar) || !isOcpColor(colorVar)) {
							if (IE){
								badTag.outerHTML = "<"+newTagName+">"+badTag.innerHTML+"</"+newTagName+">";
							} else{
								badTag = removeAttributes(badTag);
							}
						} else {
							if (IE) {
								badTag.outerHTML = "<"+newTagName+" style=\"color:"+colorVar+"\">"+badTag.innerHTML+"</"+newTagName+">";
							} else {
								badTag = removeAttributes(badTag);
								badTag.setAttribute("style", "color:"+colorVar);
							}
						}
					}
					break;
			case "align":
					var alignVar = badTag.getAttribute("align") ;
					if (!isValid(alignVar)) {
						if (IE){
							badTag.outerHTML = "<"+newTagName+">"+badTag.innerHTML+"</"+newTagName+">";
						} else{
							badTag = removeAttributes(badTag);
						}
					} else {
						if (IE){
							badTag.outerHTML = "<"+newTagName+" align=\""+alignVar+"\">"+badTag.innerHTML+"</"+newTagName+">";
						} else{
							badTag = removeAttributes(badTag);
							badTag.setAttribute("align", alignVar);
						}
					}
					break;
			case "class":
					if (newTagName == "table"){
						var classVar = badTag.className;

						var foundClass = false;
						for (var i=0; i<tableArr.length; i++){
							if (classVar == tableArr[i]){
								foundClass = true;
								break;
							}							
						}

						if (foundClass) {
							;
						} else {
							badTag.removeAttribute ("class");
							badTag.className = tableArr[0];
						}	
					} else {
						var classVar = badTag.className;

						var foundClass = false;
						for (var i=0; i<cellsArr.length; i++){
							if (classVar == cellsArr[i]){
								foundClass = true;
								break;
							}							
						}

						if (foundClass) {
							;
						} else {
							badTag.removeAttribute ("class");
							badTag.className = cellsArr[0];
						}
					}
					break;
			case "width":
					var widthVar = badTag.width;
					if (isValid(widthVar)) {
						badTag.width = widthVar;
					} else {
						var styleWidthVar = badTag.style.width;
						if (isValid(styleWidthVar))
							badTag.width = styleWidthVar;
					}
					break;
			case "href":
					var urlVar = badTag.href;
					var targetVar = badTag.target;
//					if (IE){
//						badTag.outerHTML = "<"+newTagName+" href=\""+urlVar+"\">"+badTag.innerHTML+"</"+newTagName+">";
//					} else{
						badTag = removeAttributes(badTag);
						if (urlVar != null){
							badTag.setAttribute("href", urlVar);
						}
						if (targetVar != null){
							badTag.setAttribute("target", targetVar);
						}
//					}
					break;
		
		}
		
//		badTag.nodeName = newTagName;
		return badTag;
	}
	

/* Funkcija koja proverava boju
======================================*/
	function isOcpColor(color){
		var ocpColor = false;
		for (var k=0; k<colorsArr.length; k++){
			if (color.toLowerCase() == colorsArr[k].toLowerCase()){
				ocpColor = true;
				break;
			} 
		}
		return ocpColor;
	}

/* Funkcija koja odredjuje validnost
======================================*/
	function isValid(variable){
		return ((variable != null) && (variable != "") && (variable != "null") && (variable != "undefined") && (variable != "NaN"));
	}

/* Funkcija koja cisti <FONT size=+0></FONT>
======================================*/
	function removeFontSize0(innerText){
		var replaceStr = "<FONT size=+0>";

		while (innerText.indexOf(replaceStr) != -1){
			var poz = innerText.indexOf(replaceStr);
			var endPoz = innerText.indexOf("</FONT>", poz+1);
			var tempStr = innerText.substring(0, poz) + innerText.substring( poz + replaceStr.length, endPoz) + innerText.substring(endPoz + 7, innerText.length);
			innerText = tempStr;
		}
		return innerText;
	}

/* Funkcija koja cisti <SPAN></SPAN>
<FONT></FONT>
<P></P>
<SPAN>neki tekst</SPAN>
<FONT>neki tekst</FONT>
======================================*/
	function removeEmptyTags(temporary){
		var badTagsArray = new Array("SPAN", "LI", "FONT", "P", "UL");
		
		if (IE){
			var tags = temporary;
			for (var j=0;j<badTagsArray.length;j++) {
				for (var i = tags(badTagsArray[j]).length-1; i >= 0; i--) {
					try {
						badTag = tags(badTagsArray[j])[i];

						if (!isValid(trim(badTag.innerHTML))){
							badTag.outerHTML = "";
						} else if (badTagsArray[j] == "SPAN" || badTagsArray[j] == "FONT"){
							if (!isValid(badTag.style.color) && !isValid(badTag.style.display) && !isValid(badTag.color) ){
								badTag.outerHTML = badTag.innerHTML;
							}
						}
					} catch (e) {
					}
				}
			}
			temporary = tags;
		} else {
			for (var j=0;j<badTagsArray.length;j++) {
				var bodyTags = temporary.getElementsByTagName(badTagsArray[j]);
				for (var i = 0; i < bodyTags.length; i++) {
					try {
						badTag = bodyTags[i];
						if (!isValid(trim(badTag.innerHTML))){
							var parentNode = badTag.parentNode;
							parentNode.removeChild(badTag);
							i--;
						} else if (badTagsArray[j].toUpperCase() == "SPAN" || badTagsArray[j].toUpperCase() == "FONT"){
							if (!isValid(badTag.style.color) && !isValid(badTag.style.display) && !isValid(badTag.color) ){
								badTag = removeAttributes(badTag);
							}
						}
					} catch (e) {
					}
				}
			}
		}
		return temporary;
	}

	function getSelectedHTML(obj){
		var rng=null,html="";

		if (obj.document.selection && obj.document.selection.createRange){
			rng=obj.document.selection.createRange();
			html=rng.htmlText||"";
		}else if (obj.contentWindow.getSelection){
			rng=obj.contentWindow.getSelection();

			if (rng.rangeCount > 0 && obj.contentWindow.XMLSerializer){
				rng=rng.getRangeAt(0);
				html=new XMLSerializer().serializeToString(rng.cloneContents());
			}
		}
		return html;
	}

	function trim(value){
		value = value.replace(/^( )+/g, "");
		value = value.replace(/( )+$/g, "");
		return value;
	}

	function removeAttributes(tag){
		for (var k=0; k<tag.attributes.length; k++){
			tag.removeAttribute(tag.attributes[k].name);
			if (!IE) k--;
		}
		return tag;
	}