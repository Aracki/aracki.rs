<?php
/*Vraca sve template u bazi
===========================*/	
	function template_getAll($sortName = NULL, $direction = NULL){
		$strSQL = "select * from Template where Temp_Valid=1";
		if (utils_valid($sortName)) $strSQL .= " order by ".$sortName." ".$direction;
		return con_getResults($strSQL);
	}

/*Vraca odredjeni template
==========================*/
	function template_get($Temp_Id){
		return con_getResult("select * from Template where Temp_Id=".$Temp_Id);
	}

/*Azurira postojeci template
=============================*/
	function template_edit($data){
		$strSQL = "update Template set Temp_Naziv='".$data["Temp_Naziv"]."', Temp_Url='".$data["Temp_Url"]."' where Temp_Id=".$data["Temp_Id"];
		con_update($strSQL);
	}

/*Kreira novi template
=======================*/
	function template_new($data){
		$strSQL = "insert into Template(Temp_Naziv, Temp_Url) values('".$data["Temp_Naziv"]."','".$data["Temp_Url"]."')"; 
		con_update($strSQL);
	}

/*Brise postojeci template
==========================*/
	function template_delete($Temp_Id){
		$count = con_getValue("select count(*) from Stranica where Stra_Temp_Id = ".$Temp_Id." and Stra_Valid=1");
		if (intval($count) > 0){	?>
	<script>
		alert("<?php echo ocpLabels("You cannot delete existing pages\' template.");?>");
		window.open("/ocp/admin/siteManager/templates_edit.php?<?php echo utils_randomQS();?>", "_self");
	</script>	
<?php	} else {
			con_update("update Template set Temp_Valid=0 where Temp_Id=".$Temp_Id);
		}
	}
		
?>
