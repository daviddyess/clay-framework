<?php
namespace Clay\abstracts;
/**
 * Clay Framework
 *
 * @copyright (C) 2007-2010 David L Dyess II
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://clay-project.com
 * @author David L Dyess II (david.dyess@gmail.com)
 * @package Singleton Abstract Class
 */

/**
 * Provides a base abstraction class for implementing the Singleton Design Pattern
 */

abstract class singleton {
	# Scope is restricted
	private function construct(){}
	# Used to instantiate and pass objects
	public static function getInstance(){
		static $instance; # Class object storage
		$class = get_called_class(); # Child class being instantiated
		# If the child class hasn't been instantiated
		if(!isset($instance[$class])) {
			# Instantiate the class and add it to the storage array
			$instance[$class] = new $class;
		}
		# Return the stored object
		return $instance[$class];
	}
}