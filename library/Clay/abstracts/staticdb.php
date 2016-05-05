<?php
namespace Clay\abstracts;
/**
 * Clay Framework
 *
 * @copyright (C) 2007-2010 David L Dyess II
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://clay-project.com
 * @author David L Dyess II (david.dyess@gmail.com)
 * @package Database Resource Abstract Class
 */

/**
 * Provides a base abstraction class for carrying forward a static database resource.
 */

abstract class staticdb {

	public static function db(){
		static $link;
		if(!empty($link)) { goto end; }

		\Library('ClayDB');
		$link = \claydb::connect();
		\claydb::tables();

		end:
		return $link;
	}

}