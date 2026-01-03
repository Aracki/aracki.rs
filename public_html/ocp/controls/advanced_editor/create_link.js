var IE = (navigator.userAgent.indexOf("Firefox") == -1) ? true : false;
var OLDLINK= null; //da sacuvamo stari link

//if (!IE)
document.writeln('<script src="/ocp/controls/advanced_editor/showModalDialog.js"></script>');

function getParentTagA(el){
    var Range= el.document.selection.createRange();
    var parentTag=Range(0).parentElement;
    while(parentTag && parentTag.tagName!='BODY'){
        if(parentTag.tagName=='A'){
            return parentTag;
        } 
        parentTag= parentTag.parentElement;
    }
    return null
}

function init(){
    var href='', target='', type='', parentTag;

    if (IE){//IE
        var el= window.opener.document.getElementById(window.opener.fID).contentWindow;//window.dialogArguments;
        var sel = el.document.selection;
        if(sel==null) return

        var Range;
        Range = sel.createRange()
        temp = Range.parentElement();
			
        if (sel.type=="Control"){
            parentTag= getParentTagA(el)
            if(parentTag) return;
            href= parentTag.getAttribute('href')
            target= parentTag.getAttribute('target')
        } else {
            parentTag= Range.parentElement();
            if(parentTag.tagName!='A') return;
            href= parentTag.href
            target= parentTag.target
        }
    } else {//Firefox
        var win= window.opener.document.getElementById(window.opener.fID).contentWindow;
        var sel= win.getSelection();
        var range= sel.getRangeAt(0);

        var container = range.startContainer;
                        
        var el;
			
        if(container.nodeType != 1) el= container.parentNode;
        else el= container;

        /*			if ((range.startContainer == range.endContainer) && el.nodeName != "A"){//nema ugnjezdenih linkova tako da je ovaj kod uvek korektan
				var child = null;
				for (var i=0; i<el.childNodes.length; i++){
					var temp = el.childNodes[i];
					if (temp.nodeType == 1 && temp.nodeName == "A") {
						child = temp; break;
					}
				}
				if (child != null)	el = child;
			}*/


        if (el.href){
            href = el.href;
            target= el.target;
            OLDLINK= el;
        }
    }

    var idx= href.indexOf('://')
    if(idx>=0){
        type= href.substring(0,idx+3);
        href= href.substr(idx+3);
    } else {
        idx = href.indexOf(':');
        if (idx >=0 ){
            type = href.substring(0, idx+1);
            href = href.substr(idx+1);
        }
    }
		
    if (type != ''){
        document.forms[0].type.value= type
    }
    document.forms[0].href.value= href
    if (target == "_blank" ){
        document.forms[0].target.selectedIndex= 1;
    } else {
        document.forms[0].target.selectedIndex= 0;
    }
}

function doCreate(){
    var el = window.opener.document.getElementById(window.opener.fID).contentWindow; 
    /*		if(IE) el= window.opener.document.getElementById(window.opener.fID).contentWindow;//window.dialogArguments; 
		else el= window.opener.document.getElementById(window.opener.fID).contentWindow;*/

    var href= document.forms[0].href.value
    if(href==''){
        window.close();
        return;
    }

    if((href.indexOf('://') == -1) && (href.charAt(0) != '/'))
        href= document.forms[0].type.value + href;
    if (!IE){
        if(href.charAt(0) == '/') href= window.opener.siteURL + href;
    }
    var target= document.forms[0].target.value

    if(!IE){ // Mozilla
        if(!OLDLINK){
            //el.document.execCommand("CreateLink", false, href); dino: ovo ne dodaje target:_blank, kod ispod to radi
            var range = el.getSelection().getRangeAt(0);
            var contents = range.extractContents();
            var newLink = el.document.createElement("a");
            newLink.setAttribute("href", href);
            newLink.setAttribute("target", target);
            newLink.appendChild(contents);
            range.insertNode(newLink);
        } else{ 
            OLDLINK.href= href;
            OLDLINK.target= target;
        }
        window.close();
        return
    }  
		
    var el= window.opener.document.getElementById(window.opener.fID).contentWindow;//window.dialogArguments
		
    var sel = el.document.selection;
    if(sel==null) return
    var selType= sel.type
    var Range, parentTag;

    if(selType!='Control'){
        Range= sel.createRange();
        el.curword= Range.duplicate();
        if(el.curword.text=='' && Range.parentElement().tagName!='A'){
            el.curword.text= href;
            Range.moveEnd("character", href.length);
            el.curword= Range;
            el.curword.select(); 
        }
    }

    if(selType != 'Control'){
        Range = sel.createRange();
        parentTag= Range.parentElement();
				
        if (parentTag.tagName != 'A'){
            var targetStr = "";
            if(target)
                targetStr = " target='"+target+"'";
            var temp = "<a href='"+href+"' "+targetStr+">"+el.curword.text+"</a>";

            Range = sel.createRange();		//ako je nesto bilo selektovano ide u tabelu
            if(!Range.duplicate) return;
            Range.pasteHTML(temp);
        } else {
            el.document.execCommand("CreateLink",false,href);
            if(target) parentTag.target= target;
            else parentTag.removeAttribute('target');
        }
    } else {
        el.document.execCommand("CreateLink",false,href);
        parentTag= getParentTagA(el)
        if(target) parentTag.setAttribute('target',target);
        else parentTag.removeAttribute('target')
    }
    window.close()
}


