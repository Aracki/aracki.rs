//SQL i ODBC reserved_keywords
var reserved_keywords = new Array();
reserved_keywords["A"] = Array("ADD", "ALL", "ALTER", "ANALYZE", "AND", "ANY", "AS", "ASC", "AUTHORIZATION", "ABSOLUTE", "ACTION", "ADA", "ALLOCATE", "ARE", "ASSERTION", "AT", "AVG", "AUTO_INCREMENT");
reserved_keywords["B"] = Array("BACKUP", "BEGIN", "BETWEEN", "BULK", "BY", "BREAK", "BROWSE", "BIT", "BIT_LENGTH", "BOTH", "BDB", "BERKELEYDB", "BIGINT", "BINARY", "BLOB");
reserved_keywords["C"] = Array("CASCADE", "CASCADED", "CASE", "CAST", "CATALOG", "CHAR", "CHAR_LENGTH", "CHARACTER", "CHARACTER_LENGTH", "CHECK", "CHECKPOINT", "CLOSE", "CLUSTERED", "COALESCE", "COLLATE", "COLLATION", "COMMIT", "COLUMN", "COMPUTE", "CONSTRAINT", "CONTAINS", "CONTAINSTABLE", "CROSS", "CONTINUE", "CURRENT", "CREATE", "CONVERT", "CURRENT_TIME", "CURRENT_DATE", "CURRENT_TIMESTAMP", "CURRENT_USER", "CURSOR", "CONNECT", "CONNECTION", "CONSTRAINTS", "CORRESPONDING", "COUNT", "CHANGE COLUMNS", "CURRENT_DATE", "CURRENT_TIME", "CURRENT_TIMESTAMP");
reserved_keywords["D"] = Array("DATABASE", "DBCC", "DEALLOCATE", "DECLARE", "DEFAULT", "DELETE", "DENY", "DESC", "DISK", "DISTINCT", "DISTRIBUTED", "DOUBLE", "DROP", "DUMMY", "DUMP", "DATE", "DAY", "DEC", "DECIMAL", "DEFERRABLE", "DESCRIBE", "DISCONNECT", "DEFERRED", "DESCRIPTOR", "DIAGNOSTICS", "DOMAIN", "DOUBLE", "DATABASES", "DAY_HOUR", "DAY_MINUTE", "DAY_SECOND", "DELAYED", "DISTINCTROW");
reserved_keywords["E"] = Array("EXCEPT", "EXEC", "EXECUTE", "EXISTS", "EXIT", "ELSE", "END", "ESCAPE",  "ERRLVL", "EXTERNAL", "EXTRACT", "EXCEPTION", "END-EXEC", "ENCLOSED", "ESCAPED", "EXPLAIN");
reserved_keywords["F"] = Array("FETCH", "FILE", "FILLFACTOR", "FOR", "FOREIGN", "FREETEXT", "FREETEXTTABLE", "FROM", "FULL", "FUNCTION", "FALSE", "FIRST", "FLOAT", "FORTRAN", "FOUND", "FIELDS", "FULLTEXT");
reserved_keywords["G"] = Array("GOTO",  "GRANT", "GROUP", "GET", "GLOBAL", "GO");
reserved_keywords["H"] = Array("HAVING", "HOLDLOCK", "HOUR", "HIGH_PRIORITY", "HOUR_MINUTE", "HOUR_SECOND");
reserved_keywords["I"] = Array("ID", "IDENTITY", "IDENTITY_INSERT", "IDENTITYCOL", "IF", "IMMEDIATE", "IN", "INCLUDE", "INDEX", "INDICATOR", "INITIALLY", "INNER", "INPUT", "INSERT", "INTERSECT", "INT", "INTEGER", "INTO", "IS", "INSENSITIVE", "INTERVAL", "ISOLATION", "IGNORE", "INFILE", "INNODB");
reserved_keywords["J"] = Array("JOIN");
reserved_keywords["K"] = Array("KEY", "KILL", "KEYS");
reserved_keywords["L"] = Array("LEFT", "LIKE", "LINENO", "LOAD", "LAST", "LEADING", "LEVEL", "LOCAL", "LOWER", "LIMIT", "LINES", "LOCK", "LONG", "LONGBLOB", "LONGTEXT", "LOW_PRIORITY");
reserved_keywords["M"] = Array("MATCH", "MAX", "MONTH", "MIN", "MINUTE", "MODULE", "MASTER_SERVER_ID", "MEDIUMBLOB", "MEDIUMINT", "MEDIUMTEXT", "MIDDLEINT", "MINUTE_SECOND", "MRG_MYISAM");
reserved_keywords["N"] = Array("NATIONAL", "NOCHECK", "NONCLUSTERED", "NOT", "NULL", "NULLIF", "NAMES", "NATURAL", "NCHAR", "NEXT", "NO", "NONE", "NUMERIC");
reserved_keywords["O"] = Array("OF", "OFF", "OFFSETS", "ON", "OPEN", "OPENDATASOURCE", "OPENROWSET", "OPENXML", "OPTION", "OR", "OPENQUERY", "OVER", "ORDER", "OUTER", "OVERLAPS", "OCTET_LENGTH", "OUTPUT", "ONLY", "OPTIMIZE", "OPTIONALLY", "OUTFILE");
reserved_keywords["P"] = Array("PAD", "PERCENT",  "PLAN", "PRECISION", "PRIMARY", "PRINT", "PROCEDURE", "PROC", "PUBLIC", "PARTIAL", "PASCAL", "POSITION", "PREPARE", "PRESERVE", "PRIOR", "PRIVILEGES", "PURGE"); 
reserved_keywords["Q"] = Array();
reserved_keywords["R"] = Array("RAISERROR", "READ", "READTEXT", "RECONFIGURE", "REFERENCES", "REPLICATION", "RESTORE", "RETURN", "RESTRICT", "REVOKE", "RIGHT", "ROWCOUNT", "ROLLBACK", "ROWGUIDCOL", "RULE", "REAL", "RELATIVE", "ROWS", "REGEXP", "RENAME", "REPLACE", "REQUIRE RETURNS", "RLIKE"); 
reserved_keywords["S"] = Array("SAVE", "SCHEMA", "SCROLL", "SECOND", "SECTION", "SELECT", "SESSION", "SESSION_USER", "SET", "SETUSER",  "SHUTDOWN", "SIZE", "SOME", "STATISTICS", "SYSTEM_USER", "SMALLINT", "SPACE", "SQL", "SQLCA", "SQLCODE", "SQLERROR", "SQLSTATE", "SQLWARNING", "SUBSTRING", "SUM", "SHOW", "SONAME", "SQL_BIG_RESULT", "SQL_CALC_FOUND_ROWS", "SQL_SMALL_RESULT", "SSL", "STARTING", "STRAIGHT_JOIN", "STRIPED"); 
reserved_keywords["T"] = Array("TABLE", "TEXTSIZE", "THEN", "TO", "TOP", "TRAN", "TRANSACTION", "TRIGGER", "TRUNCATE", "TSEQUAL", "TEMPORARY", "TIME", "TIMESTAMP", "TIMEZONE_HOUR", "TIMEZONE_MINUTE", "TRAILING", "TRANSLATE", "TRANSLATION", "TRIM", "TRUE", "TABLES", "TERMINATED", "TINYBLOB", "TINYINT", "TINYTEXT");
reserved_keywords["U"] = Array("UNION", "UNIQUE", "UPDATE", "UPDATETEXT", "USE", "USER", "UNKNOWN", "USAGE", "UPPER", "USING", "USER_RESOURCES", "UNLOCK", "UNSIGNED");
reserved_keywords["V"] = Array("VALUES", "VALUE", "VARBINARY", "VARCHAR", "VARYING", "VIEW");
reserved_keywords["W"] = Array("WAITFOR", "WHEN", "WHERE", "WHILE", "WITH", "WRITETEXT", "WORK", "WRITE", "WHENEVER");
reserved_keywords["X"] = Array("XOR");
reserved_keywords["Y"] = Array("YEAR", "YEAR_MONTH");
reserved_keywords["Z"] = Array("ZONE", "ZEROFILL");

/*	if column name has ' or blank returns false
==============================================*/

function validate_column_name(name, labNotNumber, labFirstChar, labForrbidenChar, labReservedKeywords){
	//ime ne sme biti broj
	if (!isNaN(name)){
		alert(name + labNotNumber);
		return false;
	}

	//mora poceti sa _ ili alfa characterom
	var firstChar = name.charAt(0);
	re = new RegExp( "[_a-zA-Z]", "gi");
	if (!re.test(firstChar)){
		alert(name + labFirstChar);
		return false;
	}

	//nedozvoljeni karakteri
	var forrbidenChars = Array("", "");
	re = new RegExp( "[!@#$%^&*()+={}|\;':,./<>? \"]", "gi");
	if (re.test(name)){
		alert(name + labForrbidenChar);
		return false;
	}

	//validaciju rezervisanih kljuceva
	var retVal = true;

	var tempName = name.toUpperCase();
	var firstChar = tempName.charAt(0);
	for (var i=0; i<reserved_keywords[firstChar].length; i++){
		if (tempName == reserved_keywords[firstChar][i]){
			alert(name + labReservedKeywords);
			retVal = false;
			break;
		}
	}

	return retVal;
}