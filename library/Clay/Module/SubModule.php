<?php
namespace Clay\Module;
/**
 * Clay Framework
 *
 * @package Clay Modules
 * @subpackage Module API SubModule Class
 *
 * @copyright (C) 2014 David L Dyess II
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://clay-project.com
 * @author David L Dyess II (david.dyess@gmail.com)
 */

/**
 * Module API SubModule Library
 * Allows a module to have submodule APIs.
 * Provides an interface to object methods in module subclasses.
 * Reduces overhead by limiting the code base required to operate - submodules are loaded on-demand
 * 
 * NOTE: SubModule's can be used by modules implemented using static class methods, but the SubModule requested
 * 		 will have to be an object. For static submodule classes, use the Module API class.
 */
trait SubModule {
	/**
	 * Submodule Object API Handler
	 * @param string $class
	 * @param mixed $args (optional)
	 */
	public function Object($class, $args = NULL){
		# Module Name
		static $module;
		# Parent class namespace		
		static $ns;
		# SubModule object cache
		static $objects = array();
		# On the first run, we cache all of the module class info
		if(empty($module)){
			# Using Module class name with namespace (not trait name)
			$ns = get_called_class();
			$modulens = explode('\\', $ns);
			# Module class name without namespace
			$module = end($modulens);
		}
		# Module namespace plus imported class (api)
		$obj = $ns.'\\'.$class;
		# Check for a cached Object
		if(!empty($objects[$obj])){
			# Return the saved Object
			return $objects[$obj];
		}
		# Import module subclass file
		\Clay\Module($module.'/'.$class);
		# Initialize the new class object
		$objects[$obj] = new $obj($args);
		# Return the new/saved Object
		return $objects[$obj];
	}
}