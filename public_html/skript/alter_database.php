<?php 
	require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/include/connect.php");	
	require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/include/session.php");		
	
	con_update("CREATE TABLE IF NOT EXISTS Izvestaj ( Id int(11) unsigned NOT NULL auto_increment,  Ime varchar(255) NOT NULL,  Grupa varchar(255) NOT NULL,  Upit text NOT NULL,  ParametarXml text NOT NULL,  DetaljniIzvestaj int(11) unsigned default '0',   Aktivan tinyint(1) unsigned NOT NULL default '1',  PRIMARY KEY  (Id)) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

	con_update("CREATE TABLE IF NOT EXISTS SecurityIzvestaj (IzSec_Id int(11) unsigned NOT NULL auto_increment, IzSec_UGrp_Id int(11) unsigned NOT NULL, IzSec_Izve_Id int(11) unsigned NOT NULL, IzSec_Visible tinyint(1) unsigned NOT NULL default '0',  PRIMARY KEY  (IzSec_Id)) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

	con_update("DROP TABLE IF EXISTS VisitLog, VisitReports");

	$res = con_getResult("SHOW COLUMNS FROM Root WHERE Field='Root_LastExport'");
	if (isset($res["Field"]) && utils_valid($res["Field"]))
		con_update("ALTER TABLE Root DROP COLUMN Root_LastExport");

	$res = con_getResult("SHOW COLUMNS FROM Root WHERE Field='Root_VisitLogPeriod'");
	if (isset($res["Field"]) && utils_valid($res["Field"]))
		con_update("ALTER TABLE Root DROP COLUMN Root_VisitLogPeriod");

	$res = con_getResult("SHOW COLUMNS FROM Root WHERE Field='Root_Rewrite'");
	if (!isset($res["Field"]) || !utils_valid($res["Field"]))
		con_update("ALTER TABLE Root ADD COLUMN Root_Rewrite tinyint(1) NOT NULL default '0' AFTER Root_MaxDubina");

	$res = con_getResult("SHOW COLUMNS FROM Stranica WHERE Field='Stra_LinkName'");
	if (!isset($res["Field"]) || !utils_valid($res["Field"]))
		con_update("ALTER TABLE Stranica ADD COLUMN Stra_LinkName varchar(255) default NULL AFTER Stra_ExtraParams");

	$res = con_getResult("SHOW COLUMNS FROM Stranica WHERE Field='Stra_Link'");
	if (!isset($res["Field"]) || !utils_valid($res["Field"]))
		con_update("ALTER TABLE Stranica ADD COLUMN Stra_Link text NULL AFTER Stra_LinkName");

	$res = con_getResult("SHOW COLUMNS FROM Sekcija WHERE Field='Sekc_LinkName'");
	if (!isset($res["Field"]) || !utils_valid($res["Field"]))
		con_update("ALTER TABLE Sekcija ADD COLUMN Sekc_LinkName varchar(255) default NULL AFTER Sekc_ExtraParams");

	$res = con_getResult("SHOW COLUMNS FROM Root WHERE Field='Root_HtmlDescription'");
	if (!isset($res["Field"]) || !utils_valid($res["Field"]))
		con_update("ALTER TABLE Root ADD COLUMN Root_HtmlDescription varchar(255) default NULL AFTER Root_HtmlKeywords");

	$res = con_getResult("SHOW COLUMNS FROM Verzija WHERE Field='Verz_HtmlDescription'");
	if (!isset($res["Field"]) || !utils_valid($res["Field"]))
		con_update("ALTER TABLE Verzija ADD COLUMN Verz_HtmlDescription varchar(255) default NULL AFTER Verz_HtmlKeywords");

	$res = con_getResult("SHOW COLUMNS FROM Sekcija WHERE Field='Sekc_HtmlDescription'");
	if (!isset($res["Field"]) || !utils_valid($res["Field"]))
		con_update("ALTER TABLE Sekcija ADD COLUMN Sekc_HtmlDescription varchar(255) default NULL AFTER Sekc_HtmlKeywords");

	$res = con_getResult("SHOW COLUMNS FROM Stranica WHERE Field='Stra_HtmlDescription'");
	if (!isset($res["Field"]) || !utils_valid($res["Field"]))
		con_update("ALTER TABLE Stranica ADD COLUMN Stra_HtmlDescription varchar(255) default NULL AFTER Stra_HtmlKeywords");

	$res = con_getResult("SHOW COLUMNS FROM Ocp WHERE Field='LabeleXml'");
	if (!isset($res["Field"]) || !utils_valid($res["Field"]))
		con_update("ALTER TABLE Ocp ADD COLUMN LabeleXml longtext default NULL AFTER InputXml");
	
	utils_dump("Izmene izvrsene.");
?>