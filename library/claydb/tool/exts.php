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
	function exts($args){
		$dbs = array('MSSQL','MySQL','MySQLi','ODBC','Oracle','PDO MySQL','PDO SQLite','PostgreSQL','SQLite');
		return $dbs;
	}
?>
