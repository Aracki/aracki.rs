<?php

function insertData($export, $srpske_reci) {
    global $rec_inserted, $rec_deleted;

    if ($export['rec'] !== null && $export['prevod'] !== null) {

		if ($srpske_reci == 1) {
			$table = "reci_srpeng";
		}else{
			$table = "reci_engsrp";
		}
       
        $sql = "SELECT * FROM ".$table." WHERE rec = '".$export['rec']."' AND prevod = '".$export['prevod']."' AND Valid = 1";
        $res = con_getResults($sql);        
        
        if (!empty($res) && count($res) > 0) {
            $sql = "UPDATE ".$table." SET Valid = 0 WHERE rec = '".mysql_real_escape_string(strtolower($export['rec']))."' AND prevod = '".mysql_real_escape_string(strtolower($export['prevod']))."' AND Valid = 1";
            con_update($sql);
			var_dump($sql); 
			$rec_deleted++;
        } 

		//$sql = "INSERT INTO ".$table." (rec, prevod, Valid) VALUES ('".mysql_real_escape_string($export['rec'])."', '".mysql_real_escape_string($export['prevod'])."', 1)";
		$sql = "INSERT INTO ".$table." (rec, prevod, Valid) VALUES ('".mysql_real_escape_string(strtolower($export['rec']))."', '".mysql_real_escape_string(strtolower($export['prevod']))."', 1)";

		
		$mod_id = con_insert($sql); 
		var_dump($sql); 
		$rec_inserted++;
        return $mod_id;
    }
}

/**
 * Hooks to be executed before inserting an object to the database
 * @param string $type Typename of the object (database table name)
 * @param array $object An associative array with column names as keys and object values as values
 * @return array Modified object
 */
function obj_beforeInsert($type, $object) {
    return $object;
}

/**
 * Hooks to be executed after inserting an object to the database
 * @param string $type Typename of the object (database table name)
 * @param array $object An associative array with column names as keys and object values as values
 * @return void
 */
function obj_afterInsert($type, $object) {
	if ($type == "Import"){
		$sql = "UPDATE Import SET datum = NOW() WHERE Id = ".$object['Id'];
		con_update($sql);

		global $rec_inserted, $rec_deleted;
		
		$inserted = array();

		$str = file_get_contents($_SERVER['DOCUMENT_ROOT'].$object['fajl']);
		$bom = pack("CCC", 0xef, 0xbb, 0xbf); 
		if (0 == strncmp($str, $bom, 3)) { 
			echo "BOM detected - file is UTF-8\n";
			$str = substr($str, 3); 
		}
			
		$row = 0;
		setlocale(LC_ALL, "en_US.UTF-8");
		if (($handle = fopen($_SERVER['DOCUMENT_ROOT'].$object['fajl'], "r")) !== FALSE) {
			while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {				
				 if ($data[0] != '' && $data[1] != '') {
					$export['rec']  = trim($data[0]);
					$export['prevod']  = trim($data[1]);
					$inserted[] = insertData($export,$object['srpske_reci']);
				 }
			}
			fclose($handle);
		}

		echo "<p>Broj dodatih: $rec_inserted</p>";
		echo "<p>Broj obrisanih: $rec_deleted</p>";

		die();
	}
}

/**
 * Hooks to be executed before updating an object in the database
 * @param string $type Typename of the object (database table name)
 * @param array $object An associative array with column names as keys and object values as values
 * @return array Modified object
 */
function obj_beforeUpdate($type, $object) {
    return $object;
}

/**
 * Hooks to be executed after updating an object in the database
 * @param string $type Typename of the object (database table name)
 * @param array $object An associative array with column names as keys and object values as values
 * @return void
 */
function obj_afterUpdate($type, $object) {
	if ($type == "Import"){
		$sql = "UPDATE Import SET datum = NOW() WHERE Id = ".$object['Id'];
		con_update($sql);
		
		global $rec_inserted, $rec_deleted;
		
		$inserted = array();
 
		$str = file_get_contents($_SERVER['DOCUMENT_ROOT'].$object['fajl']);
		$bom = pack("CCC", 0xef, 0xbb, 0xbf); 
		if (0 == strncmp($str, $bom, 3)) { 
			echo "BOM detected - file is UTF-8\n";
			$str = substr($str, 3); 
		}

		$row = 0;
		setlocale(LC_ALL, "en_US.UTF-8");
		if (($handle = fopen($_SERVER['DOCUMENT_ROOT'].$object['fajl'], "r")) !== FALSE) {
			while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {				
				 if ($data[0] != '' && $data[1] != '') {
					$export['rec']  = trim($data[0]);
					$export['prevod']  = trim($data[1]);
					$inserted[] = insertData($export,$object['srpske_reci']);
				 }
			}
			fclose($handle);
		}

		echo "<p>Broj dodatih: $rec_inserted</p>";
		echo "<p>Broj obrisanih: $rec_deleted</p>";

		die();
	}
}

/**
 * Hooks to be executed before deleting an object form the database
 * @param string $type Typename of the object (database table name)
 * @param integer $id Id of the deleted object
 * @return void
 */
function obj_beforeDelete($type, $id) {
}

/**
 * Hooks to be executed after deleting an object form the database
 * @param string $type Typename of the object (database table name)
 * @param integer $id Id of the deleted object
 * @return void
 */
function obj_afterDelete($type, $id) {
}

/**
 * Hooks to be executed before object form is rendered
 * @param string $type Typename of the object (database table name)
 * @param array $object An associative array with column names as keys and object values as values
 * @param integer $id Id of the deleted object
 * @return array Modified object
 */
function obj_preForm($type, $object, $id) {
    return $object;
}
?>