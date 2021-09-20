
	var dateField = "";
	
	function getDate(){
		eval("var dd = document."+dateField+"_dd.value");
		eval("var mm = document."+dateField+"_mm.value");
		eval("var yyyy = document."+dateField+"_yyyy.value");
		eval("var x_time = document."+dateField+"_time");
		x = "";
		if ((dd != "") && (mm != "") && (yyyy != "") && checkDateData(dd, mm, yyyy)){
			
			var x = yyyy + "/" + mm + "/" + dd;
			if (x_time && checkTimeData(x_time.value)){
				x += " " + x_time.value;
			} else {
				x += " 00:00:00"
			}
			return x;
		}
		//var today = new Date();
		//var x = today.getFullYear() + "/" + (today.getMonth()+1) + "/" + today.getDate() + " " + today.getHours() + ":" + today.getMinutes() + ":" + today.getSeconds();
		//alert (x);
		return x;
	}

	function setDate(value){
		if (value != ""){
			var dateValue = new Date(value);
			var dd = dateValue.getDate(); dd = (parseInt(dd) < 10) ? "0" + dd : dd;
			var mm = dateValue.getMonth() + 1; mm = (parseInt(mm) < 10) ? "0" + mm : mm;
			eval("document."+dateField+"_dd.value='"+dd+"'");
			eval("document."+dateField+"_mm.value='"+mm+"'");
			eval("document."+dateField+"_yyyy.value='"+dateValue.getFullYear()+"'");
			eval("var x_time = document."+dateField+"_time");
			if (x_time){
				var hh = dateValue.getHours(); hh = (parseInt(hh) < 10) ? "0" + hh : hh;
				var MM = dateValue.getMinutes(); MM = (parseInt(MM) < 10) ? "0" + MM : MM;
				var ss = dateValue.getSeconds(); ss = (parseInt(ss) < 10) ? "0" + ss : ss;
				x_time.value = hh+":"+MM+":"+ss;
			}
		}
	}

	function openDateFlash(event, target){
		var d = new Date();
	    var event = window.event;
		var windVar = window.open("/ocp/controls/dateControl/date.php?random="+Date.parse(d), "dateWindow", "left=100, top=100, width=350, height=250");
		windVar.focus();
		dateField = target;
	}

	function openDatetimeFlash(event, target){
		var d = new Date();
		var event = window.event;
		var windVar = window.open("/ocp/controls/dateControl/date.php?random="+Date.parse(d), "datetimeWindow", "left=100, top=100, width=350, height=250");
		windVar.focus();
		dateField = target;
	}

	function checkDateData(day, month, year){
		if (day != "" && day.substring(0, 1) == "0") 
			day = day.substring(1, day.length); 

		if (month != "" && month.substring(0, 1) == "0") 
			month = month.substring(1, month.length); 

		var day = parseInt(day);
		var month = parseInt(month);
		var reYear = /^[1-9]*\d{3}$/;
		var retVal = true;
		
		if ((day >= 1) && (day <= 31)){
			if ((month >= 1) && (month <= 12)){
				if (!reYear.test(year))
					retVal = false;
			} else retVal = false;
		} else retVal = false;

		return retVal;
	}

	function checkTimeData(time){
		var timeArr = time.split(":");
		if (timeArr.length < 2 && timeArr.length > 3) return false;
		var retVal = true;

		for (var i=0; i<timeArr.length; i++){
			var timeTemp = parseInt(timeArr[i]);
			if (isNaN(timeTemp)){
				retVal = false;
				break;
			}

			if ((i == 0) && (timeTemp < 0 && timeTemp > 23)){
				retVal = false;
				break;
			} else if (timeTemp < 0 && timeTemp > 59){
				retVal = false;
				break;
			}
		}
		return retVal;
	}