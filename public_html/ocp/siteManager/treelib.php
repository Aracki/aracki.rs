<?php
	require_once("../include/connect.php");
	require_once("../include/session.php");
	require_once("../siteManager/lib/root.php");
	require_once("../siteManager/lib/verzija.php");
	require_once("../siteManager/lib/meni.php");
	require_once("../siteManager/lib/sekcija.php");
	require_once("../siteManager/lib/stranica.php");

	$action = utils_requestStr(getGVar("action"));

	switch($action){
		case "movePageIntoSection": movePageIntoSection(); break; 
		case "movePageIntoMenu": movePageIntoMenu(); break;
		case "movePageBeforePage": movePageBeforePage(); break;
		case "movePageAfterPage": movePageAfterPage(); break;
		case "moveSectionIntoSection": moveSectionIntoSection(); break;
		case "moveSectionIntoVersion": moveSectionIntoVersion(); break;
		case "moveVersionIntoMenu": moveVersionIntoMenu(); break;
		case "moveSectionBeforeSection": moveSectionBeforeSection(); break;
		case "moveSectionAfterSection": moveSectionAfterSection(); break;
		default:
			echo ("&action=error&msg=".rawurlencode("guru meditation #80000004"));
			break;
	}

	// stranica u sekciju
	function movePageIntoSection() {
		$lastModify = utils_requestInt(getGVar("lastModify"));
		$srcID = utils_requestInt(getGVar("srcID"));
		$destID = utils_requestInt(getGVar("destID"));

		$srcStra = stranica_get($srcID);
		$destSekc = sekcija_get($destID);

		// da li src i dest postoje
		if (!key_exists("Stra_Naziv", $srcStra)) {
			echo ("&action=error&msg=".rawurlencode(ocpLabels("Page")." ".ocpLabels("that what you want to move doesn't exist anymore")."."));
		} else {
			if (!key_exists("Sekc_Naziv", $destSekc)) {
				echo ("&action=error&msg=".rawurlencode(ocpLabels("Section")." ".ocpLabels("where you want to move doesn't exist anymore")."."));
			} else {
				// da li src i dest imaju ok LastModify
				if (is_numeric($srcStra["Stra_LastModify"]) && $srcStra["Stra_LastModify"] > $lastModify) {
					echo ("&action=error&msg=".rawurlencode(ocpLabels("Page")." ".$srcStra["Stra_Naziv"].ocpLabels("that you want to move is changed by another user")."."));
				} else {
					if (is_numeric($destSekc["Sekc_LastModify"]) && $destSekc["Sekc_LastModify"] > $lastModify) {
						echo ("&action=error&msg=".rawurlencode(ocpLabels("Section")." ".$destSekc["Sekc_Naziv"].ocpLabels("where you want to move is changed by another user")."."));
					} else {
						if (stranica_security(2, $srcID) && sekcija_security(3, $destID)) {
							
							stranica_changePosition($srcID, $destID);
							con_update("update Sekcija set Sekc_Stra_Id = null where Sekc_Stra_Id=".$srcID);

							$newLastModify=date_getMiliseconds();
							con_update("update Stranica set Stra_LastModify='".$newLastModify."' where Stra_Id=$srcID");
							con_update("update Sekcija set Sekc_LastModify='".$newLastModify."' where Sekc_Id=$destID");
							//decheckiranje pocetne stranice stare sekcije
							checkSekcijaFirstStranica($srcStra["Stra_Sekc_Id"]);
							
							stranica_updatePath($srcID);

							echo ("&msg=".rawurlencode(ocpLabels("Operation is successfully executed"))."&action=movePageIntoSection&srcID=".$srcID."&destID=".$destID."&newLastModify=$newLastModify");
						} else {
							echo ("&action=error&msg=".rawurlencode(ocpLabels("You don't have sufficient privileges for this operation")));
						}
					}
				}
			}
		}
	}

	//stranica u meni odredjene verzije
	function movePageIntoMenu() {
		$lastModify = utils_requestInt(getGVar("lastModify"));
		$srcID = utils_requestInt(getGVar("srcID"));
		$destID = utils_requestInt(getGVar("destID"));
		$userGroupId = getSVar("ocpUserGroup");

		if (verzija_security(4, $destID)){
			$srcStra = stranica_get($srcID);

			if (!key_exists("Stra_Naziv", $srcStra)) {
				echo ("&action=error&msg=".rawurlencode(ocpLabels("Page")." ".ocpLabels("that you want to move doesn't exist anymore")."."));
			} else {
				// da li src ima ok LastModify
				if (is_numeric($srcStra["Stra_LastModify"]) && $srcStra["Stra_LastModify"] > $lastModify) {
					echo ("&action=error&msg=".rawurlencode(ocpLabels("Page")." ".$srcStra["Stra_Naziv"].ocpLabels("that you want to move is changed by another user")."."));
				} else {
					meni_addPage($srcID, $destID);
					$newLastModify=date_getMiliseconds();
					con_update("UPDATE Stranica SET Stra_LastModify = '".$newLastModify."' WHERE Stra_Id = ".$srcID);
					echo ("&msg=".rawurlencode(ocpLabels("Operation is successfully executed"))."&action=movePageIntoMenu&srcID=".$srcID."&destID=".$destID."&newLastModify=$newLastModify");
				}
			}
		} else {
			echo ("&action=error&msg=".rawurlencode(ocpLabels("You don't have sufficient privileges for this operation")));
		}
	}

	// sekcija postaje podsekcija
	function moveSectionIntoSection() {
		$lastModify = utils_requestInt(getGVar("lastModify"));
		$srcID = utils_requestInt(getGVar("srcID"));
		$destID = utils_requestInt(getGVar("destID"));

		$srcSekc = sekcija_get($srcID);
		$destSekc = sekcija_get($destID);

		if (!key_exists("Sekc_Naziv",$srcSekc)) {
			echo ("&action=error&msg=".rawurlencode(ocpLabels("Section")." ".ocpLabels("that you want to move doesn't exist anymore")."."));
		} else {
			if (!key_exists("Sekc_Naziv",$destSekc)){
				echo ("&action=error&msg=".rawurlencode(ocpLabels("Section")." ".ocpLabels("where you want to move doesn't exist anymore")."."));
			} else {
				// da li src i dest imaju ok LastModify
				if (is_numeric($srcSekc["Sekc_LastModify"]) && $srcSekc["Sekc_LastModify"] > $lastModify) {
					echo ("&action=error&msg=".rawurlencode(ocpLabels("Section")." ".$srcSekc["Sekc_Naziv"].ocpLabels("that you want to move is changed by another user")."."));
				} else {
					if (is_numeric($destSekc["Sekc_LastModify"]) && $destSekc["Sekc_LastModify"] > $lastModify) {
						echo ("&action=error&msg=".rawurlencode(ocpLabels("Section")." ".$destSekc["Sekc_Naziv"].ocpLabels("where you want to move is changed by another user")."."));
					} else {
						if (sekcija_security(2, $srcID) && sekcija_security(3, $destID)) {
							sekcija_changeSekcija2Subsekcija($srcID, $destID);
							$newLastModify = date_getMiliseconds();
							con_update("update Sekcija set Sekc_LastModify='".$newLastModify."' where Sekc_Id=".$srcID);
							con_update("update Sekcija set Sekc_LastModify='".$newLastModify."' where Sekc_Id=".$destID);
							//decheckiranje pocetne sekcije stare verzije
							checkVerzijaFirstSekcija($srcSekc["Sekc_Verz_Id"]);
							
							sekcija_updatePath($srcID, null);

							echo ("&msg=".rawurlencode(ocpLabels("Operation is successfully executed"))."&action=moveSectionIntoSection&srcID=".$srcID."&destID=".$destID."&newLastModify=$newLastModify");
						} else {
							echo ("&action=error&msg=".rawurlencode(ocpLabels("You don't have sufficient privileges for this operation")));
						}
					}
				}
			}
		}
	}

	//sekcija se pomera pod verziju	
	function moveSectionIntoVersion() {
		$lastModify = utils_requestInt(getGVar("lastModify"));
		$srcID = utils_requestInt(getGVar("srcID"));
		$destID = utils_requestInt(getGVar("destID"));

		$srcSekc = sekcija_get($srcID);
		$destVerz = verzija_get($destID);

		if (!key_exists("Sekc_Naziv", $srcSekc)) {
			echo ("&action=error&msg=".rawurlencode(ocpLabels("Section")." ".ocpLabels("where you want to move doesn't exist anymore")."."));
		} else {
			if (!key_exists("Verz_Naziv", $destVerz)) {
				echo ("&action=error&msg=".rawurlencode(ocpLabels("Version")." ".ocpLabels("where you want to move doesn't exist anymore")."."));
			} else {
				// da li src i dest imaju ok LastModify
				if (is_numeric($srcSekc["Sekc_LastModify"]) && $srcSekc["Sekc_LastModify"] > $lastModify) {
					echo ("&action=error&msg=".rawurlencode(ocpLabels("Section")." ".$srcSekc["Sekc_Naziv"].ocpLabels("that you want to move is changed by another user")."."));
				} else {
					if (is_numeric($destVerz["Verz_LastModify"]) && $destVerz["Verz_LastModify"] > $lastModify) {
						echo ("&action=error&msg=".rawurlencode(ocpLabels("Version")." ".$destVerz["Verz_Naziv"].ocpLabels("where you want to move is changed by another user")."."));
					} else {
						if (sekcija_security(2, $srcID) && verzija_security(3, $destID)) {
							sekcija_changePosition($srcID, $destID);
							$newLastModify = date_getMiliseconds();
							con_update("update Sekcija set Sekc_LastModify='".$newLastModify."' where Sekc_Id=$srcID");
							con_update("update Verzija set Verz_LastModify='".$newLastModify."' where Verz_Id=$destID");
							
							sekcija_updatePath($srcID, null);

							echo ("&msg=".rawurlencode(ocpLabels("Operation is successfully executed"))."&action=moveSectionIntoVersion&srcID=".$srcID."&destID=".$destID."&newLastModify=$newLastModify");
						} else {
							echo ("&action=error&msg=".rawurlencode(ocpLabels("You don't have sufficient privileges for this operation")));
						}
					}
				}
			}
		}
	}

	// premesta verziju pod meni
	function moveVersionIntoMenu() {
		$lastModify = utils_requestInt(getGVar("lastModify"));
		$srcID = utils_requestInt(getGVar("srcID"));
		$destID = utils_requestInt(getGVar("destID"));
		$userGroupId = getSVar("ocpUserGroup");

		if (verzija_security(4, $destID)) {
			$srcVerz = verzija_get($srcID);

			if (!key_exists("Verz_Naziv",$srcVerz)) {
				echo ("&action=error&msg=".rawurlencode(ocpLabels("Version")." ".ocpLabels("that you want to move doesn't exist anymore")."."));
			} else {
				if (is_numeric($srcVerz["Verz_LastModify"]) && $srcVerz["Verz_LastModify"] > $lastModify) {
					echo ("&action=error&msg=".rawurlencode(ocpLabels("Version")." ".$srcVerz["Verz_Naziv"].ocpLabels("that you want to move is changed by another user")."."));
				} else {
					meni_addVersion($srcID, $destID);
					$newLastModify = date_getMiliseconds();
					con_update("update Verzija set Verz_LastModify='".$newLastModify."' where Verz_Id=$srcID");
				
					echo ("&msg=".rawurlencode(ocpLabels("Operation is successfully executed"))."&action=moveVersionIntoMenu&srcID=".$srcID."&destID=".$destID."&newLastModify=$newLastModify");
				}
			}
		} else {
			echo ("&action=error&msg=".rawurlencode(ocpLabels("You don't have sufficient privileges for this operation")));
		}
	}

	function reorderSection($parentId, $verzId){
		$sql = "select  s1.Sekc_Id from Sekcija s1 where s1.Sekc_Verz_Id= $verzId";
		$sql .= (is_null($parentId)) ? " and s1.Sekc_ParentId is null" : " and s1.Sekc_ParentId = $parentId";
		$sql .= " order by s1.Sekc_RedPrikaza asc";

		$records = con_getResults($sql);
		for ($i=0; $i<count($records); $i++)
			con_update("update Sekcija set Sekc_RedPrikaza = ".$i." where Sekc_Id=".$records[$i]["Sekc_Id"]);
	}

	// pomera sekciju posle odredjene sekcije
	function moveSectionAfterSection() {
		$lastModify = utils_requestInt(getGVar("lastModify"));
		$srcID = utils_requestInt(getGVar("srcID"));
		$destID = utils_requestInt(getGVar("destID"));

		$srcSekc = sekcija_get($srcID);
		$destSekc = sekcija_get($destID);

		reorderSection($srcSekc["Sekc_ParentId"], $srcSekc["Sekc_Verz_Id"]);
		reorderSection($destSekc["Sekc_ParentId"], $destSekc["Sekc_Verz_Id"]);

		if (!key_exists("Sekc_Naziv",$srcSekc)) {
			echo ("&action=error&msg=".rawurlencode(ocpLabels("Section")." ".ocpLabels("that you want to move doesn't exist anymore")."."));
		} else {
			if (!key_exists("Sekc_Naziv",$destSekc)) {
				echo ("&action=error&msg=".rawurlencode(ocpLabels("Section")." ".ocpLabels("that you want to move doesn't exist anymore")."."));
			} else {
				if (is_numeric($srcSekc["Sekc_LastModify"]) && $srcSekc["Sekc_LastModify"] > $lastModify) {
					echo ("&action=error&msg=".rawurlencode(ocpLabels("Section")." ".$srcSekc["Sekc_Naziv"].ocpLabels("that you want to move is changed by another user")."."));
				} else {
					if (is_numeric($destSekc["Sekc_LastModify"]) && $destSekc["Sekc_LastModify"] > $lastModify) {
						echo ("&action=error&msg=".rawurlencode(ocpLabels("Section")." ".$destSekc["Sekc_Naziv"].ocpLabels("that you want to move is changed by another user")."."));
					} else {
						if (securityMoveSection($srcID, $destID)) {
							// move by +1 at destination
							$sql = "UPDATE Sekcija SET Sekc_RedPrikaza = Sekc_RedPrikaza + 1 ";
							$sql .= "WHERE Sekc_RedPrikaza > ".$destSekc["Sekc_RedPrikaza"];

							if ($destSekc["Sekc_ParentId"] > 0) {
								$sql .= " AND Sekc_ParentId = ".$destSekc["Sekc_ParentId"];
							} else {
								$sql .= " AND Sekc_ParentId IS NULL";
							}

							$sql .= " AND Sekc_Verz_Id = ".$destSekc["Sekc_Verz_Id"];

							con_update($sql);

							// move source
							$sql = "UPDATE Sekcija SET Sekc_RedPrikaza = ".$destSekc["Sekc_RedPrikaza"]." + 1";
							if ($destSekc["Sekc_ParentId"]>0) {
								$sql .= ", Sekc_ParentId = ".$destSekc["Sekc_ParentId"];
							} else {
								$sql .= ", Sekc_ParentId = NULL";
							}
							$sql .= ", Sekc_Verz_Id = ".$destSekc["Sekc_Verz_Id"];
							$sql .= " WHERE Sekc_Id = ".$srcSekc["Sekc_Id"];

							con_update($sql);

							// move by -1 at source
							$sql = "UPDATE Sekcija SET Sekc_RedPrikaza = Sekc_RedPrikaza - 1 ";
							$sql .= "WHERE Sekc_RedPrikaza > ".$srcSekc["Sekc_RedPrikaza"];
							if ($srcSekc["Sekc_ParentId"]>0) {
								$sql .= " AND Sekc_ParentId = ".$srcSekc["Sekc_ParentId"];
							} else {
								$sql .= " AND Sekc_ParentId IS NULL";
							}
							$sql .= " AND Sekc_Verz_Id=" . $srcSekc["Sekc_Verz_Id"];

							con_update($sql);

							sekcija_changeParentPodsekcija($srcSekc["Sekc_Id"], $destSekc["Sekc_Verz_Id"]);

							$newLastModify = date_getMiliseconds();
							con_update("UPDATE Sekcija SET Sekc_LastModify='".$newLastModify."' WHERE Sekc_Id = ".$srcID);
							con_update("UPDATE Sekcija SET Sekc_LastModify='".$newLastModify."' WHERE Sekc_Id = ".$destID);
							
							utils_updateSiteMenu();
								
							sekcija_updatePath($srcID, null);

							echo("&msg=".rawurlencode(ocpLabels("Operation is successfully executed"))."&action=moveSectionAfterSection&srcID=".$srcID."&destID=".$destID."&newLastModify=$newLastModify");
						} else {
							echo ("&action=error&msg=".rawurlencode(ocpLabels("You don't have sufficient privileges for this operation")));
						}
					}
				}
			}
		}
	}

	// pomeranje sekcije pre odredjene sekcije
	function moveSectionBeforeSection() {
		$lastModify = utils_requestInt(getGVar("lastModify"));
		$srcID = utils_requestInt(getGVar("srcID"));
		$destID = utils_requestInt(getGVar("destID"));

		$srcSekc = sekcija_get($srcID);
		$destSekc = sekcija_get($destID);
	
		reorderSection($srcSekc["Sekc_ParentId"], $srcSekc["Sekc_Verz_Id"]);
		reorderSection($destSekc["Sekc_ParentId"], $destSekc["Sekc_Verz_Id"]);
		
		if (!key_exists("Sekc_Naziv",$srcSekc)) {
			echo ("&action=error&msg=".rawurlencode(ocpLabels("Section")." ".ocpLabels("that you want to move doesn't exist anymore")."."));
		} else {
			if (!key_exists("Sekc_Naziv",$destSekc)) {
				echo ("&action=error&msg=".rawurlencode(ocpLabels("Section")." ".ocpLabels("that you want to move doesn't exist anymore")."."));
			} else {
				if (is_numeric($srcSekc["Sekc_LastModify"]) && ($srcSekc["Sekc_LastModify"] > $lastModify)) {
					echo ("&action=error&msg=".rawurlencode(ocpLabels("Section")." ".$srcSekc["Sekc_Naziv"].ocpLabels("that you want to move is changed by another user")."."));
				} else {
					if (is_numeric($destSekc["Sekc_LastModify"]) && ($destSekc["Sekc_LastModify"] > $lastModify)) {
						echo ("&action=error&msg=".rawurlencode(ocpLabels("Section")." ".$destSekc["Sekc_Naziv"].ocpLabels("that you want to move is changed by another user")."."));
					} else {
						if (securityMoveSection($srcID, $destID)) {
							// move by +1 at destination
							$sql = "UPDATE Sekcija SET Sekc_RedPrikaza = Sekc_RedPrikaza + 1 ";
							$sql .= "WHERE Sekc_RedPrikaza >= ".$destSekc["Sekc_RedPrikaza"];
							if ($destSekc["Sekc_ParentId"]>0) {
								$sql .= " AND Sekc_ParentId = ".$destSekc["Sekc_ParentId"];
							} else {
								$sql .= " AND Sekc_ParentId IS NULL";
							}
							$sql .= " AND Sekc_Verz_Id = ".$destSekc["Sekc_Verz_Id"];
							// echo ($sql."<br>");
							con_update($sql);

							// move source
							$sql = "UPDATE Sekcija SET Sekc_RedPrikaza = ".$destSekc["Sekc_RedPrikaza"];
							if ($destSekc["Sekc_ParentId"]>0) {
								$sql .= ", Sekc_ParentId = ".$destSekc["Sekc_ParentId"];
							} else {
								$sql .= ", Sekc_ParentId = NULL";
							}
							$sql .= ", Sekc_Verz_Id = ".$destSekc["Sekc_Verz_Id"];
							$sql .= " WHERE Sekc_Id = ".$srcSekc["Sekc_Id"];
							// echo ($sql."<br>");
							con_update($sql);

							// move by -1 at source
							$sql = "UPDATE Sekcija SET Sekc_RedPrikaza = Sekc_RedPrikaza - 1 ";
							$sql .= "WHERE Sekc_RedPrikaza > ".$srcSekc["Sekc_RedPrikaza"];

							if ($srcSekc["Sekc_ParentId"]>0) {
								$sql .= " AND Sekc_ParentId = ".$srcSekc["Sekc_ParentId"];
							} else {
								$sql .= " AND Sekc_ParentId IS NULL";
							}

							$sql .= " AND Sekc_Verz_Id = ".$srcSekc["Sekc_Verz_Id"];
							// echo ($sql."<br>");
							con_update($sql);

							sekcija_changeParentPodsekcija($srcSekc["Sekc_Id"], $destSekc["Sekc_Verz_Id"]);

							$newLastModify = date_getMiliseconds();
							con_update("UPDATE Sekcija SET Sekc_LastModify = '".$newLastModify."' WHERE Sekc_Id = ".$srcID);
							con_update("UPDATE Sekcija SET Sekc_LastModify = '".$newLastModify."' WHERE Sekc_Id = ".$destID);

							utils_updateSiteMenu();
							
							sekcija_updatePath($srcID, null);

							echo ("&msg=".rawurlencode(ocpLabels("Operation is successfully executed"))."&action=moveSectionBeforeSection&srcID=".$srcID."&destID=".$destID."&newLastModify=$newLastModify");
						} else {
							echo ("&action=error&msg=".rawurlencode(ocpLabels("You don't have sufficient privileges for this operation")));
						}
					}
				}
			}
		}
	}

	function reorderPage($straid){
		$sql = "select s1.Stra_Id from Stranica s1, Stranica s2 where s1.Stra_Sekc_Id= s2.Stra_Sekc_Id and s2.Stra_Id=$straid";
		$sql .= " order by s1.Stra_RedPrikaza asc";

		$records = con_getResults($sql);
		for ($i=0; $i<count($records); $i++)
			con_update("update Stranica set Stra_RedPrikaza = ".$i." where Stra_Id=".$records[$i]["Stra_Id"]);
	}

	// pomeranje stranice posle odredjene stranice
	function movePageAfterPage() {
		$lastModify = utils_requestInt(getGVar("lastModify"));
		$srcID = utils_requestInt(getGVar("srcID"));
		$destID = utils_requestInt(getGVar("destID"));

		reorderPage($srcID);
		reorderPage($destID);
	
		$srcStra = stranica_get($srcID);
		$destStra = stranica_get($destID);
		
		if (!key_exists("Stra_Naziv",$srcStra)) {
			echo ("&action=error&msg=".rawurlencode(ocpLabels("Page")." ".ocpLabels("that you want to move doesn't exist anymore")."."));
		} else {
			if (!key_exists("Stra_Naziv",$destStra)) {
				echo ("&action=error&msg=".rawurlencode(ocpLabels("Page")." ".ocpLabels("that you want to move doesn't exist anymore")."."));
			} else {
				if (is_numeric($srcStra["Stra_LastModify"]) && $srcStra["Stra_LastModify"] > $lastModify) {
					echo ("&action=error&msg=".rawurlencode(ocpLabels("Page")." ".$srcStra["Stra_Naziv"].ocpLabels("that you want to move is changed by another user")."."));
				} else {
					if (is_numeric($destStra["Stra_LastModify"]) && $destStra["Stra_LastModify"] > $lastModify) {
						echo ("&action=error&msg=".rawurlencode(ocpLabels("Page")." ".$destStra["Stra_Naziv"].ocpLabels("that you want to move is changed by another user")."."));
					} else {
						if (securityMovePage($srcID, $destID)) {
							// move by +1 at destination
							$sql = "UPDATE Stranica SET Stra_RedPrikaza = Stra_RedPrikaza + 1 ";
							$sql .= "WHERE Stra_RedPrikaza > ".$destStra["Stra_RedPrikaza"];
							$sql .= " AND Stra_Sekc_Id = ".$destStra["Stra_Sekc_Id"];

							con_update($sql);

							// move source
							$sql = "UPDATE Stranica SET Stra_RedPrikaza = ".$destStra["Stra_RedPrikaza"]." + 1";
							$sql .= ", Stra_Sekc_Id = ".$destStra["Stra_Sekc_Id"];
							$sql .= " WHERE Stra_Id = ".$srcStra["Stra_Id"];

							con_update($sql);

							// move by -1 at source
							$sql = "UPDATE Stranica SET Stra_RedPrikaza = Stra_RedPrikaza - 1 ";
							$sql .= "WHERE Stra_RedPrikaza > ".$srcStra["Stra_RedPrikaza"];
							$sql .= " AND Stra_Sekc_Id = ".$srcStra["Stra_Sekc_Id"];

							con_update($sql);

							$newLastModify = date_getMiliseconds();
							con_update("UPDATE Stranica SET Stra_LastModify = '".$newLastModify."' WHERE Stra_Id = ".$srcID);
							con_update("UPDATE Stranica SET Stra_LastModify = '".$newLastModify."' WHERE Stra_Id = ".$destID);
							
							utils_updateSiteMenu();

							stranica_updatePath($srcID);

							echo ("&msg=".rawurlencode(ocpLabels("Operation is successfully executed"))."&action=movePageAfterPage&srcID=".$srcID."&destID=".$destID."&newLastModify=$newLastModify");
						} else {
							echo ("&action=error&msg=".rawurlencode(ocpLabels("You don't have sufficient privileges for this operation")));
						}
					}
				}
			}
		}
	}

	// pomeranje stranice posle stranice
	function movePageBeforePage() {
		$lastModify = utils_requestInt(getGVar("lastModify"));
		$srcID = utils_requestInt(getGVar("srcID"));
		$destID = utils_requestInt(getGVar("destID"));

		$srcStra = stranica_get($srcID);
		$destStra = stranica_get($destID);

		reorderPage($srcID);
		reorderPage($destID);

		//utils_dump($srcID." ".$srcStra["Stra_Naziv"]." ".$srcStra["Stra_LastModify"]);
		//utils_dump($destID." ".$destStra["Stra_Naziv"]." ".$destStra["Stra_LastModify"]);
		//utils_dump($lastModify);
		
		if (!key_exists("Stra_Naziv", $srcStra)) {
			echo ("&action=error&msg=".rawurlencode(ocpLabels("Page")." ".ocpLabels("that you want to move doesn't exist anymore")."."));
		} else {
			if (!key_exists("Stra_Naziv",$destStra)) {
				echo ("&action=error&msg=".rawurlencode(ocpLabels("Page")." ".ocpLabels("that you want to move doesn't exist anymore")."."));
			} else {
				if (is_numeric($srcStra["Stra_LastModify"]) && $srcStra["Stra_LastModify"] > $lastModify) {
					echo ("&action=error&msg=".rawurlencode(ocpLabels("Page")." ".$srcStra["Stra_Naziv"].ocpLabels("that you want to move is changed by another user")."."));
				} else {
					if (is_numeric($destStra["Stra_LastModify"]) && $destStra["Stra_LastModify"] > $lastModify) {
						echo ("&action=error&msg=".rawurlencode(ocpLabels("Page")." ".$destStra["Stra_Naziv"].ocpLabels("that you want to move is changed by another user")."."));
					} else {
						if (securityMovePage($srcID, $destID)) {
							// move by +1 at destination
							$sql = "UPDATE Stranica SET Stra_RedPrikaza = Stra_RedPrikaza + 1 ";
							$sql .= "WHERE Stra_RedPrikaza >= ".$destStra["Stra_RedPrikaza"];
							$sql .= " AND Stra_Sekc_Id=".$destStra["Stra_Sekc_Id"];

							con_update($sql);

							// move source
							$sql = "UPDATE Stranica SET Stra_RedPrikaza = ".$destStra["Stra_RedPrikaza"];
							$sql .= ", Stra_Sekc_Id = ".$destStra["Stra_Sekc_Id"];
							$sql .= " WHERE Stra_Id = ".$srcStra["Stra_Id"];

							con_update($sql);

							// move by -1 at source
							$sql = "UPDATE Stranica SET Stra_RedPrikaza = Stra_RedPrikaza - 1";
							$sql .= " WHERE Stra_RedPrikaza > ".$srcStra["Stra_RedPrikaza"];
							$sql .= " AND Stra_Sekc_Id = ".$srcStra["Stra_Sekc_Id"];

							con_update($sql);
							
							$newLastModify = date_getMiliseconds();
							con_update("UPDATE Stranica SET Stra_LastModify = '".$newLastModify."' WHERE Stra_Id = ".$srcID);
							con_update("UPDATE Stranica SET Stra_LastModify = '".$newLastModify."' WHERE Stra_Id = ".$destID);

							utils_updateSiteMenu();

							stranica_updatePath($srcID);

							echo ("&msg=".rawurlencode(ocpLabels("Operation is successfully executed"))."&action=movePageBeforePage&srcID=".$srcID."&destID=".$destID."&newLastModify=$newLastModify");
						} else {
							echo ("&action=error&msg=".rawurlencode(ocpLabels("You don't have sufficient privileges for this operation")));
						}
					}
				}
			}
		}
	}

	function securityMoveSection($srcID, $destID) {
		$sec = false;
		
		if (sekcija_security(2, $srcID)) {
			$destSekc = sekcija_get($destID);
			if (is_numeric($destSekc["Sekc_ParentId"])) {
				if (sekcija_security(3, $destSekc["Sekc_ParentId"])) {
					$sec = true;
				}
			} else {
				if (verzija_security(3, $destSekc["Sekc_Verz_Id"])) {
					$sec = true;
				}
			}
		}
		
		return $sec;
	}

	function securityMovePage($srcID, $destID) {
		$sec = false;
		
		if (stranica_security(2, $srcID)) {
			$destStra = stranica_get($destID);
			if (sekcija_security(3, $destStra["Stra_Sekc_Id"])) {
				$sec = true;
			}
		}
		
		return $sec;
	}

	function checkSekcijaFirstStranica($Sekc_Id) {
		//izvuci roditelj sekciju za stranicu koja je pocetna sekciji Sekc_Id
		$sql = "select Stra_Sekc_Id from Stranica ,Sekcija";
		$sql .= " where Stra_Id = Sekc_Stra_Id and Sekc_Id=" . $Sekc_Id;
		$sekcId = con_getValue($sql);

		if (is_integer($sekcId) && ($sekcId != $Sekc_Id))
			con_update("update Sekcija set Sekc_Stra_Id = NULL where Sekc_Id=".$Sekc_Id);
	}

	function checkVerzijaFirstSekcija($Verz_Id) {
		$sql = "select Sekc_ParentId from Sekcija, Verzija";
		$sql .= " where Sekc_Id = Verz_Sekc_Id and Verz_Id=" . $Verz_Id;
		$sekcId = con_getValue($sql);
			
		if (utils_valid($sekcId))
			con_update("update Verzija set Verz_Sekc_Id = NULL where Verz_Id=".$Verz_Id);
	}

/* Testiranja
	http://www.ocp-prevod.co.yu/ocp/siteManager/treelib.php?action=movePageIntoSection&srcID=3&destID=4&lastModify=9999999999

	http://www.ocp-prevod.co.yu/ocp/siteManager/treelib.php?action=movePageBeforePage&srcID=4&destID=3&lastModify=9999999999

	http://www.ocp-prevod.co.yu/ocp/siteManager/treelib.php?action=moveSectionBeforeSection&srcID=13&destID=12&lastModify=9999999999

	http://www.ocp-prevod.co.yu/ocp/siteManager/treelib.php?action=moveSectionIntoVersion&srcID=7&destID=2&lastModify=9999999999

	http://www.ocp-prevod.co.yu/ocp/siteManager/treelib.php?action=movePageIntoMenu&srcID=4&destID=2&lastModify=9999999999
*/
?>