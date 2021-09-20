<?php

require_once($_SERVER['DOCUMENT_ROOT']."/ocp/config/db.php");

class dbase {
	var $link;
	var $connected = false;
	var $result;
	var $error_no;
	var $error_msg = "";
	var $last_query = "";

	/**
	 * @return void
	 * @desc otvara novu konekciju
	 */

	function open() {
		global $ocpDbServer, $ocpDbDbname, $ocpDbUsename, $ocpDbPassword;

		// $db_user = db_getCredentals();
		$db_user = array($ocpDbServer, $ocpDbDbname, $ocpDbUsename, $ocpDbPassword);
		$link = @mysql_connect($db_user[0], $db_user[2], $db_user[3]);
		if ($link) {
			if(!mysql_select_db($db_user[1])){
				mysql_create_db($db_user[1]);
				mysql_select_db($db_user[1]);
			}
			$this->link = $link;
			$this->connected = true;
		} else {
			$this->getError();
		}
	}

	/**
	 * @return void
	 * @param sql strin
	 * @desc izvrsava query
	 */
	function query($sql){
		$this->last_query = $sql;
		// die();

		$res = mysql_query($sql, $this->link) or die(mysql_error());

		// var_dump($res);
		// die();

		if ($res) {
			$this->result = $res;
			return $res;
		} else {
			$this->getError();
			return NULL;
		}
	}

	/**
	 * @return int
	 * @desc daje broj redova rezultata poslednjeg query-a
	 */
	function rowCount(){
		return mysql_num_rows($this->result);
	}

	function begin(){
		//$this->query("BEGIN");
	}

	function rollback(){
		//$this->query("ROLLBACK");
	}

	function commit(){
		//$this->query("COMMIT");
	}

	/**
	 * @return void
	 * @desc zatvara konekciju
	 */
	function close() {
	/*
		if(mysql_close($this->link)){
			$this->connected=false;
		}
	*/
	}

	function getError(){
		$this->error_no = mysql_errno();
		$this->error_msg = mysql_error();
//		printf("<hr><b>MySQL ERROR (%d) - %s<br></b>%s<hr>",$this->error_no,$this->error_msg,nl2br($this->last_query));
//		trigger_error("mysql error", E_USER_ERROR);
	}
}

	//Fetch type
	$FETCH_ASSOC 	= 0;
	$FETCH_OBJECT 	= 1;
	$FETCH_ARRAY 	= 2;

	/*Pakuje rezultate query-ija u niz
	nizova, odnosno objekata
	========================================*/
	function con_getResults($strSQL, $fetchType = NULL){
		global $FETCH_ARRAY, $FETCH_ASSOC, $FETCH_OBJECT;

		$retArr = array();

		$cn = new dbase();
		$cn->open();

		$res = $cn->query($strSQL);
		if (!is_null($res)){
			$fetchType = (is_null($fetchType)) ? $FETCH_ASSOC : $fetchType;

			switch ($fetchType){
				case $FETCH_OBJECT:
					while ($record = mysql_fetch_object($res)) $retArr[] = $record;
					break;
				case $FETCH_ARRAY:
					while ($record = mysql_fetch_array($res)) $retArr[] = $record;
					break;
				case $FETCH_ASSOC:
					while ($record = mysql_fetch_assoc($res)) $retArr[] = $record;
					break;
			}
			$cn->close();
		}
		else {
		    die(mysql_error());
		}

		return $retArr;
	}

	/*Pakuje rezultate query-ija u niz
	niz[prva kolona] = druga kolona
	========================================*/
	function con_getResultsDict($strSQL){
		$retArr = array();

		$cn=new dbase();
		$cn->open();

		$res = $cn->query($strSQL);
		if (!is_null($res)){
			while ($record = mysql_fetch_array($res)) {
				$retArr[$record[0]] = $record[1];
			}
			$cn->close();
		}
		return $retArr;
	}

	/*Pakuje rezultate query-ija u niz (1 kolona)
	========================================*/
	function con_getResultsArr($strSQL){
		$retArr = array();

		$cn=new dbase();
		$cn->open();

		$res = $cn->query($strSQL);
		if (!is_null($res)){
			while ($record = mysql_fetch_array($res)) {
				$retArr[] = $record[0];
			}
			$cn->close();
		}
		return $retArr;
	}

	/*Pakuje rezultat query-ija u dictionary
	========================================*/
	function con_getResult($strSQL, $fetchType = NULL){
		global $FETCH_ARRAY, $FETCH_ASSOC, $FETCH_OBJECT;



		$result = array();

		$cn=new dbase();
		$cn->open();

		$res = $cn->query($strSQL);
		if (!is_null($res)){
			$fetchType = (is_null($fetchType)) ? $FETCH_ASSOC : $fetchType;
			switch ($fetchType){
				case $FETCH_OBJECT:
					if ($record = mysql_fetch_object($res)) $result = $record; break;
				case $FETCH_ARRAY:
					if ($record = mysql_fetch_array($res)) $result = $record; break;
				case $FETCH_ASSOC:
					if ($record = mysql_fetch_assoc($res)) $result = $record; break;
			}
			$cn->close();
		}

		return $result;
	}

	/*Vraca jednu vrednost
	========================================*/
	function con_getValue($strSQL){
		$retVal = "";

		$cn=new dbase();
		$cn->open();

		$res = $cn->query($strSQL);
		if (!is_null($res)){
			if ($record = mysql_fetch_array($res)) $retVal = $record[0];

			$cn->close();
		}

		return $retVal;
	}

	/*Izvrsava insert/update/delete iskaz
	========================================*/
	function con_update($updateStr){
		$executeUpdate = 0;

		$cn=new dbase();
		$cn->open();

		$cn->query($updateStr);
		$executeUpdate = mysql_affected_rows();
		$cn->close();

		return $executeUpdate;
	}

	/*Izvrsava insert iskaz
	========================================*/
	function con_insert($updateStr){
		$cn=new dbase();
		$cn->open();

		$cn->query($updateStr);
		$id = mysql_insert_id($cn->link);
		$cn->close();

		return $id;
	}

	/*Izvrsava delete iskaz
	========================================*/
	function con_delete($updateStr){
		$cn=new dbase();
		$cn->open();

		$cn->query($updateStr);
		$executeUpdate = mysql_affected_rows();
		$cn->close();

		return $executeUpdate;
	}
?>
