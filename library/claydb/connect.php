<?php
namespace claydb;
/**
 * Clay Framework
 *
 * @copyright (C) 2007-2012 David L Dyess II
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://clay-project.com
 * @author David L Dyess II (david.dyess@gmail.com)
 * @package ClayDB
 */

/**
 * Provides a base abstraction class for carrying forward a static database resource.
 */

abstract class connection {

	public static function db(){
		static $link;
		if(!empty($link)) { goto end; }

		\library('claydb');
		$link = \claydb::connect();
		\claydb::tables();

		end:
		return $link;
	}

}