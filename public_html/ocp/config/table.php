<?php
//!!!prilikom kreiranja nove table ili umetanja novih celija 
// uzima se tableArr[0] i cellsArr[0] klasa 
// kao default klase za odgovarajuce objekte

//table
$tableArr = array("table_main", "table_main1");
$tableLabelsArr = array("Yes", "No");

//cells
$cellsArr = array("td_colorless", "td_colorl_01","td_colorl_02","td_colorl_03", "td_header_row", "td_header_col");
$cellsLabelsArr = array("common cell", "cell style 1","cell style 2","cell style 3", "cell header 1", "cell header 2");


//kod dalje se ne menja, uvek je isti
?><script>
var tableArr = new Array();
var tableLabelsArr = new Array();
<?php
for ($i=0; $i<count($tableArr); $i++){
	?>tableArr[tableArr.length] = "<?php echo $tableArr[$i]?>";
	tableLabelsArr[tableLabelsArr.length] = "<?php echo $tableLabelsArr[$i]?>";<?php  
}
?>
var cellsArr = new Array();
var cellsLabelsArr = new Array();
<?php  
for ($i=0; $i<count($cellsArr); $i++){
	?>cellsArr[cellsArr.length] = "<?php echo $cellsArr[$i]?>";
	cellsLabelsArr[cellsLabelsArr.length] = "<?php echo $cellsLabelsArr[$i]?>";<?php  
}
?></script>
