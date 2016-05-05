<?php
namespace claydb;
/**
* ClayDB
*
* @copyright (C) 2007-2012 David L Dyess II
* @license GPL {@link http://www.gnu.org/licenses/gpl.html}
* @link http://clay-project.com
* @author David L Dyess II (david.dyess@gmail.com)
* @since 2012-07-01
*/

/**
 * ClayDB Data Dictionary Interface.
 * An interface for creating Data Dictionary classes in ClayDB
 * @author David L Dyess II
 * @TODO See abstract below
 */
interface DataDictionaryInterface {
	
	public function __construct($arg);
	
	public function createTable($table,$args);
	
	public function alterTable($table,$args);
	
	public function createIndex($table,$args);
	
	public function dropIndex($table,$args);
	
	public function dropTable($table);
	
	public function dataType($args);
	
	public function registerTables($tables);
	
	public function unregisterTables($tables);
	
}

/**
 * ClayDB Data Dictionary Abstract Class
 * @see \claydb\DataDictionaryInterface
 * @author David L Dyess
 * @TODO Determine methods that can be moved out of individual datadicts and implemented here
 * @TODO Add dropTables() method
 * @TODO Add parameter to optionally unregister tables to dropTable() and dropTables()
 */
abstract class datadict implements DataDictionaryInterface {
	
	abstract public function __construct($arg);
	
	abstract public function createTable($table,$args);
	
	abstract public function alterTable($table,$args);
	
	abstract public function createIndex($table,$args);
	
	abstract public function dropIndex($table,$args);

	abstract public function dropTable($table);
	
	abstract public function dataType($args);
	
	/**
	 * Register Tables.
	 * Add database tables to the database.tables configuration file
	 */
	public function registerTables($tables){
		
		$path = !empty(\claydb::$cfg) ? 'sites/'.\claydb::$cfg.'/database.tables' : 'sites/'.\clay\CFG_NAME.'/database.tables';
		$storedTables = \clay::config($path);
		switch(true) {
			case !is_array($tables):
				return false;
			case !is_array($storedTables) AND is_array($tables):
				$tableData = $tables;
				break;
			case is_array($storedTables) AND is_array($tables):
				$tableData = array_merge($storedTables,$tables);
				break;
		}
		\clay::setConfig($path,$tableData);
		return true;
	}
	/**
	 * Remove database tables from the database.tables configuration file
	 */
	public function unregisterTables($tables){
		$path = !empty(\claydb::$cfg) ? 'sites/'.\claydb::$cfg.'/database.tables' : 'sites/'.\clay\CFG_NAME.'/database.tables';
		$storedTables = \clay::config($path);
		foreach($tables as $table){
			if(!empty($storedTables[$table])){
				unset($storedTables[$table]);
			}
		}
		\clay::setConfig($path,$tableData);
		return true;
	}
}