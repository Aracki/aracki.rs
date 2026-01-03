<?php
	require_once("../../include/connect.php");
	require_once("../../include/session.php");
?>
<HTML>
<HEAD>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="STYLESHEET" type="text/css" href="/ocp/css/opsti.css">
<link rel="STYLESHEET" type="text/css" href="/ocp/css/opcije.css">
<link rel="STYLESHEET" type="text/css" href="/ocp/css/dugmici.css">
<script>
	var IE = (navigator.userAgent.indexOf("Firefox") == -1) ? true : false;
</script>
</HEAD>
<BODY class="ocp_body"><?php

	$_SESSION["ocp_searchResults"] = array();

	jsValidateOcpLabels();
	drawQuery();

	?><?php

?><script language="javascript">
	window.onload = function(){
		parent.document.getElementById("downFrameset").setAttribute("rows", "100%,*");
	}
	</script><?php

	function drawQuery(){
?><form action="/ocp/controls/search_replace/replace.php?<?php echo utils_randomQS()?>" name="formObject" method="POST" onSubmit="return validate();" style="display:inline;" target="replaceFrame">
	<input type="hidden" name="Akcija" value="Pretraga">
	<table class="ocp_opcije_table" border="0"> 
    <tr> 
      <td colspan="2" class="ocp_opcije_td_naslov"><?php echo ocpLabels('Search for')?> </td> 
    </tr>
	<tr> 
      <td align="left" class="ocp_opcije_td"><span class="ocp_opcije_tekst1"><?php echo ocpLabels('Where')?> </span></td> 
      <td class="ocp_opcije_td"><?php
		$disable = getSVar("ocpDisable");

		if (substr_count($disable, "siteManager") == 0){
		?><input name="content[]" type="radio" value="site" onfocus="this.checked=true;showAttributes();" onchange="showAttributes();"><span class="ocp_opcije_tekst2" style="margin-right: 3px"><?php echo ocpLabels("Site Manager")?> </span><?php 
		}
		
		if (substr_count($disable, "objectManager") == 0){
		?><input name="content[]" type="radio" value="objects" onfocus="this.checked=true;showAttributes();" onchange="showAttributes();"><span class="ocp_opcije_tekst2" style="margin-right: 3px"><?php echo ocpLabels("Object Manager")?> </span><?php
		}	

		if (substr_count($disable, "siteManager") == 0 && substr_count($disable, "objectManager") == 0){
		?><input name="content[]" type="radio" value="all"  onfocus="this.checked=true;showAttributes();" onchange="showAttributes();" checked><span class="ocp_opcije_tekst2" style="margin-right: 3px"><?php echo ocpLabels("Entire Site Content")?> </span><?php 
			
		}
		?><div style="display:none;height:100px;" id="sm_attributes"><br>
		<input name="attribute[]" type="checkbox" value="menuTitle" checked><span class="ocp_opcije_tekst2" style="margin-right: 3px"><?php echo ocpLabels("Menu title")?> </span><br>
		<input name="attribute[]" type="checkbox" value="htmlTitle" checked><span class="ocp_opcije_tekst2" style="margin-right: 3px"><?php echo ocpLabels("Html title")?> </span><br>
		<input name="attribute[]" type="checkbox" value="htmlKeywords" checked><span class="ocp_opcije_tekst2" style="margin-right: 3px"><?php echo ocpLabels("Html keywords")?> </span><br>
		<input name="attribute[]" type="checkbox" value="htmlDescription" checked><span class="ocp_opcije_tekst2" style="margin-right: 3px"><?php echo ocpLabels("Html description")?> </span><br>
		<input name="attribute[]" type="checkbox" value="extraParams" checked><span class="ocp_opcije_tekst2" style="margin-right: 3px"><?php echo ocpLabels("Extra parameters")?> </span>
		</div>
	  </td> 
    </tr> 
    <tr> 
      <td align="left" class="ocp_opcije_td"><span class="ocp_opcije_tekst1"><?php echo ocpLabels('Parameter')?> </span></td> 
      <td class="ocp_opcije_td"><input name="parameter" type="text" class="ocp_forma" style="width:100%;"></td> 
    </tr>
    <tr> 
      <td align="left" class="ocp_opcije_td"><span class="ocp_opcije_tekst1"><?php echo ocpLabels('Match case')?> </span></td> 
      <td class="ocp_opcije_td"><input name="matchCase" type="checkbox" value="1"></td> 
    </tr>
	<tr> 
      <td colspan="2" class="ocp_opcije_td_naslov"><?php echo ocpLabels('Replace with')?> </td> 
    </tr>
    <tr> 
      <td align="left" class="ocp_opcije_td"><span class="ocp_opcije_tekst1"><?php echo ocpLabels('Replacement')?> </span></td> 
      <td class="ocp_opcije_td"><input name="replacement" type="text" class="ocp_forma" style="width:100%;"></td> 
    </tr>
	<tr> 
      <td align="left" class="ocp_opcije_td"><span class="ocp_opcije_tekst1"><?php echo ocpLabels('Preview')?> </span></td> 
      <td class="ocp_opcije_td"><input name="preview" type="checkbox" value="1" checked>
	  </td> 
    </tr> 
    <tr>
      <td align="left" class="ocp_opcije_td">
     </td>
      <td align="left" class="ocp_opcije_td">
        <input type="submit" class="ocp_dugme_malo" value="<?php echo ocpLabels('Confirm')?> ">&nbsp;<input type="button" name="submitCancel" class="ocp_dugme_malo" onClick="document.formObject.reset();" value="<?php echo ocpLabels("Cancel")?> ">
      </td>
    </tr>
</table>
</form>
<script src="/ocp/validate/validate_double_quotes.js"></script>
<script src="/ocp/validate/user/is_necessary.js"></script>
<script>
	function showAttributes(){
		var radio = document.formObject.elements["content[]"];
		var checkedValue = "";
		for (var i=0; i<radio.length; i++){
			if (radio[i].checked){
				checkedValue = radio[i].value;
				break;
			}
		}

		switch (checkedValue){
			case "site":
				if (IE){ 
					document.getElementById('sm_attributes').style.display='block';
				} else {
					document.getElementById('sm_attributes').style.display='inline';
				}
				break;
			case "objects":
			case "all":
				document.getElementById('sm_attributes').style.display='none';
				break;
		}
	}

	function validate(){
		var forma = document.formObject;
		var value = true;
		
		value = is_necessary("formObject.parameter", null, "<?php echo ocpLabels("Parameter")?> ");
		if (value){
			if (!document.formObject.preview.checked && document.formObject.replacement.value == ""){
				if (!confirm("<?php echo ocpLabels("Found strings will be replaced with blank string. Do you still want to proceed?")?> ")){
					document.formObject.replacement.focus();
					value = false;
				}
			}
		}

		if (value){
			validate_double_quotes(document.formObject);
		}
		return value;
	}
</script><?php 
	}

?> </BODY>
</HTML>