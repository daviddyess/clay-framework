<?php
namespace ClayDB;

\Library('ClayDB');

/**
 * Clay Framework
 *
 * @copyright (C) 2012 David L Dyess II
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://clay-project.com
 * @author David L Dyess II (david.dyess@gmail.com)
 * @package ClayDB
 */

/**
 * Trait for ClayDB connection resource
 */
trait Connection {
	
	/**
	 * Database Connection
	 * @return object ClayDB Resource
	 */
	protected static function db($db = NULL){
		
		return \claydb::connect($db);
	}
}