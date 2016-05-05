<?php
namespace Clay\Module;
/**
 * Clay Framework
 *
 * @package Clay Modules
 * @subpackage Module API Base Class
 *
 * @copyright (C) 2012-2014 David L Dyess II
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://clay-project.com
 * @author David L Dyess II (david.dyess@gmail.com)
 */

/**
 * Module API Library
 * Allows a module to have submodule APIs, see Service module for an example.
 * Provides an interface to static methods in module classes.
 * @author David
 */
trait API {
	/**
	 * Submodule Static API Handler
	 * @param string $class
	 * @param string $method
	 * @param mixed $args
	 */
	public static function API($class,$method,$args){
		# Parent Module Name
		static $module;
		# Parent Class Namespace
		static $ns;
		# On first run, we cache all of the module class info
		if(empty($module)){
			# Using Module class name with namespace (not trait name)
			$ns = get_called_class();
			$modulens = explode('\\', $ns);
			# Module class name without namespace
			$module = end($modulens);
		}
		# Import class file called via this static method
		\Clay\Module($module.'/'.$class);
		# Module namespace plus imported class (api)
		$api = $ns.'\\'.$class;
		# Return SubModule API class and method
		return $api::$method($args);
	}
}