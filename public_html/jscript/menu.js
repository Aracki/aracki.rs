var meni_left = null;

function menuOn_left(menuName){
	if (meni_left != null) {
		setClass_left(false, meni_left);
	}

	meni_left = document.getElementById(menuName);
	setClass_left(true, meni_left);
	void(0);
}

function menuOff_left(menuName){
	var meni_left = document.getElementById(menuName);
	setClass_left(false, meni_left);
	void(0);
}

function setClass_left(over, where) {
	var hoverStr_left = 'hoverleft';

	if (where.className != "opened"  && where.className != "opened selected"  && where.className != "selected") {
		if (over) {
			where.className = hoverStr_left;
		} else {
			where.className = '';
		}		
	}

	test = where.getElementsByTagName("ul");
}

function adaptLis_left() {
	var lis_left = "";
	lis_left = document.getElementById('leftMenu').getElementsByTagName('li');

	if (lis_left != "" && lis_left != undefined && lis_left != null) {
		for (i = 0; i < lis_left.length; i++) {
			li_left = lis_left[i];

			if (li_left.className != 'opened') {
				li_left.onmouseover = function() {
					setClass_left(true, this);
				}

				li_left.onmouseout = function() {
					setClass_left(false, this);
				}
			}
		}
	}
}

var meni = null;

function menuOn(menuName){
	if (meni != null) {
		setClass(false, meni);
	}

	meni = document.getElementById(menuName);
	setClass(true, meni);
	void(0);
}

function menuOff(menuName){
	var meni = document.getElementById(menuName);
	setClass(false, meni);
	void(0);
}

function setClass(over, where) {
	var hoverStr = 'hover';

	if (over) {
		where.className = hoverStr;
	} else {
		where.className = '';
	}
}

function adaptLis() {
	var lis = "";
	lis = document.getElementById('mainMenu').getElementsByTagName('li');
	if (lis != "" && lis != undefined && lis != null) {
		for (i = 0; i < lis.length; i++) {
			li = lis[i];

			li.onmouseover = function() {
				setClass(true, this);
			}

			li.onmouseout = function() {
				setClass(false, this);
			}
		}
	}

	adaptLis_left();
}

window.onload = adaptLis;