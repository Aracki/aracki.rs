<?php 
	require_once("../../include/session.php");

	$fieldName = utils_requestStr(getGVar("field"));
?><HTML>
<HEAD>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="STYLESHEET" type="text/css" href="/ocp/css/opsti.css">
<link rel="STYLESHEET" type="text/css" href="/ocp/css/opcije.css">
<link rel="STYLESHEET" type="text/css" href="/ocp/css/dugmici.css">
<title>OCP</title>
<script>
//sta je sa blankom u pretrazi?
//ili je prazno ili ima vise od jedne reci
	field = "<?php echo $fieldName?>";
	formName = field.substring(0, field.indexOf("."));
	elemName = field.substring(field.indexOf(".")+1);

	function prepareSQL(){
		var forma = document.formObject;
		
		var all = trim(forma.all.value);
		var exact = forma.exact.value;	//ili je prazno ili ima vise od jedne reci
		var least = forma.least.value;
		var without = forma.without.value;

		if (all == "" && exact == "" && least == "" && without == "") this.close();
//		onload onblur (parent OR window OR top) "Firefox iframe" -event		
		var expression = all;
		if (exact != ""){
			if (expression != "") expression += " ";
			expression += "\""+ exact + "\"";
		}
		if (least != ""){
			if (expression != "") expression += " ";
			expression += "(";
			var leastArr = least.split(" ");
			expression += leastArr.join(" OR ");
			expression += ")";
		}
		if (without != ""){
			if (expression != "") expression += " ";			
			var withoutArr = without.split(" ");
			withoutExp = "";
			for (var i=0; i<withoutArr.length; i++)
				expression += "-" + withoutArr[i];
		}
		
		expression = trim(expression);
		//alert("Google search: '" + expression + "'");

		if (parent){
			parent.document.forms[formName].elements[elemName].value = expression;
		}
		closeMe();
	}

	function trim(value){
		value = value.replace(/^( )+/g, "");
		value = value.replace(/( )+$/g, "");
		return value;
	}

	function fillFromParent(){
		var expression = parent.document.forms[formName].elements[elemName].value;

		expression = trim(expression);

		var all = "";
		var least = "";
		var exact = "";
		var without = "";

		//disjunkcija
		var patternLeast = /[(][a-zA-Z_0-9 ]+[)]/i;
		var reArray = expression.match(patternLeast);
		var least = "";
		if (reArray != null){
			for (var i=0; i<reArray.length; i++){
				least = reArray[i].substring(1, reArray[i].length-1);
				least = least.replace(/( OR)/g, "");
				expression = expression.replace(patternLeast, "");
				expression = expression.replace(/  /g, " ");
			}
			//alert("Least "+least);
		}
		
		//exact phrase
		var patternExact = /\"[a-z0-9_ ]+\"/i;
		expression = expression.replace(/&quot;/g, "\"");
		reArray = expression.match(patternExact);
		var exact = "";
		if (reArray != null){
			for (var i=0; i<reArray.length; i++){
				exact = reArray[i].substring(1, reArray[i].length-1);
			}
			if (exact != ""){
				expression = expression.replace(patternExact, "");
				expression = expression.replace(/  /g, " ");
			}
			//alert("Exact "+exact);
		}

		//negacija
		var patternWithout = /\x2D[a-z0-9_ ]+/gi;
		reArray = expression.match(patternWithout);
		var without = "";
		if (reArray != null){
			for (var i=0; i<reArray.length; i++){
				without += reArray[i].substring(1) + " ";
			}
			if (without != ""){
				without = without.substring(0, without.length-1);
				expression = expression.replace(patternWithout, "");
				expression = expression.replace(/  /g, " ");	
			}
			//alert("Without "+without);
		}
		
		//konjukcija ostatak
		var all = trim(expression);
		//alert("All "+all);

		

		document.formObject.all.value = trim(all);
		document.formObject.exact.value = trim(exact);
		document.formObject.least.value = trim(least);
		document.formObject.without.value = trim(without);
	}

	function closeMe(){
		parent.document.getElementById("advancedSearch_"+elemName).style.display='none';
	}
</script>
</HEAD>
<BODY class="ocp_body">
	<form action="/ocp/controls/advanced_search/form.php?<?php echo utils_randomQS()?>" name="formObject" style="display:inline;">
	<table class="ocp_opcije_table" border="0"> 
	<tr> 
      <td align="left" class="ocp_opcije_td" style="width:30%"><span class="ocp_opcije_tekst1"><?php echo ocpLabels('with all of the words')?></span></td> 
      <td class="ocp_opcije_td"><input name="all" type="textbox" class="ocp_forma" style="width:100%;" value=""></td> 
    </tr> 
    <tr> 
      <td align="left" class="ocp_opcije_td"><span class="ocp_opcije_tekst1"><?php echo ocpLabels('with the exact phrase')?></span></td> 
      <td class="ocp_opcije_td"><input name="exact" type="text" class="ocp_forma" style="width:100%;" value=""></td> 
    </tr>
    <tr> 
      <td align="left" class="ocp_opcije_td"><span class="ocp_opcije_tekst1"><?php echo ocpLabels('with at least one of the words')?></span></td> 
      <td class="ocp_opcije_td"><input name="least" type="text" class="ocp_forma" style="width:100%;" value=""></td> 
    </tr>
	 <tr> 
      <td align="left" class="ocp_opcije_td"><span class="ocp_opcije_tekst1"><?php echo ocpLabels('without the words')?></span></td> 
      <td class="ocp_opcije_td"><input name="without" type="text" class="ocp_forma" style="width:100%;" value=""></td> 
    </tr>
    <tr>
    <tr>
      <td align="left" class="ocp_opcije_td">
     </td>
      <td align="left" class="ocp_opcije_td">
        <input type="button" class="ocp_dugme_malo" value="<?php echo ocpLabels('Confirm')?>" onclick="prepareSQL();">&nbsp;<input type="button" name="submitCancel" class="ocp_dugme_malo" onClick="closeMe();" value="<?php echo ocpLabels("Cancel")?>">
		<br><br>
      </td>
    </tr>
</table>
</form>
</BODY>
</HTML>