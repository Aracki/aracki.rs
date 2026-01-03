<?php
	$sessOcpLabels = (isset($_SESSION["ocpLabels"]))? $_SESSION["ocpLabels"] : "";
	
	/*Vraca prevod labele iz sessije, 
	ako labele nema	vraca default vrednost
	======================================*/
	function ocpLabels($key){
		global $sessOcpLabels;
		$value = (isset($sessOcpLabels[$key]))? $sessOcpLabels[$key] : "";
		if (!utils_valid($value)) return $key . "!";
		return $value;
	}

	/*Vraca labele za validacione js-ove
	======================================*/
	function jsValidateOcpLabels(){
		global $sessOcpLabels;

		if (isset($sessOcpLabels) && !is_null($sessOcpLabels)){ //nije istekao session labela
?>

<script>
	var labField = "<?php echo ocpLabels("Field")?>";
	var labMustHaveValue = "<?php echo ocpLabels("must have value")?>";
	var labDateNotValid = "<?php echo ocpLabels("Date is not valid, please use calendar.")?>";
	var labDimensionValid = "<?php echo ocpLabels("Dimension is in format width x height")?>";
	var labHeightNumber = "<?php echo ocpLabels("Height is number")?>";
	var labWidthNumber = "<?php echo ocpLabels("Width is number")?>";
	var labEmailCorrect = "<?php echo ocpLabels("Email is not correct")?>";
	var labIsNumber = "<?php echo ocpLabels("is a number")?>";
	var labTwoImages = "<?php echo ocpLabels("You have to fill at least one image url")?>";
	var labTextImage = "<?php echo ocpLabels("You have to fill at least one of the fields: Title, Text or Image url")?>";
	var labLargeImage = "<?php echo ocpLabels("You have to fill image url")?>";
</script>

<?php	
		}
	}

	/*Vraca labele za block move js-ove
	======================================*/
	function jsBlockOcpLabels(){
		global $sessOcpLabels;

		if (isset($sessOcpLabels) && !is_null($sessOcpLabels)){ //nije istekao session labela
?>
<script>
	var labMoveBlock = "<?php echo ocpLabels("CLICK HERE TO MOVE BLOCK ABOVE THIS BLOCK")?>";
	var labCopyBlock = "<?php echo ocpLabels("CLICK HERE TO COPY BLOCK ABOVE THIS BLOCK")?>";
</script>
<?php	
		}
	}
?>