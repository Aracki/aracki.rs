/*Funkcija koja izjednacava sadrzaj textarea i iframe-a
=======================================================*/
	function actualize(){
		var i=0;
		if (IE){
			while(document.all.tags('iframe')[i])
				setHiddenValue(document.all.tags('iframe')[i++].id);
		} else {
			while(document.getElementsByTagName('iframe')[i])
				setHiddenValue(document.getElementsByTagName('iframe')[i++].id)
		}
	}

/*Konkretna funkcija koja izjednacava sadrzaj 
textarea i iframe
============================================*/
	function setHiddenValue(fid){ 
		if(!fid) return;

		var strx= editorContents(fid);
		var idA= fid.split('VDevID');
		if(!idA[0]) return;

		var fobj= document.forms[idA[0]];
		if(!fobj) return;
		strx=exchangeTags(strx,"<div>","</div>","","")//delete trick div
		fobj[idA[1]].value=strx;
	}

/*Funkcija koja otvara kontrolu za boje
============================================================*/
	function openColorEditor() {
		var d = new Date();

		var el= (IE) ? document.frames[fID] : document.getElementById(fID).contentWindow;
		if(!el){
			alert(labClickEditor);
			return false;
		} 
		el.focus();
		var arr=showPopup('/ocp/controls/advanced_editor/create_color.php?random='+Date.parse(d), el, 
		"dialogWidth:400px;dialogHeight:123px; edge:sunken;help:no;status:no");
	}


/*Funkcija koja otvara prozore za kreiranje tabele 
============================================================*/
	function openTableCreator (){
		var d = new Date();

		var el= (IE) ? document.frames[fID] : document.getElementById(fID).contentWindow;
		if(!el){
			alert(labClickEditor);
			return false;
		}
		el.focus();
		var arr=showPopup('/ocp/controls/advanced_editor/create_table.php?random='+Date.parse(d), el, 
			"dialogWidth:400px;dialogHeight:143px;help:no;status:no");
		return true;
	}

	function openCellProperties(){
		var d = new Date();

	if(!cellSelected()) { 
		return; 
	} else {
		var arr=showPopup('/ocp/controls/advanced_editor/cell_properties.php?random='+Date.parse(d), cellSelect, 
			"dialogWidth:400px;dialogHeight:175px;edge:sunken;help:no;status:no");
	}
	
}

function openTableProperties(){
	var d = new Date();

	if(!cellSelected()) {
		return false;
	} else {
		var TABLE = "";
		if (IE){
			var TABLE = cellSelect.parentElement;
			while (TABLE.tagName != "TABLE"){
				TABLE = TABLE.parentElement;
			}
		}
		var arr = showPopup('/ocp/controls/advanced_editor/table_properties.php?random='+Date.parse(d), TABLE, 
			"dialogWidth:400px;dialogHeight:143px; edge:sunken;help:no;status:no");
		
		return true;
	}
}

function exchangeTags(text,oOpen,oClose,nOpen,nClose){
	var str1,str2,idx,idx1
	var len1=oOpen.length
	var len2=oClose.length
	var oOpen1=oOpen.substring(0,len1-1)
	var chr1=oOpen1.replace(/^(.)/,"$1TrickTag")
	var chr2=oClose.replace(/^(.)/,"$1TrickTag")
	var oOpen2
	while(1){
		str1=''
		while(2){
			idx=text.indexOf(oClose)
			if(idx<0)break
			str2=text.substring(0,idx)
			text=text.substr(idx+len2)
			idx1=str2.lastIndexOf(oOpen1)
			idx=str2.lastIndexOf(oOpen)
			if(idx1>=0&&idx>=0&&idx1>idx){
				oOpen2=str2.substring(idx1+len1-1,idx1+len1)
				str1+=str2.substring(0,idx1)+chr1+oOpen2
				str1+=str2.substr(idx1+len1)+chr2
				break
			}  else if(idx>=0) {
				str1+=str2.substring(0,idx)+nOpen
				str1+=str2.substr(idx+len1)+nClose
			} else str1+=str2+oClose
		} 
		str1+=text
		if(str1.indexOf(oOpen)<0)break
		text=str1
	}//while1

	str1=str1.replace(/TrickTag/g,"")
	return str1;
}