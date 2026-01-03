<?php 
	require_once("../../include/session.php");
?><HTML>
<HEAD>
<TITLE>OCP <?php echo ocpLabels("Date time control")?> </TITLE>
<script>
	function getDate(){
		var temp = "";
		temp = opener.getDate();
		if (temp == "" || temp =="undefined"){
			var today = new Date();
			temp = today.getFullYear()+"/"+ (today.getMonth()+1)+"/"+today.getDate()+" "+today.getHours()+":"+today.getMinutes()+":"+today.getSeconds();
		}
		window.document.kalendar.SetVariable("output", temp);
		window.document.kalendar.SetVariable("tommorow", '<?php echo ocpLabels("Tommorow")?>');
		window.document.kalendar.SetVariable("nextweek", '<?php echo ocpLabels("Next week")?>');
		window.document.kalendar.SetVariable("submit", '<?php echo ocpLabels("Submit")?>');
		window.document.kalendar.SetVariable("cancel", '<?php echo ocpLabels("Cancel")?>');
		window.document.kalendar.SetVariable("months", '<?php echo ocpLabels("January")?>,<?php echo ocpLabels("February")?>,<?php echo ocpLabels("March")?>,<?php echo ocpLabels("April")?>,<?php echo ocpLabels("May")?>,<?php echo ocpLabels("June")?>,<?php echo ocpLabels("July")?>,<?php echo ocpLabels("August")?>,<?php echo ocpLabels("September")?>,<?php echo ocpLabels("October")?>,<?php echo ocpLabels("November")?>,<?php echo ocpLabels("December")?>');
		window.document.kalendar.SetVariable("days", '<?php echo ocpLabels("Sunday")?>,<?php echo ocpLabels("Monday")?>,<?php echo ocpLabels("Tuesday")?>,<?php echo ocpLabels("Wednesday")?>,<?php echo ocpLabels("Thursday")?>,<?php echo ocpLabels("Friday")?>,<?php echo ocpLabels("Saturday")?>');
	}

	function setDate(value){
		opener.setDate(value);
		this.close();
	}
</script>
<script language="javascript" type="text/javascript" src="/ocp/jscript/swfobject.js"></script>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"></HEAD>
<BODY leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<div align="center">
	<div id="date"></div>
	<script type="text/javascript">
	   var so = new SWFObject("/ocp/controls/dateControl/calendar_omnicom.swf", "kalendar", "350", "250", "6", "#ffffff");
	   so.write("date");
	</script>
</div>
</BODY>
</HTML>
