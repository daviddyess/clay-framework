<?php
namespace Clay;

/**
 * @file Module.php
 * @brief Module Utility Class
 */

\Library('ClayDB/Connection');

/**
 * Module Library
 *
 * @copyright (C) 2012 David L Dyess II
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://clay-project.com
 * @author David L Dyess II (david.dyess@gmail.com)
 * @package Clay Module System
 */

/**
 * Import a Module file
 * @param string $path
 * @return boolean
 */
function Module($path){

	# Try to import(). If we can't we throw an exception
	if(!Import(\clay\MODS_PATH.$path)){

		throw new \Exception('Module at '.\clay\MODS_PATH.$path.' doesn\'t exist!');
	}

	return true;
}

/**
 * @brief Clay Module System
 *
 * @details A module system that acts as a functional layer between Libraries and Applications within the Clay Framework
 * @copyright (C) 2012 David L. Dyess II
 * @author David L. Dyess II (david.dyess@gmail.com)
 * @version 1.0
 * @license GPL
 */
class Module {

	# Store Loaded Modules' Objects
	private static $Module = array();

	/*
	 * self::db() Database Object via Trait
	 */
	use \ClayDB\Connection;

	/**
	 * Private Constructor
	 * @see Module::Instance()
	 */
	private function __constructor(){
		# Singleton Factory
	}

	/**
	 * Import Module Base Class
	 * @param string $module
	 * @throws \Exception
	 */
	protected static function Import($module){

		# Try to import(). If we can't we throw an exception
		if(!\Import(\clay\MODS_PATH.$module)){

			throw new \Exception('Module at '.\clay\MODS_PATH.$module.' doesn\'t exist!');
		}

		return TRUE;
	}

	/**
	 * Initialize a Module Object, optionally Saving the Object Reference (similar to Save()).
	 * @details \Module::Object method supports Singleton objects, as well as an Object Cache for saving non-singleton objects.
	 * @param string $module
	 * @param mixed $args
	 * @param string $name
	 * @return object
	 */
	public static function Object($module,$args=NULL,$name=NULL){

		# Import the Requested Module's base class
		self::Import($module);

		$nsModule = '\Clay\Module\\'.$module;

		if(!\class_exists($nsModule)){

			throw new \Exception("Module: $nsModule Class Does Not Exist.");
		}

		# If the Module is a Singleton, return use its Singleton Method
		if(\method_exists($nsModule,'Instance')) {

			# Return the Singleton Object
			return $nsModule::Instance($args);
		}

		# If Request is for a New Object
		if(empty($name)){

			# Return the Module's New Object
			return new $nsModule($args);
		}

		# If Request was for a Saved Object and it Does Exist
		if(!empty(self::$Module[$module][$name]) && (!empty($name))){

			# Return the Saved Object
			return self::$Module[$module][$name];
		}

		# If Request iss for a Saved object and it Doesn't Exist
		if(empty(self::$Module[$module][$name]) && (!empty($name))){

			# Save the Module's Object for Later Use
			self::$Module[$module][$name] = new $nsModule($args);

			# Return the Saved Object
			return self::$Module[$module][$name];
		}
	}
	/**
	 * Save a Module Object for Later Use
	 * @param string $module
	 * @param object $object
	 * @param string $name
	 * @return NULL
	 */
	public static function Save($module,$object,$name){

		if(!empty(self::$Module[$module][$name])){

			self::$Module[$module][$name] = $object;

		} else {

			throw new \Exception("Module: Object Storage for Module: $module, Using Storage Name: $name Already Exists.");
		}
	}

	/**
	 * Check if an Saved Object Exists
	 * @param string $module
	 * @param string $name
	 */
	public static function Saved($module,$name){

		if(!empty(self::$Module[$module][$name])){

			return TRUE;

		} else {

			return FALSE;
		}
	}

	/**
	 * Call a Static Module Method
	 * @param string $module
	 * @param string $method
	 * @param mixed $args (optional)
	 * @throws \Exception
	 */
	public static function API($module,$method,$args=NULL){

		# Module name as a string (including namespace)
		$nsModule = '\Clay\Module\\'.$module;

		if(!method_exists($nsModule,$method)) {

			self::Import($module);

			if(!method_exists($nsModule,$method)){

				throw new \Exception('Module API Method "'.$nsModule.'::'.$method.'()" could not be found using known file patterns!');
			}
		}
		# Allow the Module to set the default for $args
		if (is_null($args)){
			return $nsModule::$method();
		}

		return $nsModule::$method($args);
	}

	/**
	 * Configuration Data File
	 * @param string $module
	 */
	public static function Config($module){

		# Use the Clay Library's Config method
		return \Clay::Config('sites/'. \clay\CFG_NAME .'/module.'.$module);
	}

	/**
	 * Set Configuration Data in a Data File
	 * @param string $module
	 * @param array $data
	 */
	public static function setConfig($module,$data){

		# Use the Clay Library's setConfig method
		return \Clay::setConfig('sites/'. \clay\CFG_NAME .'/module.'.$module,$data);
	}

	/**
	 * Information Data File
	 * @param string $module
	 */
	public static function Info($module){

		# Modules Contain an Info.php file that is contains information about the current version
		if(file_exists(\clay\MODS_PATH.$module.'/Info.php')){

			include(\clay\MODS_PATH.$module.'/Info.php');

			# Info.php contains an array in a $data variable
			return $data;

		} else {

			# No Info.php file was found
			return FALSE;
		}
	}
	#@FIXME Everything below this point should be put somewhere else. This type of functionality should be in either a module or application.
	#@TODO See FIXME on line 233 - The methods below should reference a handler that points to this type of functionality; ie. database queries.
	/**
	 *
	 * Get Module's System ID (modid)
	 * @param string $name
	 * @return integer
	 */
	public static function getID($name){
		# Get modid where name = $name, 1 row
		$mod = self::db()->get("modid FROM ".\claydb::$tables['modules']." WHERE name = ?", array($name), '0,1');
		return $mod['modid'];
	}

	/**
	 * Get a Module's Namespace (name)
	 * @param integer $id (modid)
	 * @return string
	 */
	public static function getName($id){
		# Get name where modid = $id, 1 row
		$mod = self::db()->get("name FROM ".\claydb::$tables['modules']." WHERE modid = ?", array($id), '0,1');
		# return just the name, string
		return $mod['name'];
	}

	/**
	 * Get a Module's System Version (version)
	 * @param integer (modid) OR string (name) $app
	 * @return string
	 */
	public static function getVersion($mod){
		# $mod can be the modid or the name
		if(is_numeric($mod)){

			$where = " WHERE modid = ?";

		} else {

			$where = " WHERE name = ?";
		}
		# Get version where $mod as applicable, 1 row
		$info = self::db()->get("version FROM ".\claydb::$tables['modules'].$where, array($mod), '0,1');
		# return just the version, string
		return $info['version'];
	}

	/**
	 * Check if a Module is Installed
	 * @param string $module
	 */
	public static function isInstalled($module){

		if(self::getVersion($module)){

			return TRUE;
		}

		return FALSE;
	}
}
