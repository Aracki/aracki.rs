<?
require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/include/connect.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/include/utils.php");

class Recnik {

	protected $rec, $prevod, $lang, $letter, $table;

	public function __construct($lang, $letter) {
		$this->setData($lang, $letter);			
	}

	public function setData($lang, $letter) {
		$this->lang = $lang;
		$this->letter = $letter;

		if ($this->lang == "es") {
			$this->table = "reci_engsrp";
		}else{
			$this->table = "reci_srpeng";
		}
	}

	public function printData() {
		echo $this->lang . " & " . $this->letter. " & " . $this->table;
	}

	public function getWordList() {
		$sql = "SELECT 	rec, prevod 
				FROM ".$this->table." 
					WHERE Valid = 1 AND
					rec LIKE '".trim(strtolower($this->letter))."%'
				ORDER BY rec";					
		return con_getResults($sql, 1);
	}

	public function getPrevod($rec) {
		$sql = "SELECT 	prevod 
				FROM ".$this->table." 
					WHERE Valid = 1 AND
					LOWER(rec) = '".trim(strtolower($rec))."'";					
		return con_getValue($sql, 1);
	}

}
?>