function doUnLink(){
    if(IE){
        var el= window.opener.document.getElementById(window.opener.fID).contentWindow;//window.dialogArguments; 
        el.document.execCommand("UnLink",false,null)  
    } else {
        if(!OLDLINK) return       
        var parent= OLDLINK.parentNode
        var fchild= OLDLINK.firstChild
        var eclone= fchild.cloneNode(true)
        parent.insertBefore(eclone, OLDLINK);

        var nchild
        while(nchild= fchild.nextSibling){
            eclone= nchild.cloneNode(true)
            parent.insertBefore(eclone, OLDLINK);
            fchild= nchild
        }
        parent.removeChild(OLDLINK)
    }
    window.close();
}

/*Funkcija koja otvara popup za biranje dokumenta na serveru
============================================================*/
function anchorPage(){
    var d = new Date();

    var urlPage = window.open('/ocp/admin/siteManager/intlink.php?random='+Date.parse(d)+'&Id=&field=formaZaLink.href', 'intLink', 'top=100px, left=150px, width=600px, height=260px, scrollbars=no, resizable=no, status=yes');
    return false;
    if(urlPage==null) { 
        return; 
    } else {
        document.forms[0].href.value = urlPage;
    }
}
	
function anchorDocument(){
    var d = new Date();

    var urlDoca = showPopup('/ocp/controls/fileControl/frameset.php?random='+Date.parse(d)+'&root=/upload&field=formaZaLink.href', "intLink" ,  "dialogTop=100px; dialogLeft=50px; dialogWidth=760px; dialogHeight=560px; scrollbars=yes; resizable=yes; status=yes;");
    if(urlDoca==null) { 
        return; 
    } else {
        document.forms[0].href.value = urlDoca;
    }
}

function anchorBlock(){
    var d = new Date();

		
    var Stra_Id = null;
    var Stra_Id_Arr = document.forms[0].href.value.split("&");
    var Stra_Id_Arr = Stra_Id_Arr[0].split("Id=");
    if (Stra_Id_Arr.length > 1){
        Stra_Id = Stra_Id_Arr[1];
    } else if (document.forms[0].href.value.charAt(0) == "/"){
        Stra_Id = document.forms[0].href.value;
    } else {
        var el= window.opener.document.getElementById(window.opener.fID).contentWindow;
        Stra_Id_Arr = el.parent.parent.location.href.split("&");
        Stra_Id_Arr = Stra_Id_Arr[0].split("Id=");
        if (Stra_Id_Arr.length > 1){
            Stra_Id = Stra_Id_Arr[1];
        } else {
            Stra_Id_Arr = el.parent.parent.opener.location.href.split("&");
            Stra_Id_Arr = Stra_Id_Arr[0].split("Id=");
            if (Stra_Id_Arr.length > 1){
                Stra_Id = Stra_Id_Arr[1];
            }
        }
    }
		
    if (Stra_Id != null){
        var blokId = showPopup('/ocp/controls/advanced_editor/chooseblok.php?random='+Date.parse(d)+'&Stra_Id='+Stra_Id, "." ,  "dialogWidth:30em;dialogHeight:34em; edge:sunken;help:no;status:no");
    /*			if(blokId==null) {
				return;
			} else {
				if (IE && document.forms[0].href.value.lastIndexOf("#") > -1)
					document.forms[0].href.value = document.forms[0].href.value.substring(0, document.forms[0].href.value.lastIndexOf("#"));
				document.forms[0].href.value = document.forms[0].href.value+"#"+blokId;
			}*/
    }
}