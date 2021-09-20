<HTML>
	<HEAD>
		<TITLE>Paleta boja</TITLE>
		<META http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<script language="javascript" type="text/javascript" src="/ocp/jscript/swfobject.js"></script>
		<SCRIPT LANGUAGE="javascript">
			function setColor(hval) {
				window.opener.colorFieldFill(hval);
				self.close();
			}
		</SCRIPT>  
	</HEAD>
	<BODY BGCOLOR="#FFFFFF" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0"  class="ocp_body" onLoad="window.focus()">
		<div align="center">
			<div id="create_color"></div>
			<script type="text/javascript">
			   var so = new SWFObject("/ocp/controls/colorControl/colorChoose.swf?boja="+window.opener.getColorFieldFill(), "color", "250", "200", "6", "#C0C0C0");
			   so.write("create_color");
			</script>
		</div>
	</BODY>
</HTML>