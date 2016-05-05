<?php
namespace Clay\Application;
/**
 * Object Test
 * @since 2012-03-22
 */

class Object2 {
	/**
	 * 
	 * Build a dynamic object class for using a global object
	 * @param string $app - namespace of an application
	 */
	public function __construct($app){
		
	}
	/**
	 * 
	 * Static cache of objects
	 * @param string $id
	 */
	private static function cache($id){
		static $cache = array();
		
	}
}