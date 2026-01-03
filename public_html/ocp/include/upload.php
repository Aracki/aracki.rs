<?php
/* ================================================================================
	-generise uploads-e (Id, Labela, SFN, Datetime, Type) formatirane za tabelu
	-vrati broj uploads-a
===================================================================================*/

	function uplib_getAllUploads($number, $howMany, $sortName, $direction, $expr, $uploadConsts) {
		global $recordCount;
		$Color = '/ocp/img/blue_4.gif';
		
		$recordCount = con_getValue("select count(*) from Upload");

		$sql = 'select UploadId as Id, Label as Labela, SourceFileName as SFN, UploadDT as Datum, Type from Upload';
		if (($sortName != 'Link') && (!is_null($sortName)))
			$sql .= " order by $sortName $direction";

		$records = con_getResults($sql . " limit " . ($howMany*$number) . ", ".$number, 1);
		for ($i=0; $i<count($records); $i++){
			$records[$i]->Link = uplib_izabran($records[$i]->Id, $expr);
			$records[$i]->Broj = $i+1;
		}

		if ($sortName == 'Link') 
			$records = uplib_sortByLink($records, $direction);	
		

		for ($i=0; $i<count($records);$i++) {
			$d = $records[$i];
?>
	<TR>
		<TD CLASS="ocp_tekst1" background="<?php echo $Color?>"  ALIGN="center">
			<?php echo $d["Broj"]?>
			<INPUT TYPE="HIDDEN" NAME="Id<?php echo $d["Broj"]?>" VALUE="<?php echo $d["Id"]?>">
		</TD>
		<TD CLASS="ocp_tekst1" background="<?php echo $Color?>"  ALIGN="center">
			<INPUT TYPE="TEXT" NAME="Labela<?php echo $d["Broj"]?>" VALUE="<?php echo $d["Labela"]?>" class="ocp_forma">
			<INPUT TYPE="HIDDEN" NAME="OldLabela<?php echo $d["Broj"]?>" VALUE="<?php echo $d["Labela"]?>">
		</TD>
		<TD CLASS="ocp_tekst1" background="<?php echo $Color?>" ALIGN="center"> 
			<?php echo $d["Type"]?>
		</TD>
		<TD CLASS="ocp_tekst1" background="<?php echo $Color?>" ALIGN="center"> 
			<img src="/ocp/img/details_light.gif" border="0" alt="<?php echo $d["SFN"]?>">
		</TD>
		<TD CLASS="ocp_tekst1" background="<?php echo $Color?>"  ALIGN="center">
			<?php echo $d["Datum"]?>
		</TD>
		<TD CLASS="ocp_tekst1" background="<?php echo $Color?>" ALIGN="center">
			<a href="javascript:down('<?php echo $d["Id"]?>')"><img src="/ocp/img/view.gif" border="0"></a>
		</TD>
		<TD CLASS="ocp_tekst1" background="<?php echo $Color?>" ALIGN="center">
			<?php echo $d["Link"]?>
		</TD>
		<TD CLASS="ocp_tekst1" background="<?php echo $Color?>"  ALIGN="center">
			<INPUT TYPE="checkbox" Name="Delete<?php echo $d["Broj"]?>" VALUE="1" >
		</TD>
	</TR>
<?php		
			$Color = ($Color == "/ocp/img/green_1.gif") ? "/ocp/img/blue_4.gif" : "/ocp/img/green_1.gif";
		}

		return count($records);
	}


/* ================================================================================
		izlaz je Da ako se upload idTemp dobija izvrsenjem sql upita expr
		Ne ako se ne dobija
===================================================================================*/
	function uplib_izabran($idTemp, $expr){
		$found = false;
		for ($i=0; $i<count($expr); $i++){
			if (intval(con_getValue($expr[$i] . $idTemp)) > 0) {
				$found=true; 
				break;
			}
		}
		return (($found) ? "Da" : "Ne");
	}

/* ================================================================================
	-generise niz expressions u kom se nalaze iskazi tipa
	'select count(*) from imeTipa where imeKolona='
	-imeTipa i imeKolone su svi tipovi i njihove kolone koje su tipa upload
===================================================================================*/
	function uplib_getSQLExpressions(){
		$tempArr = con_getResults("Select t.Ime, p.ImePolja from Polja p, TipoviObjekata t where p.TipTabela='Uploads' and p.TipId=t.Id", $GLOBALS["FETCH_ARRAY"]);
		$records = array();
		for ($i=0; $i<count($tempArr); $i++)
			$records[] = "select count(*) from ".$tempArr[$i][0]." where ".$tempArr[$i][1]."="; 
		return $records;
	}

/* ============================================================================
	-brise upload Id i sve njegove pojave u objektima, uz pomoc expression
==============================================================================*/
	function uplib_deleteAllInObjects($Id, $expr){
		for ($i=0; $i<count($expr); $i++){
			$p1 = strpos($expr[$i], 'from')+5;
			$p2 = strpos($expr[$i], ' where');
			$tabela = substr($expr[$i], $p1, $p2-$p1);
			$p3 = strpos($expr[$i], '=');
			$polje = substr($expr[$i], $p2+6, $p3-($p2+6));
			$where = substr($expr[$i], $p2);
			
			$iskaz = "update $tabela set $polje=NULL $where $Id";
			con_update($iskaz);
		}
	}

/* =====================================
	sortiranje niza slogovi u smeru smer
	link je kriterijum za soritranje
========================================*/
	function uplib_sortByLink($slogovi, $smer){
		if ($smer == "asc"){
			for ($i=0; $i<count($slogovi)-1; $i++){
				for ($j=$i; $j<count($slogovi); $j++){
					$di = $slogovi[$i];
					$dj = $slogovi[$j];
					if ($di["Link"] > $dj["Link"]){
						$temp = $dj; $slogovi[$j] = $di; $slogovi[$i] = $temp;
					}
				}
			}	
		} else {
			for ($i=0; $i<count($slogovi)-1; $i++){
				for ($j=$i; $j<count($slogovi); $j++){
					$di = $slogovi[$i];
					$dj = $slogovi[$j];
					if ($di["Link"] < $dj["Link"]){
						$temp = $dj; $slogovi[$j] = $di; $slogovi[$i] = $temp;
					}
				}
			}	
		}
		return $slogovi;
	}

	/*Vraca listu uploada za zadat Tip
	==================================*/
	function upload_getUploadLabels($value, $uType){
		$strSQL = "select UploadId, Label from Upload ";
		if (utils_valid($uType)) $strSQL .= " where Type=".$uType;
		$strSQL .= "order by Label asc";
		$nizUploada = con_getResults($strSQL);

		for ($i=0; $i < count($nizUploada); $i++){
			$result = $nizUploada[$i];
			if (utils_valid($value)){
				if ($result["Id"] == $value) $result["Selected"] = "1";
				else $result["Selected"] = "0";
			} else $result["Selected"] = "0";
			$result["Tip"] = "Upload";
			$nizUploada[$i] = $result;
		}

		return $nizUploada;
	}
?>
