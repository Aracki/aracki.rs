var ocpMoveNode; //selected container name
var ocpMoveNodeCollection; //selected container list name

document.write('<div id="ocpMoveDivDenied" style="position:absolute;top:-50;left:-50;z-index:901;visibility:hidden;"><img src="/ocp/img/opsti/blokovi/blok_traka/blok_move_denied.gif"></div>');

var ocpMoveDont=false;
var ocpMove = false;
var ocpTypeAction = "";
var ocpMoveList = new Array();
var IE = (navigator.userAgent.indexOf("Firefox") == -1) ? true : false;

//startuje drag & drop
function ocpMoveNodeStart(containerList, container, barId, action){
	if (!ocpMoveDont && !ocpMove){
		ocpMoveNodeCollection=containerList;
		ocpMoveNode=container;

		var divDenied=document.getElementById('ocpMoveDivDenied');
		divDenied.style.visibility='visible'; // only if new selection
		ocpTypeAction = action;
		ocpMoveNodeSetClassName(barId, "START");
		ocpMove=true;
	}
}

//kada se desi mouseover headera
 function ocpMoveNodeHigh(bar){
	var tmp=bar.id.split('__');
	if (ocpMove){
		if (tmp[0]==ocpMoveNodeCollection && tmp[1] != ocpMoveNode){
			//same container list and not the container to move: highlight this bar
			bar.className="ocp_blok_traka_table_pomeri";
			var divDenied = document.getElementById('ocpMoveDivDenied');
			if (divDenied.style.visibility=='visible') divDenied.style.visibility='hidden';	
		} 
	}
}

//kada se desi mouseout headera
function ocpMoveNodeReset(bar){
	tmp=bar.id.split('__');
	if (ocpMove){
		if (tmp[0]==ocpMoveNodeCollection){
			//same container list and not the container to move: reset this bar
			bar.className="ocp_blok_traka_table";
			var divDenied = document.getElementById('ocpMoveDivDenied');
			if (divDenied.style.visibility == 'hidden') divDenied.style.visibility='visible';
		}
	}
}

//kraj drag & drop, pravo pomeranje pocinje
function ocpMoveNodeEnd(bar){
	if (ocpMove){
		var tmp=bar.id.split('__');
		if (ocpMoveNodeCollection==tmp[0] && ocpMoveNode!=tmp[1]){
			//ocpMoveNodeSetClassName(null, "END");
			if (ocpTypeAction == "move"){
				changeOrderBlok(ocpMoveNode, tmp[1], 'Pomeri');
			} else {
				changeOrderBlok(ocpMoveNode, tmp[1], 'Kopiraj');
			}
		}
	}
}

//pokrivanje mouse event-a
var xMousePos = 0; 
var yMousePos = 0;
function initMouseEvents(){
	// ns 4, check if needed for ns 6+
	if (navigator.appName=="Netscape"){
		document.captureEvents(Event.MOUSEMOVE);
		document.captureEvents(Event.MOUSEDOWN);
		document.captureEvents(Event.MOUSEUP);
	}
	document.onmousemove=ocpGetMousePos;
	document.onmousedown=ocpResetDown;
}

initMouseEvents();

//pozicioniranje layera na klik misem
function ocpGetMousePos(evt){
	var x,y;
	if (IE){
		x=window.event.clientX+document.body.scrollLeft;
		y=window.event.clientY+document.body.scrollTop;
	} else {
		x=evt.pageX;
		y=evt.pageY;
	}
	xMousePos = x;
	yMousePos = y;

	// moving content paragraphs
	if (ocpMove){
		var divDenied=document.getElementById('ocpMoveDivDenied');
		divDenied.style.left=x+5;
		divDenied.style.top=y-15;
	}
}

function ocpResetDown(){
    if (ocpMove){
		ocpMoveNodeSetClassName(null, "END");

		var divDenied=document.getElementById('ocpMoveDivDenied');
		divDenied.style.visibility='hidden';
		divDenied.style.left=-50;
		divDenied.style.top=-50;
		ocpMove=false;
		//trick! otherwise, by clicking a cont. of a different list, this one would be selected directly. now, the already selected will just be disabled
		ocpMoveDont = true;
		setTimeout("ocpMoveDont=false;",500);
	}
}

//postavlja ili sklanja div za pozicioniranje
function ocpMoveNodeSetClassName(barId, state){
	if (state == "END"){
		for (var i=0; i<ocpMoveList.length; i++){
			document.getElementById(ocpMoveList[i]).style.cursor = "auto";
			document.getElementById(ocpMoveList[i] + "_div").style.display = "block";
			document.getElementById(ocpMoveList[i] + "_div2").style.display = "block";
			document.getElementById(ocpMoveList[i] + "_div1").style.display = "none";
		}
		document.getElementById("blocks__last_position").style.visibility = "hidden";
	} else {
		for (var i=0; i<ocpMoveList.length; i++){
			if (ocpMoveList[i] != barId){
				if (ocpTypeAction == "move"){
					document.getElementById(ocpMoveList[i] + "_div1").innerText = " "+labMoveBlock;
					document.getElementById("blocks__last").innerHTML = "<span class='ocp_blok_traka_tekst_pom'>&nbsp;"+labMoveBlock+"</span>";
				} else {
					document.getElementById(ocpMoveList[i] + "_div1").innerText = " "+labCopyBlock;
					document.getElementById("blocks__last").innerHTML = "<span class='ocp_blok_traka_tekst_pom'>&nbsp;"+labCopyBlock+"</span>";
				}
				document.getElementById(ocpMoveList[i]).style.cursor = "hand";
				document.getElementById(ocpMoveList[i] + "_div").style.display = "none";
				document.getElementById(ocpMoveList[i] + "_div2").style.display = "none";
				document.getElementById(ocpMoveList[i] + "_div1").style.display = "block";
			}
		}
		document.getElementById("blocks__last_position").style.visibility = "visible";
		if (ocpTypeAction == "move"){
			document.getElementById("blocks__last").innerHTML = "<span class='ocp_blok_traka_tekst_pom'>&nbsp;"+labMoveBlock+"</span>";
		} else {
			document.getElementById("blocks__last").innerHTML = "<span class='ocp_blok_traka_tekst_pom'>&nbsp;"+labCopyBlock+"</span>";
		}
	}
 }