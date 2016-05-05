<?php
namespace claydb\tool;
/**
 * Clay Framework
 *
 * @copyright (C) 2007-2010 David L Dyess II
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://clay-project.com
 * @author David L Dyess II (david.dyess@gmail.com)
 */
	function check_exts(){
	// if passed an extension name, we'll see if that extension is available
	/*if(!empty($ext)){
		return extension_loaded($ext) ? true : false;
	}*/
	// build a list of DB extensions supported by ClayDB
	$dbext = array();
	//$dbext['DB++'] = extension_loaded('dbplus') ? true : false;
	//$dbext['dBase'] = extension_loaded('dbase') ? true : false;
	//$dbext['MSSQL'] = extension_loaded('mssql') ? 'mssql' : false;
	//$dbext['MySQL'] = extension_loaded('mysql') ? 'mysql' : false;
	//$dbext['MySQLi'] = extension_loaded('mysqli') ? 'mysqli' : false;
	//$dbext['ODBC'] = extension_loaded('odbc') ? 'odbc' : false;
	//$dbext['Oracle'] = extension_loaded('oci8') ? 'oci8' : false;
	//$dbext['PDO'] = extension_loaded('pdo') ? true : false;
	//$dbext['PDO DBLIB'] = extension_loaded('pdo_dblib') ? true : false;
	//$dbext['PDO Firebird/Interbase'] = extension_loaded('pdo_firebird') ? true : false;
	//$dbext['PDO IBM'] = extension_loaded('pdo_ibm') ? true : false;
	//$dbext['PDO Informix'] = extension_loaded('pdo_informix') ? true : false;
	$dbext['PDO MySQL'] = extension_loaded('pdo_mysql') ? 'pdo_mysql' : false;
	//$dbext['PDO Oracle'] = extension_loaded('pdo_oci') ? true : false;
	//$dbext['PDO ODBC/DB2'] = extension_loaded('pdo_odbc') ? true : false;
	//$dbext['PDO PostgreSQL'] = extension_loaded('pdo_pgsql') ? true : false;
	//$dbext['PDO SQLite'] = extension_loaded('pdo_sqlite') ? 'pdo_sqlite' : false;
	//$dbext['PDO 4D'] = extension_loaded('pdo_4d') ? true : false;
	//$dbext['PostgreSQL'] = extension_loaded('pgsql') ? 'pgsql' : false;
	//$dbext['SQLite'] = extension_loaded('sqlite') ? 'sqlite' : false;
	// return the list
	return $dbext;
	}