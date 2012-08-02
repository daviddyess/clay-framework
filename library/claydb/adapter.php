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
 * ClayDB Adapter Interface.
 * An interface for creating database adapter classes in ClayDB
 * @author David
 *
 */
interface AdapterInterface {
	
	public function connect($driver, $host, $database, $user, $pw);
	
	public function get($sql,$bind=array(),$limit='');
	
	public function add($sql,$bind=array());
	
	public function update($sql,$bind=array(),$limit='');
	
	public function delete($sql,$bind=array(),$limit='');
	
	public function change($action,$sql,$bind=array(),$limit='');
	
	public function selectDB($database);
	
	public function datadict();
}

/**
 * ClayDB Adapter Abstract Class.
 * @see \claydb\AdapterInterface
 * @author David
 */
abstract class adapter implements AdapterInterface {
	
	abstract public function connect($driver, $host, $database, $user, $pw);
	
	abstract public function get($sql,$bind=array(),$limit='');
	
	abstract public function add($sql,$bind=array());
	
	abstract public function update($sql,$bind=array(),$limit='');
	
	abstract public function delete($sql,$bind=array(),$limit='');
	
	abstract public function change($action,$sql,$bind=array(),$limit='');
	
	abstract public function selectDB($database);
	
	abstract public function datadict();	
}