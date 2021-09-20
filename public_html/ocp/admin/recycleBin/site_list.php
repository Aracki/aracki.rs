<?php
	require_once("../../include/connect.php");
	require_once("../../include/session.php");
	require_once("../../siteManager/lib/root.php");
	require_once("../../siteManager/lib/verzija.php");
	require_once("../../siteManager/lib/sekcija.php");
	require_once("../../siteManager/lib/stranica.php");
?>

<?php session_checkAdministrator(); ?>

<HTML>
<HEAD>
<link rel="stylesheet" href="/ocp/css/opsti.css">
<link rel="stylesheet" href="/ocp/css/opcije.css">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script language="javascript" type="text/javascript" src="/ocp/jscript/swfobject.js"></script>
</HEAD>
<BODY  class="ocp_body" onload="parent.detailFrame.location.href = '/ocp/html/blank.html';"><?php
	$action = utils_requestStr(getPVar("Action"));

	$verzije = verzija_getIds(true);
	$sekcije = sekcija_getIds(NULL, true);
	$stranice = stranica_getIds(true);

	if (utils_valid($action)){
		verzija_recycleBin($verzije);
		sekcija_recycleBin($sekcije);
		stranica_recycleBin($stranice);
	}
	
	drawRecycleBinTree($verzije, $sekcije, $stranice);
	
	function drawRecycleBinTree($verzije, $sekcije, $stranice){
?><div id="ocp_main_table"><form name="formObject" id="formObject" method="post" action="site_list.php?<?php echo utils_randomQS();?>">
<input type="hidden" name="Action" value="Sacuvaj">
<div id="ocp_blok_menu_1">
  <table class="ocp_blokovi_table">
    <tr>
      <td class="ocp_blokovi_td" style="padding-left: 6px;"><?php echo ocpLabels("Deleted site items");?>:</td>
      <td class="ocp_blokovi_td" style="text-align: right;"> </td>
    </tr>
  </table>
</div>
	<table border="0" cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td valign="top">
			<table class="ocp_opcije_table" style="width:100%;">
			 <tr style="top:0px">
				<td valign="top"  class="ocp_opcije_td" style="padding:0px;"><?php
		$swfArgs = "treeSource=/ocp/admin/recycleBin/tree_data.php";
		$swfArgs .= "&menuDisallowed=1&dragDisallowed=1";
		$swfArgs .= "&versionClickDisallowed=1&sectionClickDisallowed=1&pageClickDisallowed=1";
	?><div id="recycle_left"></div>
		<script type="text/javascript">
		   var so = new SWFObject("/ocp/flash/tree.swf?<?php echo $swfArgs?>", "security_tree", "100%", "400", "6", "#ffffff");
		   so.write("recycle_left");
		</script></td>
          </tr>
      </table></td>
	</tr>
	</table><?php
		for ($k=0; $k<count($verzije); $k++){
			?><input type="hidden" name="verz<?php echo $verzije[$k];?>" value=""><?php
		}
		for ($k=0; $k<count($sekcije); $k++){
			?><input type="hidden" name="sekc<?php echo $sekcije[$k];?>" value=""><?php
		}
		for ($k=0; $k<count($stranice); $k++){
			?><input type="hidden" name="stra<?php echo $stranice[$k];?>" value="">
			  <input type="hidden" name="stra<?php echo $stranice[$k];?>path" value=""><?php
		}
	?><table width="100%">
		<tr>
			<td height="40" align="center" class="ocp_text"><input type="submit" name="submitSave" class="ocp_dugme" value="<?php echo ocpLabels("Save");?>">&nbsp;<input type="button" name="submitCancel" class="ocp_dugme" onClick="document.formObject.reset();" value="<?php echo ocpLabels("Cancel");?>"></td>
		</tr>
	</table>
</form>
</div>
<script>
	//flash poziva ovu f=ju
	function setAction (action, id, type){
		switch (type){
			case "verzija":
				eval("document.formObject.verz"+id+".value="+action);
				break;
			case "sekcija":
				eval("document.formObject.sekc"+id+".value="+action);
				break;
			case "stranica":
				if (action==0) {
					popUp('/ocp/admin/recycleBin/site_popup.php?<?php echo utils_randomQS();?>&straId='+id);
				} 
				eval("document.formObject.stra"+id+".value="+action);				
				break;
		}
	}

	function popUp(url){
		var x = window.open(url, "popup", "top=10, left=50, width=800, height=400, scrollbars=yes, resizable=yes, status=yes");
		x.focus();
	}
</script><?php	
	}
?>
</BODY>
</HTML>