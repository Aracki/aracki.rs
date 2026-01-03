<?php

require_once($_SERVER['DOCUMENT_ROOT']."/ocp/config/logs.php");

/*Fja koja vraca broj logova u bazi
========================================*/
	function visitlog_count(){
		return intval(con_getValue("select count(*) from VisitLog"));
	}

/*Fja koja vraca broj obradjenih logova
========================================*/
	function visitlog_countReports(){
		return intval(con_getValue("select count(*) from VisitReport"));
	}

/*Fja koja smesta podatke o poseti u visitlog
========================================*/
	function visitlog_save($idStranice){
		global $max_no_visit_logs;
		$cnt = visitlog_count();
		if ($cnt < $max_no_visit_logs){
			$sessionGuid = session_id();
			$executeSQL = "";

			$idVisitLog = con_getValue( "select Id from VisitLog where GuidSesije = '" . $sessionGuid . "'");
			if (!utils_valid($idVisitLog)){//nije upisan u VisitLog
				$ip = $_SERVER['REMOTE_ADDR'];
				$zemlja = "";
				if (utils_valid($ip)){
					$user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : "";
					if (visitlog_isBot($user_agent)){
						$zemlja = "Search engine";
					} else {
						$ipNo = con_getValue("select inet_aton('".$ip."')");
						$zemlja = con_getValue( "select Naziv from IpZemlja where IpOd <= " . $ipNo . " and IpDo >= " . $ipNo);
					}
				}

				$executeSQL = "insert into VisitLog(GuidSesije, PocetniDatum, KrajnjiDatum, Ip, Zemlja, Stranice)";
				$executeSQL .= " values ('".$sessionGuid."', NOW(), NOW(),";
				if (utils_valid($ip)) $executeSQL .= " '".$ip."',";
				else $executeSQL .= " NULL,";
				if (utils_valid($zemlja)) $executeSQL .= " '".$zemlja."',";
				else $executeSQL .= " NULL,";
				$executeSQL .= " '".$idStranice."')";
			} else { //vec je upisan u VisitLog
				$executeSQL = "update VisitLog set KrajnjiDatum = NOW(), Stranice = CONCAT(Stranice , ',".$idStranice."') where Id=".$idVisitLog;
			}
			con_update($executeSQL);
		}
	}

	function visitlog_isBot($user_agent){
		$lower_user_agent = strtolower($user_agent);

		$google			= (substr_count($lower_user_agent, 'googlebot') != 0);
		$msn			= (substr_count($lower_user_agent, 'msnbot') != 0);
		$lycos			= (substr_count($lower_user_agent, 'lycos') != 0);
		$altavista		= (substr_count($lower_user_agent, 'scooter') != 0 || substr_count($lower_user_agent, 'mercator'));
		$ask			= (substr_count($lower_user_agent, 'crawler.ask') != 0);
		$krstarica		= (substr_count($lower_user_agent, 'robot.krstarica') != 0);
		$yahoo			= (substr_count($lower_user_agent, 'slurp') != 0 || 
							substr_count($lower_user_agent, 'yahooseeker') != 0 || 
							substr_count($lower_user_agent, 'yahoo-mmcrawler') != 0) ;

		if($google || $msn || $lycos || $altavista || $ask || $krstarica || $yahoo){ 
			return true; 
		} 
		return false; 
	}
?>